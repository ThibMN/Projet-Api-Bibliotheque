<?php

namespace App\Models;

use \PDO;
use App\Utils\HttpException;

class BookModel extends SqlConnect {
    private string $table = "books";

    /**
     * Create a new book.
     * 
     * @param array $data The book data to insert.
     * @return array The newly created book data.
    */
    public function create(array $data) {
        $query = "INSERT INTO $this->table (title, author_id, isbn, published_year) 
                 VALUES (:title, :author_id, :isbn, :published_year)";
        
        $req = $this->db->prepare($query);
        $req->execute($data);
        
        return $this->get($this->db->lastInsertId());
    }

    /**
     * Get a book by ID.
     * 
     * @param int $id The book ID.
     * @return array The book data.
     * @throws HttpException If the book is not found.
    */
    public function get(int $id) {
        $query = "SELECT b.*, a.name as author_name 
                 FROM $this->table b 
                 LEFT JOIN authors a ON b.author_id = a.id 
                 WHERE b.id = :id";
                 
        $req = $this->db->prepare($query);
        $req->execute(['id' => $id]);
        
        if ($req->rowCount() === 0) {
            throw new HttpException("Livre non trouvé", 404);
        }
        
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all books.
     * 
     * @return array An array of all books data.
    */
    public function getAll() {
        $query = "SELECT b.*, a.name as author_name 
                 FROM $this->table b 
                 LEFT JOIN authors a ON b.author_id = a.id";
                 
        $req = $this->db->prepare($query);
        $req->execute();
        
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a book.
     * 
     * @param array $data The book data to update.
     * @param int $id The book ID.
     * @return array The updated book data.
    */
    public function update(array $data, int $id) {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $params[':id'] = $id;
        $query = "UPDATE $this->table SET " . implode(', ', $fields) . " WHERE id = :id";

        $req = $this->db->prepare($query);
        $req->execute($params);

        return $this->get($id);
    }

    /**
     * Delete a book.
     * 
     * @param int $id The book ID.
     * @return array A message indicating the result of the deletion.
    */
    public function delete(int $id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $req = $this->db->prepare($query);
        $req->execute(['id' => $id]);
        
        return ['message' => 'Livre supprimé avec succès'];
    }
}