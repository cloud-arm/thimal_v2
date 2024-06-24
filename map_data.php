<?php
include('connect.php');

// Fetch GPS data from the 'gps_data' table
$gpsDataFromTable = array(
    array('lat' => 6.043414, 'lng' => 80.245659, 'name' => 'Narangoda', 'icon' => '', 'date' => '', 'time' => '')
);

// Fetch GPS data from the 'user' table
$result = $db->prepare("SELECT * FROM user");
$result->execute();
$gpsDataFromUserTable = $result->fetchAll();

// Merge both sets of GPS data
$mergedGPSData = array_merge($gpsDataFromTable, $gpsDataFromUserTable);

// Output merged GPS data as JSON
header('Content-Type: application/json');
echo json_encode($mergedGPSData);
