<?php

$db = new db();

if (isset($_GET['action']) && $_GET['action'] == 'getRequest') {
    $requestObj = new Request($db);
    $return = $requestObj->getRequest($_GET['id']);
    $return = json_encode($return);

    echo $return;
}

if (isset($_GET['action']) && $_GET['action'] == 'getRequests') {
    $requestObj = new Request($db);
    $return['data'] = $requestObj->getRequests();
    $return = json_encode($return);

    echo $return;
}

if (isset($_POST['action']) && $_POST['action'] == 'addRequest') {
    $requestObj = new Request($db);
    $userObj = new User($db);


    if (isset($_POST['fName']) && !$userObj->getUserByName($_POST['fName'])) {
        $requestObj->addRequest($_POST['fName'], $_POST['itemName']);
    } else {
        $itemObj = new Item($db);
        $type_id = $itemObj->getItemType($_POST['itemName'][0]);
        $request_id = $requestObj->getRequestIdFromUser($_POST['fName']);
        $requestObj->updateRequest($request_id, $_POST['fName'], $_POST['itemName'], $type_id);
    }

    $return['data'] = $requestObj->getRequests();
    $return = json_encode($return);

    echo $return;
}

if (isset($_POST['action']) && $_POST['action'] == 'updateRequest') {
    $requestObj = new Request($db);

    $requestObj->updateRequest($_POST['id'], $_POST['fName'], $_POST['itemName'], $_POST['type']);

    $return['data'] = $requestObj->getRequests();;

    $return = json_encode($return);

    echo $return;
}

if (isset($_GET['action']) && $_GET['action'] == 'getItems') {
    $itemObj = new Item($db);
    $return = $itemObj->getItems();
    $return = json_encode($return);

    echo $return;
}

if (isset($_GET['action']) && $_GET['action'] == 'getItemsOfSameType') {
    $itemObj = new Item($db);
    $return = $itemObj->getAllItemsNotOfSameType($_GET['id']);
    $return = json_encode($return);

    echo $return;
}



/**
 * PDO SINGLETON CLASS
 *  
 * @author Tony Landis
 * @link http://www.tonylandis.com
 * @license Use how you like it, just please don't remove or alter this PHPDoc
 */
class db
{
    /**
     * The singleton instance
     * 
     */
    static private $PDOInstance;

    /**
     * Creates a PDO instance representing a connection to a database and makes the instance available as a singleton
     * 
     * @param string $dsn The full DSN, eg: mysql:host=localhost;dbname=testdb
     * @param string $username The user name for the DSN string. This parameter is optional for some PDO drivers.
     * @param string $password The password for the DSN string. This parameter is optional for some PDO drivers.
     * @param array $driver_options A key=>value array of driver-specific connection options
     * 
     * @return PDO
     */
    public function __construct()
    {
        $dsn = 'mysql:host=localhost;dbname=ascendionCG';
        $username = 'root';
        $password = '';
        $driver_options = array();
        if (!self::$PDOInstance) {
            try {
                self::$PDOInstance = new PDO($dsn, $username, $password, $driver_options);
            } catch (PDOException $e) {
                die("PDO CONNECTION ERROR: " . $e->getMessage() . "<br/>");
            }
        }
        return self::$PDOInstance;
    }


    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $name Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function lastInsertId($name)
    {
        return self::$PDOInstance->lastInsertId($name);
    }

    /**
     * Prepares a statement for execution and returns a statement object 
     *
     * @param string $statement A valid SQL statement for the target database server
     * @param array $driver_options Array of one or more key=>value pairs to set attribute values for the PDOStatement obj 
returned  
     * @return PDOStatement
     */
    public function prepare($statement, $driver_options = false)
    {
        if (!$driver_options) $driver_options = array();
        return self::$PDOInstance->prepare($statement, $driver_options);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function query($statement)
    {
        return self::$PDOInstance->query($statement);
    }
}


class User
{
    public $id; /* user id */
    public $fName; /* user first name */
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
     * getUser
     * 
     * get user by id
     *
     * @param int $id
     * @return array
     */
    public function getUser($id): array
    {
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
    public function getUserByName($fName): int
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE fName = :fName");
        $stmt->bindParam(':fName', $fName);
        $stmt->execute();
        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $result['id'];
        } else {
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
    public function addUser($fName): int
    {
        $stmt = $this->db->prepare("INSERT INTO users (fName) VALUES (:fName)");
        $stmt->bindParam(':fName', $fName);
        $stmt->execute();
        return $this->db->lastInsertId('users');
    }
}


class Item
{
    public $id; /* item id */
    public $item; /* item name */
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
     * getItemObj
     * 
     * get item by id and return as an object
     *
     * @param [type] $id
     * @return object
     */

