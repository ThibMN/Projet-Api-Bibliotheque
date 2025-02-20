<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\UserModel;
use App\Utils\{Route, HttpException};
use App\Middlewares\AuthMiddleware;

/**
 * Class UserController
 * 
 * This controller handles actions related to users such as creating, deleting, retrieving, and updating users.
*/
class UserController extends Controller {
    /**
     * @var UserModel $user Instance of the UserModel for handling user-related logic.
    */
    private UserModel $user;

    /**
     * UserController constructor.
     * 
     * @param array $params Parameters passed to the controller.
    */
    public function __construct($params) {
        parent::__construct($params);
        $this->user = new UserModel();
    }

    /**
     * Create a new user.
     * 
     * @Route("POST", "/users")
     * 
     * @return array The created user data.
    */
    #[Route("POST", "/users")]
    public function createUser() {
        $this->user->add($this->body);
        return $this->user->getLast();
    }

    /**
     * Delete an existing user.
     * 
     * @Route("DELETE", "/users/:id", middlewares: [AuthMiddleware::class])
     * 
     * @return bool True if the user was deleted, false otherwise.
    */
    #[Route("DELETE", "/users/:id", middlewares: [AuthMiddleware::class])]
    public function delete() {
        return $this->user->delete($this->params['id']);
    }

    /**
     * Retrieve a single user by ID.
     * 
     * @Route("GET", "/users/:id", middlewares: [AuthMiddleware::class])
     * 
     * @return array The user data.
    */
    #[Route("GET", "/users/:id", middlewares: [AuthMiddleware::class])]
    public function getOne() {
        return $this->user->get($this->params['id']);
    }

    /**
     * Retrieve all users.
     * 
     * @Route("GET", "/users", middlewares: [AuthMiddleware::class])
     * 
     * @return array List of all users.
    */
    #[Route("GET", "/users", middlewares: [AuthMiddleware::class])]
    public function getAll() 
    {
        return $this->user->getAll();
    }

    /**
     * Update an existing user.
     * 
     * @Route("PATCH", "/users/:id")
     * 
     * @return array The updated user data.
     * @throws HttpException If the update data is empty or update fails.
    */
    #[Route("PATCH", "/users/:id")]
    public function updateUser() {
        try {
            $id = intval($this->params['id']);
            $data = $this->body;

            if (empty($data)) {
                throw new HttpException("Missing parameters for the update.", 400);
            }

            $missingFields = array_diff($this->user->authorized_fields_to_update, array_keys($data));
            if (!empty($missingFields)) {
                throw new HttpException("Missing fields: " . implode(", ", $missingFields), 400);
            }

            $this->user->update($data, intval($id));
            return $this->user->get($id);
        } catch (HttpException $e) {
            throw $e;
        }
    }

    #[Route("PUT", "/users/:id", middlewares: [AuthMiddleware::class])]
    public function update() {
        try {
            if (empty($this->body)) {
                throw new HttpException("Données de mise à jour manquantes", 400);
            }
            return $this->user->update($this->body, $this->params['id']);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }
}
