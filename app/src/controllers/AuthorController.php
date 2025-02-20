<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\AuthorModel;
use App\Utils\{Route, HttpException};
use App\Middlewares\AuthMiddleware;

/**
 * Class AuthorController
 * 
 * This controller handles actions related to authors such as retrieving, creating, and updating authors.
*/
class AuthorController extends Controller {
    /**
     * @var AuthorModel $author Instance of the AuthorModel for handling author-related logic.
    */
    private AuthorModel $author;

    /**
     * AuthorController constructor.
     * 
     * @param array $params Parameters passed to the controller.
    */
    public function __construct($params) {
        parent::__construct($params);
        $this->author = new AuthorModel();
    }

    /**
     * Retrieve all authors.
     * 
     * @Route("GET", "/authors")
     * 
     * @return array List of all authors.
    */
    #[Route("GET", "/authors")]
    public function getAll() {
        return $this->author->getAll();
    }

    /**
     * Retrieve a single author by ID.
     * 
     * @Route("GET", "/authors/:id")
     * 
     * @return array The author data.
    */
    #[Route("GET", "/authors/:id")]
    public function getOne() {
        return $this->author->get($this->params['id']);
    }

    /**
     * Create a new author.
     * 
     * @Route("POST", "/authors", middlewares: [AuthMiddleware::class])
     * 
     * @return array The created author data.
     * @throws HttpException If the name is missing or creation fails.
    */
    #[Route("POST", "/authors", middlewares: [AuthMiddleware::class])]
    public function create() {
        try {
            if (empty($this->body['name'])) {
                throw new HttpException("Le nom est obligatoire", 400);
            }

            return $this->author->create($this->body);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }

    /**
     * Update an existing author.
     * 
     * @Route("PUT", "/authors/:id", middlewares: [AuthMiddleware::class])
     * 
     * @return array The updated author data.
     * @throws HttpException If the name is missing or update fails.
    */
    #[Route("PUT", "/authors/:id", middlewares: [AuthMiddleware::class])]
    public function update() {
        try {
            if (empty($this->body['name'])) {
                throw new HttpException("Le nom est obligatoire", 400);
            }
            
            return $this->author->update($this->body, $this->params['id']);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }

    /**
     * Delete an existing author.
     * 
     * @Route("DELETE", "/authors/:id", middlewares: [AuthMiddleware::class])
     * 
     * @return bool True if the author was deleted, false otherwise.
    */
    #[Route("DELETE", "/authors/:id", middlewares: [AuthMiddleware::class])]
    public function delete() {
        return $this->author->delete($this->params['id']);
    }
}