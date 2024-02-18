<?php

use classes\db;



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
    public function insertSummary($userId): array 
    {
        $stmt = $this->db->prepare("
        SELECT r.id AS req_id, u.fName, it.id AS type_id, GROUP_CONCAT( i.id SEPARATOR', ') AS i
        FROM requests r 
        LEFT JOIN type_requests tr ON r.id = tr.request_fk 
        LEFT JOIN item_type it ON it.id = tr.type_fk 
        LEFT JOIN items i ON i.id = tr.item_fk
        LEFT JOIN users u ON u.id = r.user_fk
        WHERE u.id = :id
        GROUP BY fName, it.type, DAY(r.dt_requested);
        ");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $typeRequestArr = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("INSERT INTO summary (request_fk, requested_by, items) VALUES (:request_fk, :requested_by, :items)");
        $stmt->bindParam(':request_fk', $id);
        $stmt->bindParam(':requested_by', $this->reqested_by);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;

    }
}