<?php

namespace App\Models;

use App\Models\SqlConnect;
use App\Utils\{HttpException, JWT};
use \PDO;

/**
 * Class AuthModel
 * 
 * This model handles authentication-related logic such as user registration and token validity.
*/
class AuthModel extends SqlConnect {
  private string $table = "users";
  private int $tokenValidity;
  private string $passwordSalt = "sqidq7sà";
    
  
  //Define a constant for the default token validity.
  private const DEFAULT_TOKEN_VALIDITY = 3600;
  
  /**
   * AuthModel constructor.
   * 
   * Initializes the token validity from environment variables or uses the default value.
  */
  public function __construct() {
    parent::__construct();
    $this->tokenValidity = (int)(getenv('JWT_VALIDITY') ?: self::DEFAULT_TOKEN_VALIDITY);
  }
  
  /**
   * Get the token validity period.
   * 
   * @return int The token validity period in seconds.
  */
  private function getTokenValidity(): int {
    return $this->tokenValidity;
  }

  /**
   * Register a new user.
   * 
   * @param array $data The user data including email and password.
   * @throws HttpException If the user already exists.
  */
  public function register(array $data) {
    $query = "SELECT email FROM $this->table WHERE email = :email";
    $req = $this->db->prepare($query);
    $req->execute(["email" => $data["email"]]);
    
    if ($req->rowCount() > 0) {
      throw new HttpException("User already exists!", 400);
    }

    // Combine password with salt and hash it
    $saltedPassword = $data["password"] . $this->passwordSalt;
    $hashedPassword = password_hash($saltedPassword, PASSWORD_BCRYPT);

    // Create the user
    $query_add = "INSERT INTO $this->table (email, password) VALUES (:email, :password)";
    $req2 = $this->db->prepare($query_add);
    $req2->execute([
      "email" => $data["email"],
      "password" => $hashedPassword
    ]);

    $userId = $this->db->lastInsertId();

    // Generate the JWT token
    $token = $this->generateJWT($userId);

    return ['token' => $token];
  }

  /**
   * Log in a user.
   * 
   * @param string $email The user's email.
   * @param string $password The user's password.
   * @return array The generated JWT token.
   * @throws \Exception If the credentials are invalid.
  */
  public function login($email, $password) {
    $query = "SELECT * FROM $this->table WHERE email = :email";
    $req = $this->db->prepare($query);
    $req->execute(['email' => $email]);

    $user = $req->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      // Combine input password with salt and verify
      $saltedPassword = $password . $this->passwordSalt;
        
      if (password_verify($saltedPassword, $user['password'])) {
        $token = $this->generateJWT($user['id']);
        return ['token' => $token];
      }
    }

    throw new \Exception("Invalid credentials.");
  }

  /**
   * Refresh the JWT token.
   * 
   * @param string $refreshToken The refresh token.
   * @return string The new JWT token.
   * @throws HttpException If the refresh token is invalid.
  */
  public function refreshToken($refreshToken) {
    $payload = JWT::decode($refreshToken);
    if (!$payload) {
      throw new HttpException("Invalid refresh token", 401);
    }
    
    return $this->generateJWT($payload['user_id']);
  }

  /**
   * Generate a JWT token.
   * 
   * @param string $userId The user ID.
   * @return string The generated JWT token.
  */
  private function generateJWT(string $userId) {
    $payload = [
      'user_id' => $userId,
      'exp' => time() + $this->tokenValidity
    ];
    return JWT::generate($payload);
  }
}