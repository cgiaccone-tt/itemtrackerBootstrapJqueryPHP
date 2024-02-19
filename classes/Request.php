<?php

require 'classes/User.php';
require 'classes/Item.php';
require 'classes/summary.php';


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
        it.id, r.id AS action, r.dt_requested
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
    public function addRequest(string $fName, array $item): int
    {
        //instantiate user object
        $userObj = new User($this->db);
        $summaryObj = new Summary($this->db);

        //if user does not exist, add user
        if (!$userId = $userObj->getUserByName($fName)) {
            $userId = $userObj->addUser($fName);
        }

        $stmt = $this->db->prepare("INSERT INTO requests (user_fk) VALUES (:user)");
        $stmt->bindParam(':user', $userId);
        $stmt->execute();

        $request = $this->db->lastInsertId('requests');

        //get item for item type determination
        $itemObj = new Item($this->db);
        $type_id = $itemObj->getItemType($item[0]);
        $itemArr = [];
        if(!is_array($item)){
            $item = array();
        }
        foreach ($item AS $value) {
            $itemArr[] = array("id" => $value, "item_type" => $type_id);
        }

        $this->createRequestItemArray($request, $itemArr);

        //insert summary
        $summaryObj = new Summary($this->db);
        $summaryObj->insertSummary($request);

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
    public function createRequestItemArray(int $request, array $item): void
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
     * update a request in the type_requests table
     *
     * @param int $id
     * @param string $fName
     * @param string $item
     * @param int $type_id
     * @return bool
     */
    public function updateRequest(int $id, string $fName, array $item, int $type_id): bool
    {
        //$id is request id

        //get all items for this request
        $currentRequestItemsArr = $this->getItemsByRequest($id);

        //delete all items for this user (1 user per request id) so that we can repopulate the items
        $stmt = $this->db->prepare("DELETE FROM type_requests WHERE request_fk = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        //create an array of items that were submitted and match the structure of $currentRequestItemsArr
        $itemArr = [];
        if (!is_array($item)) {
            $item = array();
        }
        foreach ($item AS $value) {
            $itemArr[] = array("id" => $value, "item_type" => $type_id);
        }

        //remove all items of the same type submitted from the current items
        foreach ($currentRequestItemsArr AS $key => $value) {
            if ($value['item_type'] == $type_id) {
                unset($currentRequestItemsArr[$key]);
            }
        }

        //merge the current items minus the matching type with the new items of that type
        $finalItemsArr = array_merge($currentRequestItemsArr, $itemArr);

       
        //repopulate the items
        $this->createRequestItemArray($id, $finalItemsArr);


        //instantiate user object
        $userObj = new User($this->db);
        if (!$userId = $userObj->getUserByName($fName)) {
            $userId = $userObj->addUser($fName);
            $this->updateUserInReqest($id, $userId);
        }

        //insert summary
        $summaryObj = new Summary($this->db);
        $summaryObj->insertSummary($id);


        return true;
    }

    public function getItemsByRequest(int $id): array
    {
        $stmt = $this->db->prepare("
        SELECT i.id, i.item_type FROM type_requests tr 
        LEFT JOIN items i ON i.id = tr.item_fk
        WHERE request_fk = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if (!$result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
            return [];
        }
        return $result;
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
