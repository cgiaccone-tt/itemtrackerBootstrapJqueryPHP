<?php

    require 'classes/db.php';
    require 'classes/Request.php';
    $db = new db();

    if (isset($_GET['action']) && $_GET['action'] == 'getRequest') {
        $requestObj = new Request($db);
        $return = $requestObj->getRequest($_GET['id']);
        $return = json_encode($return);
        error_log("return-".print_r($return, true)."\n\n", 3,"C:\cg\work\ascendion\logs\\test.log");
        
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
        $requestObj->addRequest($_POST['fName'],$_POST['itemName']);
        $return['data'] = $requestObj->getRequests();
        $return = json_encode($return);
        
        echo $return;
        
    }

    if (isset($_POST['action']) && $_POST['action'] == 'updateRequest') {
        $requestObj = new Request($db);
        $requestObj->updateRequest($_POST['id'],$_POST['fName'],$_POST['itemName']);

        $return['data'] = $requestObj->getRequests();
        $return = json_encode($return);
        
        echo $return;        
    }
    
?>