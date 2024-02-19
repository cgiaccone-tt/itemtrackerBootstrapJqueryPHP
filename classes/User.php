<?php


class User {
    public $id; /* user id */
    public $fName; /* user first name */
    private $db; /* database connection */

    /**
     * __construct
     * 
     * @param db $db
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * getUser
     * 
     * get user by id
     *
     * @param int $id
     * @return array
     */
    public function getUser($id): array{
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * getUserByName
     * 
     * get user by name
     * used to decide if user exists
     *
     * @param string $fName
     * @return int
     */
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

    /**
     * addUser
     * 
     * add a user to the users table
     *
     * @param string $fName
     * @return int
     */
    public function addUser($fName): int{
        $stmt = $this->db->prepare("INSERT INTO users (fName) VALUES (:fName)");
        $stmt->bindParam(':fName', $fName);
        $stmt->execute();
        return $this->db->lastInsertId('users');
    }
}