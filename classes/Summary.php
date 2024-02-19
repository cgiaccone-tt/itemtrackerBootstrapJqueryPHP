<?php


class Summary {
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
    public function __construct($db) {
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
        error_log("summary-request_id-".print_r($request_id,true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
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

        error_log("summary-typeRequestArr-".print_r($typeRequestArr,true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");

        //get the name of the person requesting
        $userName = $typeRequestArr[0]['fName'];

        //create a multidimensional array of objects simulating the json like summary format
        $items = [];
        foreach ($typeRequestArr AS $key => $value) {
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