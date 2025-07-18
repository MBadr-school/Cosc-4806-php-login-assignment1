<?php

class Movie {
    private $db;

    public function __construct() {
        $this->db = db_connect();
    }

    /**
     * Add or update a movie rating
     * @param string $movieId IMDB ID
     * @param string $userIp User's IP address
     * @param int $rating Rating value (1-5)
     * @return bool Success status
     */
    public function addRating($movieId, $userIp, $rating) {
        try {
            // Validate rating
            if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
                return false;
            }

            // Check rate limiting
            if (!$this->checkRateLimit($userIp, 'rating')) {
                return false;
            }

            $stmt = $this->db->prepare("
                INSERT INTO ratings (movie_id, user_ip, rating_value) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                rating_value = VALUES(rating_value)
            ");

            $result = $stmt->execute([$movieId, $userIp, $rating]);

            if ($result) {
                $this->updateRateLimit($userIp, 'rating');
            }

            return $result;
        } catch (Exception $e) {
            error_log('Rating error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's rating for a movie
     * @param string $movieId IMDB ID
     * @param string $userIp User's IP address
     * @return int|false User's rating or false if not found
     */
    public function getUserRating($movieId, $userIp) {
        try {
            $stmt = $this->db->prepare("
                SELECT rating_value 
                FROM ratings 
                WHERE movie_id = ? AND user_ip = ?
            ");

            $stmt->execute([$movieId, $userIp]);

            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log('Get rating error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get movie rating statistics
     * @param string $movieId IMDB ID
     * @return array|false Rating statistics or false on failure
     */
    public function getMovieRatingStats($movieId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_ratings,
                    AVG(rating_value) as average_rating,
                    MIN(rating_value) as min_rating,
                    MAX(rating_value) as max_rating
                FROM ratings 
                WHERE movie_id = ?
            ");

            $stmt->execute([$movieId]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['total_ratings'] > 0) {
                $result['average_rating'] = round($result['average_rating'], 1);
                return $result;
            }

            return [
                'total_ratings' => 0,
                'average_rating' => 0,
                'min_rating' => 0,
                'max_rating' => 0
            ];
        } catch (Exception $e) {
            error_log('Get stats error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get rating distribution for a movie
     * @param string $movieId IMDB ID
     * @return array Rating distribution (1-5 stars count)
     */
    public function getRatingDistribution($movieId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    rating_value,
                    COUNT(*) as count
                FROM ratings 
                WHERE movie_id = ?
                GROUP BY rating_value
                ORDER BY rating_value DESC
            ");

            $stmt->execute([$movieId]);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Initialize distribution array
            $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

            foreach ($results as $row) {
                $distribution[$row['rating_value']] = $row['count'];
            }

            return $distribution;
        } catch (Exception $e) {
            error_log('Get distribution error: ' . $e->getMessage());
            return [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        }
    }

    /**
     * Check rate limiting for user
     * @param string $userIp User's IP address
     * @param string $endpoint Endpoint name
     * @return bool Whether request is allowed
     */
    private function checkRateLimit($userIp, $endpoint) {
        try {
            $windowMinutes = 60; // 1 hour window
            $maxRequests = 100; // Max requests per hour

            if ($endpoint === 'rating') {
                $maxRequests = 50; // Lower limit for ratings
            } elseif ($endpoint === 'review') {
                $maxRequests = 20; // Lower limit for AI reviews
            }

            // Clean old entries
            $stmt = $this->db->prepare("
                DELETE FROM rate_limiting 
                WHERE window_start < DATE_SUB(NOW(), INTERVAL ? MINUTE)
            ");
            $stmt->execute([$windowMinutes]);

            // Check current rate
            $stmt = $this->db->prepare("
                SELECT request_count 
                FROM rate_limiting 
                WHERE user_ip = ? AND endpoint = ? AND window_start > DATE_SUB(NOW(), INTERVAL ? MINUTE)
            ");

            $stmt->execute([$userIp, $endpoint, $windowMinutes]);
            $currentCount = $stmt->fetchColumn();

            return $currentCount === false || $currentCount < $maxRequests;
        } catch (Exception $e) {
            error_log('Rate limit check error: ' . $e->getMessage());
            return true; // Allow on error
        }
    }

    /**
     * Update rate limiting counter
     * @param string $userIp User's IP address
     * @param string $endpoint Endpoint name
     */
    private function updateRateLimit($userIp, $endpoint) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO rate_limiting (user_ip, endpoint, request_count, window_start) 
                VALUES (?, ?, 1, NOW()) 
                ON DUPLICATE KEY UPDATE 
                request_count = request_count + 1
            ");

            $stmt->execute([$userIp, $endpoint]);
        } catch (Exception $e) {
            error_log('Rate limit update error: ' . $e->getMessage());
        }
    }

    /**
     * Check if user can make a review request
     * @param string $userIp User's IP address
     * @return bool Whether review request is allowed
     */
    public function canRequestReview($userIp) {
        return $this->checkRateLimit($userIp, 'review');
    }

    /**
     * Update review request rate limit
     * @param string $userIp User's IP address
     */
    public function updateReviewRateLimit($userIp) {
        $this->updateRateLimit($userIp, 'review');
    }

    /**
     * Get popular movies (most rated)
     * @param int $limit Number of movies to return
     * @return array Popular movies
     */
    public function getPopularMovies($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    movie_id,
                    COUNT(*) as total_ratings,
                    AVG(rating_value) as average_rating
                FROM ratings 
                GROUP BY movie_id
                ORDER BY total_ratings DESC, average_rating DESC
                LIMIT ?
            ");

            $stmt->execute([$limit]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Get popular movies error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recently rated movies
     * @param int $limit Number of movies to return
     * @return array Recently rated movies
     */
    public function getRecentlyRatedMovies($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    movie_id,
                    MAX(created_at) as last_rated,
                    COUNT(*) as total_ratings,
                    AVG(rating_value) as average_rating
                FROM ratings 
                GROUP BY movie_id
                ORDER BY last_rated DESC
                LIMIT ?
            ");

            $stmt->execute([$limit]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Get recent movies error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user's IP address
     * @return string User's IP address
     */
    public static function getUserIp() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);

                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }
} 