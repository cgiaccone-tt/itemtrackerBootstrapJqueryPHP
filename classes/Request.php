<?php

require 'classes/User.php';
require 'classes/Item.php';



class Request
{
    public $id;
    public $user;
    public $item;
    public $itemType;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getRequest($id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM requests WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getRequests(): array
    {
        //$pencil = '<span class="bi bi-pencil"></span>';
        $sql = <<<SQL
        SELECT u.fName, GROUP_CONCAT( i.item SEPARATOR', ') AS item, 
        it.type, r.id AS action, dt_requested
        FROM requests r 
        LEFT JOIN users u on r.user_fk = u.id 
        LEFT JOIN items i ON r.item_fk = i.Id 
        LEFT JOIN item_type it ON i.item_type = it.id
        GROUP BY fName, type, DAY(dt_requested);
SQL;

        error_log("sql-" . print_r($sql, true) . "\n\n", 3, "C:\cg\work\ascendion\logs\\test.log");

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            $id = $value['action'];
            $result[$key]['action'] = "<span class=\"bi bi-pencil\" data-id=\"{$id}\"></span>";
        }
        return $result;
    }

    public function addRequest($fName, $item): int
    {
        //instantiate user object
        $userObj = new User($this->db);
        if (!$userId = $userObj->getUserByName($fName)) {
            $userId = $userObj->addUser($fName);
        }
        
        $itemObj = new Item($this->db);
        $itemObjArr[] = $itemObj->getItemObjects($item);
        $itemObjJson = json_encode($itemObjArr);
        $item = 5;
        error_log("addRequest-item-".print_r($item, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
        $stmt = $this->db->prepare("INSERT INTO requests (user_fk, item_fk, json1) VALUES (:user, :item, :itemObjJson)");
        $stmt->bindParam(':user', $userId);
        $stmt->bindParam(':item', $item);
        $stmt->bindParam(':itemObjJson', $itemObjJson);
        $stmt->execute();
        return $this->db->lastInsertId('requests');
    }

    public function updateRequest($id, $fName, $item): bool
    {
        //instantiate user object
        $userObj = new User($this->db);
        if (!$userId = $userObj->getUserByName($fName)) {
            $userId = $userObj->addUser($fName);
        }

        $stmt = $this->db->prepare("UPDATE requests SET user_fk = :user, item_fk = :item WHERE id = :id");
        $stmt->bindParam(':user', $userId);
        $stmt->bindParam(':item', $item);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return true;
    }
}
