<?php

namespace App\Models;

use \PDO;
use App\Utils\HttpException;

class LoanModel extends SqlConnect {
    private string $table = "loans";

    /**
     * Create a new loan.
     * 
     * @param array $data The loan data to insert.
     * @return array The newly created loan data.
     * @throws HttpException If the book is not available.
    */
    public function create(array $data) {
        // Vérifier si le livre est disponible
        $query = "SELECT available FROM books WHERE id = :book_id";
        $req = $this->db->prepare($query);
        $req->execute(['book_id' => $data['book_id']]);
        $book = $req->fetch(PDO::FETCH_ASSOC);

        if (!$book['0']) {
            throw new HttpException("Ce livre n'est pas disponible", 400);
        }

        // Créer l'emprunt
        $query = "INSERT INTO $this->table (book_id, user_id, return_date) 
            VALUES (:book_id, :user_id, DATE_ADD(NOW(), INTERVAL 14 DAY))";
        
        $req = $this->db->prepare($query);
        $req->execute($data);

        // Mettre à jour la disponibilité du livre
        $query = "UPDATE books SET available = false WHERE id = :book_id";
        $req = $this->db->prepare($query);
        $req->execute(['book_id' => $data['book_id']]);
        
        return $this->get($this->db->lastInsertId());
    }

    /**
     * Get a loan by ID.
     * 
     * @param int $id The loan ID.
     * @return array The loan data.
     * @throws HttpException If the loan is not found.
    */
    public function get(int $id) {
        $query = "SELECT l.*, b.title as book_title, CONCAT(u.first_name, ' ', u.last_name) as user_name 
            FROM $this->table l 
            JOIN books b ON l.book_id = b.id 
            JOIN users u ON l.user_id = u.id 
            WHERE l.id = :id";
                 
        $req = $this->db->prepare($query);
        $req->execute(['id' => $id]);
        
        if ($req->rowCount() === 0) {
            throw new HttpException("Emprunt non trouvé", 404);
        }
        
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Return a loan.
     * 
     * @param int $id The loan ID.
     * @return array A message indicating the result of the return.
     * @throws HttpException If the loan is not found.
    */
    public function return(int $loan_id) {
        // Marquer comme retourné
        $query = "UPDATE $this->table SET returned = true WHERE id = :id";
        $req = $this->db->prepare($query);
        $req->execute(['id' => $loan_id]);

        // Récupérer l'ID du livre
        $query = "SELECT book_id FROM $this->table WHERE id = :id";
        $req = $this->db->prepare($query);
        $req->execute(['id' => $loan_id]);
        $loan = $req->fetch(PDO::FETCH_ASSOC);

        // Mettre à jour la disponibilité du livre
        $query = "UPDATE books SET available = true WHERE id = :book_id";
        $req = $this->db->prepare($query);
        $req->execute(['book_id' => $loan['book_id']]);

        return ['message' => 'Livre retourné avec succès'];
    }

    /**
     * Get all loans for a specific user.
     * 
     * @param int $user_id The user ID.
     * @return array An array of the user's active loans.
    */
    public function getUserLoans(int $user_id) {
        $query = "SELECT l.*, b.title as book_title 
            FROM $this->table l 
            JOIN books b ON l.book_id = b.id 
            WHERE l.user_id = :user_id AND l.returned = false";
                 
        $req = $this->db->prepare($query);
        $req->execute(['user_id' => $user_id]);
        
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}