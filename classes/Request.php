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
        $stmt = $this->db->prepare("SELECT type_fk, request_fk FROM type_requests WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $typeRequestArr = $stmt->fetch(PDO::FETCH_ASSOC);

        $type_id = $typeRequestArr['type_fk'];
        $request_id = $typeRequestArr['request_fk'];
        
        $stmt = $this->db->prepare("
        SELECT u.fName, GROUP_CONCAT( i.id SEPARATOR', ') AS i, 
        it.type, r.id AS action, r.dt_requested
        FROM requests r 
        LEFT JOIN type_requests tr ON r.id = tr.request_fk 
        LEFT JOIN item_type it ON it.id = tr.type_fk 
        LEFT JOIN items i ON i.id = tr.item_fk
        LEFT JOIN users u ON u.id = r.user_fk
        WHERE tr.request_fk = :request_id AND tr.type_fk = :type_id
        GROUP BY fName, it.type, DAY(r.dt_requested);
        ");
        $stmt->bindParam(':request_id', $request_id);
        $stmt->bindParam(':type_id', $type_id);
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
        it.type, tr.id AS action, r.dt_requested
        FROM requests r 
        LEFT JOIN type_requests tr ON r.id = tr.request_fk 
        LEFT JOIN item_type it ON it.id = tr.type_fk 
        LEFT JOIN items i ON i.id = tr.item_fk
        LEFT JOIN users u ON u.id = r.user_fk
        GROUP BY fName, it.type, DAY(r.dt_requested);
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
        error_log("request-addReqest-".print_r($item, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
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
    public function updateRequest($id, $fName, $item, $type=true): bool
    {
        //If type is true then we are pulling items from the type_requests table
        //and we need to convert to the request id
        if ($type) {
            $stmt = $this->db->prepare("SELECT request_fk FROM type_requests WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $id = $stmt->fetchColumn();
        }
        error_log("request-updateRequest-item-".print_r($item, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
        $stmt = $this->db->prepare("
        SELECT i.id 
        FROM type_requests tr
        LEFT JOIN items i ON i.id = tr.item_fk
        WHERE request_fk = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $existingItems = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $items = array_merge($item, $existingItems);

        //Remove any duplicates
        $items = is_array($items) ? array_unique($items) : [];
        error_log("request-updateRequest-items-".print_r($items, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");

        $stmt = $this->db->prepare("
        DELETE FROM type_requests 
        WHERE request_fk = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $this->createRequestItemArray($id, $items);
    

        //instantiate user object
        $userObj = new User($this->db);
        if (!$userId = $userObj->getUserByName($fName)) {
            $userId = $userObj->addUser($fName);
            $this->updateUserInReqest($id, $userId);
        }


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

    public function getRequestIdFromUser($fName): int
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE fName = :fName");
        $stmt->bindParam(':fName', $fName);
        $stmt->execute();
        $userId = $stmt->fetchColumn();
        
        $stmt = $this->db->prepare("SELECT id FROM requests WHERE user_fk = :user_fk");
        $stmt->bindParam(':user_fk', $userId);
        $stmt->execute();
        $requestId = $stmt->fetchColumn();
        return $requestId;
    }
}
