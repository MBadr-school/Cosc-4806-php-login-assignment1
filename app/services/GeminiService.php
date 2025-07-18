<?php

class GeminiService {
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct() {
        $this->apiKey = GEMINI_API_KEY;
    }

    /**
     * Generate AI review for a movie
     * @param array $movieData Movie data from OMDB API
     * @return string|false Generated review or false on failure
     */
    public function generateMovieReview($movieData) {
        try {
            // Validate movie data
            if (!isset($movieData['Title']) || !isset($movieData['Plot'])) {
                return false;
            }

            $prompt = $this->buildReviewPrompt($movieData);

            $response = $this->makeRequest($prompt);

            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                return $response['candidates'][0]['content']['parts'][0]['text'];
            }

            return false;
        } catch (Exception $e) {
            error_log('Gemini API Error: ' . $e->getMessage());
            return 'Gemini error: ' . $e->getMessage();
        }
    }

    /**
     * Build review prompt for Gemini API
     * @param array $movieData Movie data from OMDB
     * @return string Formatted prompt
     */
    private function buildReviewPrompt($movieData) {
        $title = $movieData['Title'];
        $year = $movieData['Year'] ?? 'Unknown';
        $genre = $movieData['Genre'] ?? 'Unknown';
        $plot = $movieData['Plot'] ?? 'No plot available';
        $director = $movieData['Director'] ?? 'Unknown';
        $actors = $movieData['Actors'] ?? 'Unknown';
        $imdbRating = $movieData['imdbRating'] ?? 'N/A';

        $prompt = "Write a thoughtful and engaging movie review for the following film:

Title: {$title}
Year: {$year}
Genre: {$genre}
Director: {$director}
Main Actors: {$actors}
IMDB Rating: {$imdbRating}
Plot: {$plot}

Please provide a comprehensive review that:
1. Analyzes the film's strengths and weaknesses
2. Discusses the plot, characters, and themes
3. Evaluates the direction, acting, and technical aspects
4. Considers the film within its genre and time period
5. Provides a balanced perspective for potential viewers
6. Keeps the review engaging and informative (around 200-300 words)

Write in a conversational yet professional tone that would help someone decide whether to watch this movie.";

        return $prompt;
    }

    /**
     * Make HTTP request to Gemini API
     * @param string $prompt Review prompt
     * @return array|false API response or false on failure
     */
    private function makeRequest($prompt) {
        $url = $this->baseUrl . '?key=' . $this->apiKey;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: Movie Search App/1.0'
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        file_put_contents(__DIR__ . '/gemini_debug.log', $response . PHP_EOL, FILE_APPEND);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            error_log('Gemini API Error Response: ' . $response);
            throw new Exception('HTTP Error: ' . $httpCode . ' - ' . $response);
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Gemini API JSON Error: ' . json_last_error_msg() . ' - Response: ' . $response);
            throw new Exception('JSON Decode Error: ' . json_last_error_msg());
        }

        if (!isset($decodedResponse['candidates'][0]['content']['parts'][0]['text'])) {
            error_log('Unexpected Gemini API Response Structure: ' . json_encode($decodedResponse));
            throw new Exception('Unexpected API response structure');
        }

        return $decodedResponse;
    }

    /**
     * Get cached review or generate new one
     * @param array $movieData Movie data from OMDB
     * @return string|false Review text or false on failure
     */
    public function getOrGenerateReview($movieData) {
        if (!isset($movieData['imdbID'])) {
            return false;
        }

        try {
            $dbh = db_connect();

            // Check for cached review
            $stmt = $dbh->prepare("SELECT review_text FROM cached_reviews WHERE movie_id = ?");
            $stmt->execute([$movieData['imdbID']]);
            $cachedReview = $stmt->fetchColumn();

            if ($cachedReview) {
                return $cachedReview;
            }

            // Generate new review
            $review = $this->generateMovieReview($movieData);

            if ($review && strpos($review, 'Gemini error:') !== 0) {
                // Cache the review
                $stmt = $dbh->prepare("INSERT INTO cached_reviews (movie_id, movie_title, review_text) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE review_text = VALUES(review_text)");
                $stmt->execute([$movieData['imdbID'], $movieData['Title'], $review]);

                return $review;
            }

            return $review; // Return the error message if it's an error
        } catch (Exception $e) {
            error_log('Review caching error: ' . $e->getMessage());
            return false;
        }
    }
} 