<?php

define('VERSION', '0.7.0');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));
define('APPS', ROOT . DS . 'app');
define('CORE', ROOT . DS . 'core');
define('LIBS', ROOT . DS . 'lib');
define('MODELS', ROOT . DS . 'models');
define('VIEWS', ROOT . DS . 'views');
define('CONTROLLERS', ROOT . DS . 'controllers');
define('LOGS', ROOT . DS . 'logs');	
define('FILES', ROOT . DS. 'files');

// ---------------------  MOVIE DATABASE CONFIGURATION -------------------------
define('DB_HOST',         'hyek7.h.filess.io');
define('DB_USER',         'COSC4806_anywherein'); 
define('DB_PASS',         'daeeaf1f468745cc29a9a0464041eeb4f389855b');
define('DB_DATABASE',     'COSC4806_anywherein');
define('DB_PORT',         '3305');

// API Keys
define('OMDB_API_KEY',    '5be702cb');
define('GEMINI_API_KEY',  'AIzaSyDokyitV6iiy9hqiQCX2L6qLhd5Ji72-vU');