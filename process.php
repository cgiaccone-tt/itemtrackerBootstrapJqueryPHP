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
        }else{
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
    
?>