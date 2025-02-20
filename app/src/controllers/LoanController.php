<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\LoanModel;
use App\Utils\{Route, HttpException};
use App\Middlewares\AuthMiddleware;

/**
 * Class LoanController
 * 
 * This controller handles actions related to loans such as creating a loan, retrieving user loans, and returning a book.
*/
class LoanController extends Controller {
    /**
     * @var LoanModel $loan Instance of the LoanModel for handling loan-related logic.
    */
    private LoanModel $loan;

    /**
     * LoanController constructor.
     * 
     * @param array $params Parameters passed to the controller.
    */
    public function __construct($params) {
        parent::__construct($params);
        $this->loan = new LoanModel();
    }

    /**
     * Create a new loan.
     * 
     * @Route("POST", "/loans", middlewares: [AuthMiddleware::class])
     * 
     * @return array The created loan data.
     * @throws HttpException If the book_id is missing or creation fails.
    */
    #[Route("POST", "/loans", middlewares: [AuthMiddleware::class])]
    public function create() {
        try {
            if (empty($this->body['book_id'])) {
                throw new HttpException("L'ID du livre est requis", 400);
            }

            if (empty($this->body['user_id'])) {
                throw new HttpException("L'ID du livre est requis", 400);
            }

            $data = [
                'book_id' => $this->body['book_id'],
                'user_id' => $this->body['user_id']
            ];

            return $this->loan->create($data);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }

    /**
     * Retrieve loans for the authenticated user.
     * 
     * @Route("GET", "/loans", middlewares: [AuthMiddleware::class])
     * 
     * @return array List of loans for the user.
    */
    #[Route("GET", "/loans", middlewares: [AuthMiddleware::class])]
    public function getUserLoans() {
        return $this->loan->getUserLoans($_REQUEST->user_id);
    }

    /**
     * Return a book.
     * 
     * @Route("POST", "/loans/:id/return", middlewares: [AuthMiddleware::class])
     * 
     * @return bool True if the book was returned, false otherwise.
     * @throws HttpException If the return process fails.
    */
    #[Route("POST", "/loans/:id/return", middlewares: [AuthMiddleware::class])]
    public function returnBook() {
        try {
            return $this->loan->return($this->params['id']);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 400);
        }
    }
}