<?php

class HomeController extends Controller {

    public function index() {
      $user = $this->model('User');
      $data = $user->test();

      $this->view('home/index');
      die;
    }


}
