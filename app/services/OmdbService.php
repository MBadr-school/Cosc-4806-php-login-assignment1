<?php

class OmdbService {
    private $apiKey;
    private $baseUrl = 'http://www.omdbapi.com/';

    public function __construct() {
        $this->apiKey = OMDB_API_KEY;
    }

    /**
     * Search movies by title
     * @param string $title Movie title to search
     * @param int $page Page number (default 1)
     * @return array|false API response or false on failure
     */
    public function searchMovies($title, $page = 1) {
        $params = [
            'apikey' => $this->apiKey,
            's' => trim($title),
            'page' => $page,
            'type' => 'movie'
        ];

        $url = $this->baseUrl . '?' . http_build_query($params);

        try {
            $response = $this->makeRequest($url);

            if ($response && isset($response['Response']) && $response['Response'] === 'True') {
                return $response;
            }

            return false;
        } catch (Exception $e) {
            error_log('OMDB API Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get movie details by IMDB ID
     * @param string $imdbId IMDB ID of the movie
     * @return array|false Movie details or false on failure
     */
    public function getMovieDetails($imdbId) {
        $params = [
            'apikey' => $this->apiKey,
            'i' => $imdbId,
            'plot' => 'full'
        ];

        $url = $this->baseUrl . '?' . http_build_query($params);

        try {
            $response = $this->makeRequest($url);

            if ($response && isset($response['Response']) && $response['Response'] === 'True') {
                return $response;
            }

            return false;
        } catch (Exception $e) {
            error_log('OMDB API Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get movie details by title and year
     * @param string $title Movie title
     * @param string $year Movie year (optional)
     * @return array|false Movie details or false on failure
     */
    public function getMovieByTitle($title, $year = null) {
        $params = [
            'apikey' => $this->apiKey,
            't' => trim($title),
            'plot' => 'full'
        ];

        if ($year) {
            $params['y'] = $year;
        }

        $url = $this->baseUrl . '?' . http_build_query($params);

        try {
            $response = $this->makeRequest($url);

            if ($response && isset($response['Response']) && $response['Response'] === 'True') {
                return $response;
            }

            return false;
        } catch (Exception $e) {
            error_log('OMDB API Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Make HTTP request to OMDB API
     * @param string $url Request URL
     * @return array|false Decoded JSON response or false on failure
     */
    private function makeRequest($url) {
        // Initialize cURL
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Movie Search App/1.0'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception('HTTP Error: ' . $httpCode);
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON Decode Error: ' . json_last_error_msg());
        }

        return $decodedResponse;
    }

    /**
     * Validate and sanitize search input
     * @param string $input Search input
     * @return string|false Sanitized input or false if invalid
     */
    public function sanitizeSearchInput($input) {
        $input = trim($input);

        if (empty($input) || strlen($input) < 2) {
            return false;
        }

        // Remove potentially harmful characters
        $input = preg_replace('/[<>"\']/', '', $input);

        return $input;
    }
} 