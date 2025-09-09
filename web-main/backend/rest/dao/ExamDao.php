<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class ExamDao
{

  private $connection;

  public function __construct() {
    try {
      $host = "127.0.0.1";  // XAMPP default
      $port     = "3306";        // MySQL default port
      $dbName   = "sakila";      // make sure this DB exists in phpMyAdmin
      $username = "root";        // default user
      $password = "hannan12"; 

        echo "<br>Using DB host: $host";

        $this->connection = new PDO(
            "mysql:host=$host;dbname=$dbName;port=$port;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        echo " Connected successfully<br>";

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

public function showTables() {
  $stmt = $this->connection->query("SHOW TABLES");
  return $stmt->fetchAll();
}

// ExamDao.php
public function login($data) {
  $stmt = $this->connection->prepare("SELECT * FROM customer WHERE email = :email");
  $stmt->execute([':email' => $data['email']]);
  $user = $stmt->fetch();

  if ($user && $user['password'] === $data['password']) {
      return $user; // return full user row if login is correct
  }
  return null;
}


  public function film_performance_report() {
    $stmt = $this->connection->query("
    SELECT c.category_id AS id,
        c.name AS name,
        COUNT(c.name) AS total
        FROM category c
        GROUP BY c.category_id");
  return $stmt->fetchAll();
  }



  public function delete_film($film_id) {
    $stmt = $this->connection->prepare("DELETE FROM film WHERE film_id = :id");
    $stmt->bindParam(':id', $film_id, PDO::PARAM_INT);
    return $stmt->execute(); // returns true if successful, false otherwise
}



public function edit_film($film_id, $data) {
    $stmt = $this->connection->prepare("
        UPDATE film
        SET title = :title,
            description = :description,
            release_year = :release_year
        WHERE film_id = :id
    ");
    $stmt->execute([
        ':title' => $data['title'],
        ':description' => $data['description'],
        ':release_year' => $data['release_year'],
        ':id' => $film_id
    ]);

    // fetch updated row back
    $stmt = $this->connection->prepare("SELECT * FROM film WHERE film_id = :id");
    $stmt->execute([":id" => $film_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



/* return $stmt->execute([
    ':title' => $data['title'],
    ':description'  => $data['description'],
    ':release_year' => $data['release_year'],
    ':id'         => $film_id
]);
} */

public function get_customer_rentals($customer_id) {
  $stmt = $this->connection->prepare("
      SELECT 
          r.rental_date     AS rental_date,
          f.title           AS film_title,
          p.amount          AS payment_amount
      FROM rental r
      JOIN inventory i ON r.inventory_id = i.inventory_id
      JOIN film f ON i.film_id = f.film_id
      JOIN payment p ON r.rental_id = p.rental_id
      WHERE r.customer_id = :customer_id
      ORDER BY r.rental_date DESC
  ");
  $stmt->execute([':customer_id' => $customer_id]);
  return $stmt->fetchAll();
}

}
/* 
hannan */

