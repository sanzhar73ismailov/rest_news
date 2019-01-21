<?php
	header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");
    $data = [];
    $data[] = 1;
    $data[] = 2;
    echo json_encode($data);

?>