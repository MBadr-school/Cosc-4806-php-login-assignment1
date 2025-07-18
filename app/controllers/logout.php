<?php

class LogoutController extends Controller {

    public function index() {		
        session_start();
        $_SESSION = array();
        session_destroy();
        header('location:/movie');
    }
}