    public function getItemObj($id): object
    {
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
    public function getItems(): array
    {
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
    public function getAllItemsNotOfSameType($id): array
    {
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
    public function getItemObjects(array $item): array
    {

        foreach ($item as $key => $value) {

            $result[] = $this->getItemObj($value['id']);
        }

        return $result;
    }

    /**
     * getItemType
     * 
     * get item type of an item by its id
     *
     * @param int $id
     * @return int
     */
    public function getItemType($id): int
    {
        $stmt = $this->db->prepare("SELECT item_type FROM items WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }
}


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
        if (!is_array($item)) {
            $item = array();
        }
        foreach ($item as $value) {
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
        foreach ($item as $value) {
            $itemArr[] = array("id" => $value, "item_type" => $type_id);
        }

        //remove all items of the same type submitted from the current items
        foreach ($currentRequestItemsArr as $key => $value) {
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

    /**
     * getItemsByRequest
     * 
     * Get all items associated with a request id
     * Return the item id and item type in an array
     *
     * @param integer $id
     * @return array
     */
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

    /**
     * getRequestIdFromUser
     * 
     * @param mixed $fName
     * @return int
     */

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


class Summary
{
    public $id; /* summary id */
    public $req_id; /* request id */
    public $reqested_by; /* Name of person requesting */
    public $items; /* item multidimension array of [item_type:[items]] */
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
     * insertSummary
     * 
     * insert summary by id
     *
     * @param int $id
     * @return array
     */
    public function insertSummary($request_id): bool
    {
        error_log("summary-request_id-" . print_r($request_id, true) . "\n\n", 3, "C:\cg\work\ascendion\logs\\test.log");
        //get all items in this request with other summary information
        $stmt = $this->db->prepare("
        SELECT r.id AS req_id, u.fName, it.id AS type_id, GROUP_CONCAT( i.id SEPARATOR', ') AS i
        FROM requests r 
        LEFT JOIN type_requests tr ON r.id = tr.request_fk 
        LEFT JOIN item_type it ON it.id = tr.type_fk 
        LEFT JOIN items i ON i.id = tr.item_fk
        LEFT JOIN users u ON u.id = r.user_fk
        WHERE r.id = :id
        GROUP BY fName, it.type, DAY(r.dt_requested);
        ");
        $stmt->bindParam(':id', $request_id);
        $stmt->execute();
        $typeRequestArr = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("summary-typeRequestArr-" . print_r($typeRequestArr, true) . "\n\n", 3, "C:\cg\work\ascendion\logs\\test.log");

        //get the name of the person requesting
        $userName = $typeRequestArr[0]['fName'];

        //create a multidimensional array of objects simulating the json like summary format
        $items = [];
        foreach ($typeRequestArr as $key => $value) {
            $obj = new stdClass();
            $obj->type = $value['type_id'];
            $obj->items = explode(',', $value['i']);
            $items[] = $obj;
        }

        $itemsJson = json_encode($items);

        //remove extraneous characters from the json string to match the format specified in the requirements
        $itemsJson = str_replace('"type":', '', $itemsJson);
        $itemsJson = str_replace(',"items":', ',', $itemsJson);
        $itemsJson = str_replace('"', '', $itemsJson);
        $itemsJson = str_replace(' ', '', $itemsJson);


        //delete any existing summary for this request and insert the new summary
        $stmt = $this->db->prepare("DELETE FROM summary WHERE req_id = :request_id");
        $stmt->bindParam(':request_id', $request_id);
        $stmt->execute();

        $stmt = $this->db->prepare("INSERT INTO summary (req_id, requested_by, items) VALUES (:request_id, :requested_by, :items)");
        $stmt->bindParam(':request_id', $request_id);
        $stmt->bindParam(':requested_by', $userName);
        $stmt->bindParam(':items', $itemsJson);
        $stmt->execute();

        return true;
    }
}