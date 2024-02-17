<?php

use classes\db;



class Item {
    public $id; /* item id */
    public $item; /* item name */
    public $itemType; /* item type */
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
     * getItemObj
     * 
     * get item by id and return as an object
     *
     * @param [type] $id
     * @return object
     */

    public function getItemObj($id): object{
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    /**
     * getItems
     * 
     * get all items from the items table
     *
     * @return array
     */
    public function getItems(): array{
        $stmt = $this->db->prepare("SELECT * FROM items;");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * getItemObjects
     * 
     * get items by id and return as an object
     *
     * @param array $item
     * @return array
     */
    public function getItemObjects(array $item): array{

        foreach ($item as $key => $value) {
            
            $result[] = $this->getItemObj($value);
        }
        
        return $result;
    }
}