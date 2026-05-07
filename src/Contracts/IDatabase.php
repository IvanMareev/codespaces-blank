<?php
namespace App\Contracts;

interface IDatabase {
    public function query(string $sql, array $params = []): \mysqli_result|bool;
    public function getLastInsertId(): int;
}
?>