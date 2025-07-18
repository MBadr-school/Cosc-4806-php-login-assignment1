<?php
// Direct access file for movie application - use this if .htaccess isn't working on Replit
// Access via: yoursite.com/movie.php

require_once 'app/init.php';

// Force the controller to be 'movie' and method to be 'index'
$_SERVER['REQUEST_URI'] = '/movie/index';

$app = new App;
?> 