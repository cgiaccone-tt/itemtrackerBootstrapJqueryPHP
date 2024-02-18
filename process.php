<?php

    require 'classes/db.php';
    require 'classes/Request.php';
    require_once 'classes/User.php';
    require_once 'classes/Item.php';
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
        $userObj = new User($db);
        if (!$userObj->getUserByName($_POST['fName'])) {
            $requestObj->addRequest($_POST['fName'],$_POST['itemName']);
        }else{
            $requestId = $requestObj->getRequestIdFromUser($_POST['fName']);
            //$itemType = $itemObj->getItemType($_POST['itemName']);
            $requestObj->updateRequest($requestId,$_POST['fName'],$_POST['itemName'], false);
        }
        
        $return['data'] = $requestObj->getRequests();
        $return = json_encode($return);
        
        echo $return;
        
    }

    if (isset($_POST['action']) && $_POST['action'] == 'updateRequest') {
        $requestObj = new Request($db);
        $requestObj->updateRequest($_POST['id'],$_POST['fName'],$_POST['itemName'], true);

        $return['data'] = $requestObj->getRequests();
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
    
?>