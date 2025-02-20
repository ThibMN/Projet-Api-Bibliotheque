<?php

namespace App\Models;

use \PDO;
use App\Utils\HttpException;

/**
 * Class AuthorModel
 * 
 * This model handles author-related logic such as creating, retrieving, and listing authors.
*/
class AuthorModel extends SqlConnect {
    /**
     * @var string $table The name of the authors table.
    */
    private string $table = "authors";

    /**
     * Create a new author.
     * 
     * @param array $data The author data including name and biography.
     * @return array The created author data.
     * @throws HttpException If the author's name is not provided.
    */
    public function create(array $data) {
        if (empty($data['name'])) {
            throw new HttpException("Le nom de l'auteur est requis", 400);
        }

        $query = "INSERT INTO $this->table (name, biography) VALUES (:name, :biography)";
        $req = $this->db->prepare($query);
        $req->execute([
            'name' => $data['name'],
            'biography' => $data['biography'] ?? null
        ]);
        
        return $this->get($this->db->lastInsertId());
    }

    /**
     * Get an author by ID.
     * 
     * @param int $id The author ID.
     * @return array The author data.
     * @throws HttpException If the author is not found.
    */
    public function get(int $id) {
        $query = "SELECT a.*, 
                 (SELECT COUNT(*) FROM books WHERE author_id = a.id) as books_count
                 FROM $this->table a 
                 WHERE a.id = :id";
        
        $req = $this->db->prepare($query);
        $req->execute(['id' => $id]);
        
        if ($req->rowCount() === 0) {
            throw new HttpException("Auteur non trouvé", 404);
        }
        
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all authors.
     * 
     * @return array The list of all authors with their book counts.
    */
    public function getAll() {
        $query = "SELECT a.*, 
                 (SELECT COUNT(*) FROM books WHERE author_id = a.id) as books_count
                 FROM $this->table a";
        
        $req = $this->db->prepare($query);
        $req->execute();
        
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update an author.
     * 
     * @param array $data The author data to update.
     * @param int $id The author ID.
     * @return array The updated author data.
     * @throws HttpException If the author's name is not provided.
    */
    public function update(array $data, int $id) {
        if (empty($data['name'])) {
            throw new HttpException("Le nom de l'auteur est requis", 400);
        }

        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'biography'])) {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        $params[':id'] = $id;
        $query = "UPDATE $this->table SET " . implode(', ', $fields) . " WHERE id = :id";

        $req = $this->db->prepare($query);
        $req->execute($params);

        return $this->get($id);
    }

    /**
     * Delete an author.
     * 
     * @param int $id The author ID.
     * @return array A message indicating the result of the deletion.
     * @throws HttpException If the author has associated books.
    */
    public function delete(int $id) {
        // Vérifier si l'auteur a des livres
        $query = "SELECT COUNT(*) as count FROM books WHERE author_id = :id";
        $req = $this->db->prepare($query);
        $req->execute(['id' => $id]);
        $result = $req->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            throw new HttpException("Impossible de supprimer l'auteur car il a des livres associés", 400);
        }

        $query = "DELETE FROM $this->table WHERE id = :id";
        $req = $this->db->prepare($query);
        $req->execute(['id' => $id]);
        
        return ['message' => 'Auteur supprimé avec succès'];
    }
}