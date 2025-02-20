<?php

namespace App\Models;

use \PDO;
use stdClass;

class UserModel extends SqlConnect {
  private $table = "users";
  public $authorized_fields_to_update = ['first_name', 'last_name'];

  /**
    * Add a new user.
    * 
    * @param array $data The user data to insert.
    * @return void
  */
  public function add(array $data) {
    $query = "
      INSERT INTO $this->table (first_name, last_name, promo, school)
      VALUES (:first_name, :last_name, :promo, :school)
    ";

    $req = $this->db->prepare($query);
    $req->execute($data);
  }

  /**
   * Delete a user by ID.
   * 
   * @param int $id The user ID.
   * @return stdClass An empty object.
  */
  public function delete(int $id) {
    $req = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");
    $req->execute(["id" => $id]);
    return new stdClass();
  }

  /**
   * Get a user by ID.
   * 
   * @param int $id The user ID.
   * @return array|stdClass The user data or an empty object if not found.
  */
  public function get(int $id) {
    $req = $this->db->prepare("SELECT * FROM users WHERE id = :id");
    $req->execute(["id" => $id]);

    return $req->rowCount() > 0 ? $req->fetch(PDO::FETCH_ASSOC) : new stdClass();
  }

  /**
    * Get all users.
   * 
    * @param int|null $limit The maximum number of users to retrieve.
    * @return array An array of all users data.
   */
  public function getAll(?int $limit = null) {
    $query = "SELECT * FROM {$this->table}";
     
    if ($limit !== null) {
      $query .= " LIMIT :limit";
      $params = [':limit' => (int)$limit];
    } else {
      $params = [];
    }
      
    $req = $this->db->prepare($query);
    foreach ($params as $key => $value) {
      $req->bindValue($key, $value, PDO::PARAM_INT);
    }
    $req->execute();
      
    return $req->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get the last inserted user.
   * 
   * @return array|stdClass The last user data or an empty object if not found.
  */
  public function getLast() {
    $req = $this->db->prepare("SELECT * FROM $this->table ORDER BY id DESC LIMIT 1");
    $req->execute();

    return $req->rowCount() > 0 ? $req->fetch(PDO::FETCH_ASSOC) : new stdClass();
  }

  /**
   * Update a user by ID.
   * 
   * @param array $data The user data to update.
   * @param int $id The user ID.
   * @return void
  */
  public function update(array $data, int $id) {
    $request = "UPDATE $this->table SET ";
    $params = [];
    $fields = [];
  
    # Prepare the query dynamically based on the provided data
    foreach ($data as $key => $value) {
      if (in_array($key, $this->authorized_fields_to_update)) {
        $fields[] = "$key = :$key";
        $params[":$key"] = $value;
      }
    }
  
    $params[':id'] = $id;
    $query = $request . implode(", ", $fields) . " WHERE id = :id";
  
    $req = $this->db->prepare($query);
    $req->execute($params);
      
    return $this->get($id);
  }

  /**
   * Upload a profile image for a user.
   * 
   * @param int $userId The user ID.
   * @param array $file The file data from the upload.
   * @return string The file name of the uploaded image.
   * @throws HttpException If no image is provided or if there is an error during upload.
  */
  public function uploadProfileImage(int $userId, array $file) {
    if (!isset($file['tmp_name'])) {
      throw new HttpException("Aucune image fournie", 400);
    }

    $uploadDir = __DIR__ . '/../../uploads/';
    $fileName = uniqid() . '_' . $file['name'];
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
      throw new HttpException("Erreur lors de l'upload", 500);
    }

    $this->update(['profile_image' => $fileName], $userId);
    return $fileName;
  }
}