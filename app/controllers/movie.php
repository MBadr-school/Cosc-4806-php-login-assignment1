<?php

require_once 'app/services/OmdbService.php';
require_once 'app/services/GeminiService.php';
require_once 'app/models/movie.php';

class MovieController extends Controller {
    private $omdbService;
    private $geminiService;
    private $movieModel;

    public function __construct() {
        $this->omdbService = new OmdbService();
        $this->geminiService = new GeminiService();
        $this->movieModel = new MovieModel();
    }

    /**
     * Default movie search page
     */
    public function index() {
        $data = [
            'page_title' => 'Movie Search & Reviews',
            'popular_movies' => $this->movieModel->getPopularMovies(5),
            'recent_movies' => $this->movieModel->getRecentlyRatedMovies(5)
        ];

        $this->view('movie/index', $data);
    }

    /**
     * Search movies via AJAX
     */
    public function search() {
        header('Content-Type: application/json');

        if (!isset($_POST['query'])) {
            echo json_encode(['error' => 'Search query is required']);
            return;
        }

        $query = $this->omdbService->sanitizeSearchInput($_POST['query']);

        if (!$query) {
            echo json_encode(['error' => 'Invalid search query']);
            return;
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

        try {
            $results = $this->omdbService->searchMovies($query, $page);

            if ($results) {
                // Add rating data to each movie
                if (isset($results['Search'])) {
                    foreach ($results['Search'] as &$movie) {
                        $movieStats = $this->movieModel->getMovieRatingStats($movie['imdbID']);
                        $movie['rating_stats'] = $movieStats;
                        $movie['user_rating'] = $this->movieModel->getUserRating($movie['imdbID'], MovieModel::getUserIp());
                    }
                }

                echo json_encode($results);
            } else {
                echo json_encode(['error' => 'No movies found']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Search failed. Please try again.']);
        }
    }

    /**
     * Get movie details via AJAX
     */
    public function details() {
        header('Content-Type: application/json');

        if (!isset($_POST['imdb_id'])) {
            echo json_encode(['error' => 'Movie ID is required']);
            return;
        }

        $imdbId = $_POST['imdb_id'];

        try {
            $movie = $this->omdbService->getMovieDetails($imdbId);

            if ($movie) {
                // Add rating data
                $movie['rating_stats'] = $this->movieModel->getMovieRatingStats($imdbId);
                $movie['rating_distribution'] = $this->movieModel->getRatingDistribution($imdbId);
                $movie['user_rating'] = $this->movieModel->getUserRating($imdbId, MovieModel::getUserIp());

                echo json_encode($movie);
            } else {
                echo json_encode(['error' => 'Movie not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Failed to load movie details']);
        }
    }

    /**
     * Add or update movie rating via AJAX
     */
    public function rate() {
        header('Content-Type: application/json');

        if (!isset($_POST['imdb_id']) || !isset($_POST['rating'])) {
            echo json_encode(['error' => 'Movie ID and rating are required']);
            return;
        }

        $imdbId = $_POST['imdb_id'];
        $rating = intval($_POST['rating']);
        $userIp = MovieModel::getUserIp();

        if ($rating < 1 || $rating > 5) {
            echo json_encode(['error' => 'Rating must be between 1 and 5']);
            return;
        }

        try {
            $success = $this->movieModel->addRating($imdbId, $userIp, $rating);

            if ($success) {
                // Return updated rating stats
                $stats = $this->movieModel->getMovieRatingStats($imdbId);
                $distribution = $this->movieModel->getRatingDistribution($imdbId);

                echo json_encode([
                    'success' => true,
                    'message' => 'Rating saved successfully',
                    'rating_stats' => $stats,
                    'rating_distribution' => $distribution,
                    'user_rating' => $rating
                ]);
            } else {
                echo json_encode(['error' => 'Failed to save rating. You may have exceeded the rate limit.']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Rating failed. Please try again.']);
        }
    }

    /**
     * Generate AI review via AJAX
     */
    public function review() {
        header('Content-Type: application/json');

        if (!isset($_POST['imdb_id'])) {
            echo json_encode(['error' => 'Movie ID is required']);
            return;
        }

        $imdbId = $_POST['imdb_id'];
        $userIp = MovieModel::getUserIp();

        // Check rate limiting
        if (!$this->movieModel->canRequestReview($userIp)) {
            echo json_encode(['error' => 'Too many review requests. Please wait before requesting another review.']);
            return;
        }

        try {
            // Get movie details first
            $movie = $this->omdbService->getMovieDetails($imdbId);

            if (!$movie) {
                echo json_encode(['error' => 'Movie not found']);
                return;
            }

            // Generate or get cached review
            $review = $this->geminiService->getOrGenerateReview($movie);

            if ($review) {
                // Update rate limit
                $this->movieModel->updateReviewRateLimit($userIp);

                echo json_encode([
                    'success' => true,
                    'review' => $review,
                    'movie_title' => $movie['Title']
                ]);
            } else {
                echo json_encode(['error' => 'Failed to generate review. Please try again later.']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Review generation failed. Please try again.']);
        }
    }

    /**
     * Get popular movies
     */
    public function popular() {
        header('Content-Type: application/json');

        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $limit = min($limit, 50); // Maximum 50 movies

        try {
            $movies = $this->movieModel->getPopularMovies($limit);

            // Get movie details for each popular movie
            $detailedMovies = [];
            foreach ($movies as $movie) {
                $details = $this->omdbService->getMovieDetails($movie['movie_id']);
                if ($details) {
                    $details['rating_stats'] = [
                        'total_ratings' => $movie['total_ratings'],
                        'average_rating' => round($movie['average_rating'], 1)
                    ];
                    $detailedMovies[] = $details;
                }
            }

            echo json_encode([
                'success' => true,
                'movies' => $detailedMovies
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Failed to load popular movies']);
        }
    }

    /**
     * Get recently rated movies
     */
    public function recent() {
        header('Content-Type: application/json');

        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $limit = min($limit, 50); // Maximum 50 movies

        try {
            $movies = $this->movieModel->getRecentlyRatedMovies($limit);

            // Get movie details for each recent movie
            $detailedMovies = [];
            foreach ($movies as $movie) {
                $details = $this->omdbService->getMovieDetails($movie['movie_id']);
                if ($details) {
                    $details['rating_stats'] = [
                        'total_ratings' => $movie['total_ratings'],
                        'average_rating' => round($movie['average_rating'], 1)
                    ];
                    $details['last_rated'] = $movie['last_rated'];
                    $detailedMovies[] = $details;
                }
            }

            echo json_encode([
                'success' => true,
                'movies' => $detailedMovies
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Failed to load recent movies']);
        }
    }

    /**
     * Health check endpoint
     */
    public function health() {
        header('Content-Type: application/json');

        $health = [
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'services' => [
                'database' => 'ok',
                'omdb_api' => 'ok',
                'gemini_api' => 'ok'
            ]
        ];

        // Test database connection
        try {
            $this->movieModel->getPopularMovies(1);
        } catch (Exception $e) {
            $health['services']['database'] = 'error';
            $health['status'] = 'degraded';
        }

        echo json_encode($health);
    }
} 