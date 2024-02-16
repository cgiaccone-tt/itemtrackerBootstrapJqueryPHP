<?php
    /* $fName = $_POST['fName'];
    $item = $_POST['item']; */
    require 'classes/db.php';
    require 'classes/Request.php';
    $db = new db();

    if (isset($_GET['action']) && $_GET['action'] == 'getRequests') {
        $requestObj = new Request($db);
        $return['data'] = $requestObj->getRequests();
        $return = json_encode($return);
        error_log("reqest-".print_r($return, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
        echo $return;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'addRequest') {
        $requestObj = new Request($db);
        $requestObj->addRequest($_POST['fName'],$_POST['itemName']);
        $return['data'] = $requestObj->getRequests();
        $return = json_encode($return);
        error_log("reqest-".print_r($return, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
        echo $return;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'updateRequest') {
        $requestObj = new Request($db);
        $requestObj->updateRequest($_POST['id'], $_POST['fName'], $_POST['item']);
        $return['data'] = $requestObj->getRequests();
        $return = json_encode($return);
        error_log("reqest-".print_r($return, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
        echo $return;
    }
    
?>