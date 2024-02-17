<?php

require 'classes/User.php';
require 'classes/Item.php';



class Request
{
    public $id; /* request id */
    public $user; /* user id */
    public $item; /* item id */
    public $itemType; /* item type */
    private $db; /* database connection */

    /**
     * __construct
     * 
     * @param db $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * getRequest
     * 
     * get request by id
     *
     * @param int $id
     * @return array
     */
    public function getRequest($id): array
    {
        $stmt = $this->db->prepare("
        SELECT u.fName, GROUP_CONCAT( i.id SEPARATOR', ') AS i 
        FROM requests r 
        LEFT JOIN users u ON r.user_fk = u.id
        LEFT JOIN type_requests tr ON r.id = tr.request_fk
        LEFT JOIN items i ON tr.item_fk = i.id
        WHERE r.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
        
    }

    /**
     * getRequests
     * 
     * get all requests
     *
     * @return array
     */
    public function getRequests(): array
    {
        $stmt = $this->db->prepare("
        SELECT u.fName, GROUP_CONCAT( i.item SEPARATOR', ') AS item, 
        it.type, r.id AS action, r.dt_requested
        FROM requests r 
        LEFT JOIN type_requests tr ON r.id = tr.request_fk 
        LEFT JOIN item_type it ON it.id = tr.type_fk 
        LEFT JOIN items i ON i.id = tr.item_fk
        LEFT JOIN users u ON u.id = r.user_fk
        GROUP BY r.id, fName, it.type, DAY(r.dt_requested);
        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            $id = $value['action'];
            $result[$key]['action'] = "<span class=\"bi bi-pencil\" data-id=\"{$id}\"></span>";
        }
        return $result;
    }

    /**
     * addRequest
     * 
     * add a request to the requests table
     *
     * @param string $fName
     * @param string $item
     * @return int
     */
    public function addRequest($fName, $item): int
    {
        //instantiate user object
        $userObj = new User($this->db);
        if (!$userId = $userObj->getUserByName($fName)) {
            $userId = $userObj->addUser($fName);
        }

        $stmt = $this->db->prepare("INSERT INTO requests (user_fk) VALUES (:user)");
        $stmt->bindParam(':user', $userId);
        $stmt->execute();

        $request = $this->db->lastInsertId('requests');

        $this->createRequestItemArray($request, $item);

        return $request;
    }

    /**
     * addTypeRequest
     * 
     * add a type request to the type_requests table
     *
     * @param int $request
     * @param object $item
     * @return int
     */
    public function addTypeRequest($request, $item): int
    {
        $stmt = $this->db->prepare("INSERT INTO type_requests (request_fk, type_fk, item_fk) VALUES (:request, :type, :item)");
        $stmt->bindParam(':request', $request);
        $stmt->bindParam(':type', $item->item_type);
        $stmt->bindParam(':item', $item->id);
        $stmt->execute();
        return $this->db->lastInsertId('type_requests');
    }

    /**
     * createRequestItemArray
     * 
     * create an array of items for a request
     *
     * @param int $request
     * @param array $item
     * @return void
     */
    public function createRequestItemArray($request, $item): void
    {
        $itemObj = new Item($this->db);
        $itemObjArr = $itemObj->getItemObjects($item);

        foreach ($itemObjArr as $key => $value) {
            $this->addTypeRequest($request, $value);
        }
    }

    /**
     * updateRequest
     * 
     * update a request in the requests table
     *
     * @param int $id
     * @param string $fName
     * @param string $item
     * @return bool
     */
    public function updateRequest($id, $fName, $item): bool
    {
        //instantiate user object
        $userObj = new User($this->db);
        if (!$userId = $userObj->getUserByName($fName)) {
            $userId = $userObj->addUser($fName);
            $this->updateUserInReqest($id, $userId);
        }

        //delete all type_requests for this request in preparation for repopulation
        $stmt = $this->db->prepare("DELETE FROM type_requests WHERE request_fk = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $this->createRequestItemArray($id, $item);

        return true;
    }

    /**
     * updateUserInReqest
     * 
     * update the user in the requests table
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function updateUserInReqest($id, $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE requests SET user_fk = :user WHERE id = :id");
        $stmt->bindParam(':user', $userId);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return true;
    }
}
