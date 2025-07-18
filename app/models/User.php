<?php

class User {

    public $username;
    public $password;
    public $auth = false;

    public function __construct() {

    }

    public function test () {
      $db = db_connect();
      $statement = $db->prepare("select * from users;");
      $statement->execute();
      $rows = $statement->fetch(PDO::FETCH_ASSOC);
      return $rows;
    }

    public function authenticate($username, $password) {
        /*
         * if username and password good then
         * $this->auth = true;
         */
    $username = strtolower($username);
    $db = db_connect();
        $statement = $db->prepare("select * from users WHERE username = :name;");
        $statement->bindValue(':name', $username);
        $statement->execute();
        $rows = $statement->fetch(PDO::FETCH_ASSOC);

    if (password_verify($password, $rows['password'])) {
      $_SESSION['auth'] = 1;
      $_SESSION['username'] = ucwords($username);
      unset($_SESSION['failedAuth']);
      header('Location: /home');
      die;
    } else {
      if(isset($_SESSION['failedAuth'])) {
        $_SESSION['failedAuth'] ++; //increment
      } else {
        $_SESSION['failedAuth'] = 1;
      }
      header('Location: /movie');
      die;
    }
    }

    /**
     * Check if a user already exists with the given username or email
     * @param string $username The username to check
     * @param string $email The email to check
     * @return bool True if user exists, false otherwise
     */
    public function userExists($username, $email) {
        $username = strtolower($username);
        $email = strtolower($email);

        $db = db_connect();
        $statement = $db->prepare("SELECT COUNT(*) FROM users WHERE LOWER(username) = :username OR LOWER(email) = :email");
        $statement->bindValue(':username', $username);
        $statement->bindValue(':email', $email);
        $statement->execute();
        $count = $statement->fetchColumn();

        return $count > 0;
    }

    /**
     * Create a new user account
     * @param string $username The username
     * @param string $email The email address
     * @param string $password The plain text password
     * @return bool True if user was created successfully, false otherwise
     */
    public function createUser($username, $email, $password) {
        try {
            $username = strtolower($username);
            $email = strtolower($email);

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $db = db_connect();
            $statement = $db->prepare("INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())");
            $statement->bindValue(':username', $username);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':password', $hashedPassword);

            return $statement->execute();
        } catch (PDOException $e) {
            error_log('User creation error: ' . $e->getMessage());
            return false;
        }
    }

}
