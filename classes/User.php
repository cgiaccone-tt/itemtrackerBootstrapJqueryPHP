<?php

use classes\db;



class User {
    public $id;
    public $fName;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUser($id): array{
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getUserByName($fName): int{
        $stmt = $this->db->prepare("SELECT id FROM users WHERE fName = :fName");
        $stmt->bindParam(':fName', $fName);
        $stmt->execute();
        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $result['id'];
        }else{
            return 0;
        }
    }

    public function getUsers(): array {
        $stmt = $this->db->prepare("SELECT * FROM users;");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function addUser($fName): int{
        $stmt = $this->db->prepare("INSERT INTO users (fName) VALUES (:fName)");
        $stmt->bindParam(':fName', $fName);
        $stmt->execute();
        return $this->db->lastInsertId('users');
    }

    public function matchUser($fName): bool{
        $userMatch = false;
        $stmt = $this->db->prepare("SELECT * FROM users WHERE fName = :fName");
        $stmt->bindParam(':fName', $fName);
        $stmt->execute();
        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userMatch = true;
        }
        return $userMatch;
    }


}