<?php

use classes\db;



class Item {
    public $id;
    public $item;
    public $itemType;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getItemObj($id): object{
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function getItems(): array{
        $stmt = $this->db->prepare("SELECT * FROM items;");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getItemObjects(array $item): array{

        foreach ($item as $key => $value) {
            
            $result[] = $this->getItemObj($value);
        }
        
        return $result;
    }
}