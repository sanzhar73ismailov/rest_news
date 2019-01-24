<?php
include_once "config.php";

try {
	header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");
	
	 //$method = $_SERVER['REQUEST_METHOD'];
	 //echo run($_SERVER['REQUEST_METHOD']);
    echo run ($_REQUEST);
    //$api = new NewsApi();
    //echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}

function run($request){
	//print_r($method);
	$action = $request['action'];
	$id = $request['id'];


	$response = "";
	
	 switch ($action) {
		case 'get':
			if($id == 'all'){
				$data = getAllAnnouncements();
			} else {
				$data = getAnnouncement($id);
			}
			break;
		case 'add':
			$data = createAnnouncement($request);
			break;
		case 'update':
			$data = updateAnnouncement($id, $request);
			break;
		case 'delete':
			$data = deleteAnnouncement($id);
			break;
		default:
			return null;
        }
	return response($data, "200");
}

   function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . requestStatus($status));
        return json_encode($data);
    }
	function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }


function getAllAnnouncements() {
	try {
		$conn = new PDO(sprintf("mysql:host=%s;dbname=%s", HOST, DB), USER, PASS);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT * FROM data ORDER BY id DESC"); 
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
		return $stmt->fetchAll();
	} catch(PDOException $e) {
		return "Connection failed: " . $e->getMessage();
	}
}

function getAnnouncement($id) {
	try {
		$conn = new PDO(sprintf("mysql:host=%s;dbname=%s", HOST, DB), USER, PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT * FROM data WHERE id={$id}"); 
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
		return $stmt->fetchAll()[0];
	} catch(PDOException $e) {
		return "Connection failed: " . $e->getMessage();
	}
}

function createAnnouncement($request){
	try {
		$conn = new PDO(sprintf("mysql:host=%s;dbname=%s", HOST, DB), USER, PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO data (ann_date, ann_text, deleted) VALUES ('%s', '%s', '%s')";
		$sql = sprintf($sql, $request['ann_date'], trim($request['ann_text']), $request['deleted']);
       	$conn->exec($sql);
		return true;
	} catch(PDOException $e) {
		return "Creation failed: " . $e->getMessage();
	}
}
function updateAnnouncement($id, $request){
	try {
		$conn = new PDO(sprintf("mysql:host=%s;dbname=%s", HOST, DB), USER, PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE data SET ann_date='%s', ann_text='%s', deleted='%s' WHERE id='%s'";
		$sql = sprintf($sql, $request['ann_date'], trim($request['ann_text']), $request['deleted'], $id);
       	$conn->exec($sql);
		return true;
	} catch(PDOException $e) {
		return "Creation failed: " . $e->getMessage();
	}
}
function deleteAnnouncement($id){
	try {
		$conn = new PDO(sprintf("mysql:host=%s;dbname=%s", HOST, DB), USER, PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "DELETE FROM data WHERE id='{$id}'";
		$conn->exec($sql);
		return true;
	} catch(PDOException $e) {
		return "Creation failed: " . $e->getMessage();
	}
}



