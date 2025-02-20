<?php 

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\AuthModel;
use App\Utils\{Route, HttpException};

/**
 * Class AuthController
 * 
 * This controller handles user authentication actions such as registration and login.
*/
class AuthController extends Controller {
    /**
     * @var AuthModel $auth Instance of the AuthModel for handling authentication logic.
    */
    protected object $auth;

    /**
     * AuthController constructor.
     * 
     * @param array $params Parameters passed to the controller.
    */
    public function __construct($params) {
        $this->auth = new AuthModel();
        parent::__construct($params);
    }

    /**
     * Register a new user.
     * 
     * @Route("POST", "/auth/register")
     * 
     * @return array The registered user data.
     * @throws HttpException If email or password is missing or registration fails.
    */
    #[Route("POST", "/auth/register")]
    public function register() {
        try {
            $data = $this->body;
            if (empty($data['email']) || empty($data['password'])) {
                throw new HttpException("Missing email or password.", 400);
            }
            $user = $this->auth->register($data);
            return $user;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }

    /**
     * Log in a user.
     * 
     * @Route("POST", "/auth/login")
     * 
     * @return string The authentication token.
     * @throws HttpException If email or password is missing or login fails.
    */
    #[Route("POST", "/auth/login")]
    public function login() {
        try {
            $data = $this->body;
            if (empty($data['email']) || empty($data['password'])) {
                throw new HttpException("Missing email or password.", 400);
            }
            $token = $this->auth->login($data['email'], $data['password']);
            return $token;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 401);
        }
    }
}