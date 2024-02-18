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
     * getAllItemsOfSameType
     * 
     * get all items of the same type
     *
     * @param int $id
     * @return array
     */
    //Used to get all items not of the same type for select list option removal
    //I started with getting all items of the same type but this makes the JS code less complex
    public function getAllItemsNotOfSameType($id): array{
        // get the type of the item
        $stmt = $this->db->prepare("SELECT item_type FROM items WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $type = $stmt->fetchColumn();

        // get all items of the same type
        $stmt = $this->db->prepare("SELECT * FROM items WHERE item_type != :type");
        $stmt->bindParam(':type', $type);
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

    /**
     * getItemType
     * 
     * get item type by id
     *
     * @param int $id
     * @return int
     */
    public function getItemType($id): int{
        $stmt = $this->db->prepare("SELECT item_type FROM items WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }
}