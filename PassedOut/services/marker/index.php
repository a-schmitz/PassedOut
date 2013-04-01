<?php
header('Content-type: application/json');

require("../../config/db.php");
require('../../classes/Database.class.php');
require('../../classes/Login.class.php');

$db = new Database();
$login = new Login($db);

function sendError($message) {
    echo json_encode(array('success' => false, 'errormsg' => $message));
}

function sendSuccess($data) {
    echo json_encode(array('success' => true, 'data' => $data));
}


// TODO refactor / remove duplicated code
switch($_SERVER['REQUEST_METHOD']){
    case 'GET': { // select
            $marker = array();
            if ($login->isUserLoggedIn()) {
                $stmt = $db->getDatabaseConnection()->prepare("select guid, lat, lng, title, description from marker where user_id = ?");
                if ($stmt===false) {
                    sendError($db->getDatabaseConnection()->error);
                    break;
                }
                
                $userid = $login->getUserId();
                
                if ($stmt->bind_Param("i", $userid)===false) {
                    sendError($stmt->error);
                    break;
                }  
                
                if(!$stmt->execute()) {
                    sendError($stmt->error);
                    break;
                }
                
                $stmt->bind_result($guid, $lat, $lng, $title, $description);
                
                while ($stmt->fetch()) {
                    array_push($marker, array('guid' => $guid, 'lat' => $lat, 'lng' => $lng, 'title' => $title, 'description' => $description));
                }
            }
            
            sendSuccess(array('marker'=>$marker));
            break; 
        }
    case 'POST': { // insert
            if ($login->isUserLoggedIn()) {
                if (!isset($_POST['guid']) || empty($_POST['guid'])) {
                    sendError('ArgumentException: guid.');
                    break;
                }
                if (!isset($_POST['lat'])) {
                    sendError('ArgumentException: lat.');
                    break;
                }
                if (!isset($_POST['lng'])) {
                    sendError('ArgumentException: lng.');
                    break;
                }
                
                $stmt = $db->getDatabaseConnection()->prepare("insert into marker (user_id, guid, lat, lng, title, description) values (?,?,?,?,?,?)");
                if ($stmt===false) {
                    sendError($db->getDatabaseConnection()->error);
                    break;
                }

                $userid = $login->getUserId();
                $guid = $_POST['guid'];
                $lat = $_POST['lat'];
                $lng = $_POST['lng'];
                $title = $_POST['title'];
                $description = $_POST['description'];                    
                
                if ($stmt->bind_Param("isssss", $userid, $guid, $lat, $lng, $title, $description)===false) {
                    sendError($stmt->error);
                    break;
                }                
               
                if(!$stmt->execute()) {
                    sendError($stmt->error);
                    break;
                }
            }
            
            echo sendSuccess();
            break; 
        }
    case 'PUT': { // update
            if ($login->isUserLoggedIn()) {                
                // no built-in $_PUT so we fake it
                parse_str(file_get_contents("php://input"), $_PUT);
                
                if (!isset($_PUT['guid']) || empty($_PUT['guid'])) {
                    sendError('ArgumentException: guid.');
                    break;
                }
                if (!isset($_PUT['lat'])) {
                    sendError('ArgumentException: lat.');
                    break;
                }
                if (!isset($_PUT['lng'])) {
                    sendError('ArgumentException: lng.');
                    break;
                }
                
                $stmt = $db->getDatabaseConnection()->prepare("UPDATE marker SET lat = ?, lng = ?, title = ?, description = ? WHERE user_id = ? and guid = ?");
                if ($stmt===false) {
                    sendError($db->getDatabaseConnection()->error);
                    break;
                }

                $userid = $login->getUserId();
                $guid = $_PUT['guid'];
                $lat = $_PUT['lat'];
                $lng = $_PUT['lng'];
                $title = $_PUT['title'];
                $description = $_PUT['description'];                    
                
                if ($stmt->bind_Param("ssssis", $lat, $lng, $title, $description, $userid, $guid)===false) {
                    sendError($stmt->error);
                    break;
                }                
               
                if(!$stmt->execute()) {
                    sendError($stmt->error);
                    break;
                }
            }
            
            echo sendSuccess();
            break; 
        }        
    case 'DELETE': { // delete
        if ($login->isUserLoggedIn()
            ) {                
                // no built-in $_DELETE so we fake it
                parse_str(file_get_contents("php://input"), $_DELETE);
                
                if (!isset($_DELETE['guid']) || empty($_DELETE['guid'])) {
                    sendError('ArgumentException: guid.');
                    break;
                }
                               
                $stmt = $db->getDatabaseConnection()->prepare("DELETE FROM marker WHERE user_id = ? and guid = ?");
                if ($stmt===false) {
                    sendError($db->getDatabaseConnection()->error);
                    break;
                }

                $userid = $login->getUserId();
                $guid = $_DELETE['guid'];                 
                
                if ($stmt->bind_Param("is", $userid, $guid)===false) {
                    sendError($stmt->error);
                    break;
                }                
                
                if(!$stmt->execute()) {
                    sendError($stmt->error);
                    break;
                }
            }
        
        echo sendSuccess();
        break; 
        }
}