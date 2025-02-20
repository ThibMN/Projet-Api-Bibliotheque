<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\BookModel;
use App\Utils\{Route, HttpException};
use App\Middlewares\AuthMiddleware;

/**
 * Class BookController
 * 
 * This controller handles actions related to books such as retrieving, creating, updating, and deleting books.
*/
class BookController extends Controller {
    /**
     * @var BookModel $book Instance of the BookModel for handling book-related logic.
    */
    private BookModel $book;

    /**
     * BookController constructor.
     * 
     * @param array $params Parameters passed to the controller.
    */
    public function __construct($params) {
        parent::__construct($params);
        $this->book = new BookModel();
    }

    /**
     * Retrieve all books.
     * 
     * @Route("GET", "/books")
     * 
     * @return array List of all books.
    */
    #[Route("GET", "/books")]
    public function getAll() {
        return $this->book->getAll();
    }

    /**
     * Retrieve a single book by ID.
     * 
     * @Route("GET", "/books/:id")
     * 
     * @return array The book data.
    */
    #[Route("GET", "/books/:id")]
    public function getOne() {
        return $this->book->get($this->params['id']);
    }

    /**
     * Create a new book.
     * 
     * @Route("POST", "/books", middlewares: [AuthMiddleware::class])
     * 
     * @return array The created book data.
     * @throws HttpException If the title or author_id is missing or creation fails.
    */
    #[Route("POST", "/books", middlewares: [AuthMiddleware::class])]
    public function create() {
        try {
            if (empty($this->body['title']) || empty($this->body['author_id'])) {
                throw new HttpException("Le titre et l'auteur sont obligatoires", 400);
            }

            return $this->book->create($this->body);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }

    /**
     * Update an existing book.
     * 
     * @Route("PUT", "/books/:id", middlewares: [AuthMiddleware::class])
     * 
     * @return array The updated book data.
     * @throws HttpException If update fails.
    */
    #[Route("PUT", "/books/:id", middlewares: [AuthMiddleware::class])]
    public function update() {
        try {
            return $this->book->update($this->body, $this->params['id']);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }

    /**
     * Delete an existing book.
     * 
     * @Route("DELETE", "/books/:id", middlewares: [AuthMiddleware::class])
     * 
     * @return bool True if the book was deleted, false otherwise.
    */
    #[Route("DELETE", "/books/:id", middlewares: [AuthMiddleware::class])]
    public function delete() {
        return $this->book->delete($this->params['id']);
    }
}