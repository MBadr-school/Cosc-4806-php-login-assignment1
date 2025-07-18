<?php

error_reporting(0);
ini_set('display_errors', 0);
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
$sessionCookieExpireTime = 28800; // 8hrs
session_set_cookie_params($sessionCookieExpireTime);
session_start();

require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/config.php';
require_once 'app/database.php';

// Include movie application services and models
require_once 'app/services/OmdbService.php';
require_once 'app/services/GeminiService.php';
require_once 'app/models/movie.php';

