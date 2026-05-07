<?php
namespace App\Repositories;

use mysqli;
use Exception;

class Database implements \App\Contracts\IDatabase {
    private mysqli $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }

    public function query(string $sql, array $params = []): \mysqli_result|bool {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function getLastInsertId(): int {
        return $this->conn->insert_id;
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>