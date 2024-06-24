<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$date = date("Y-m-d");
$time = date("h:i:sa");
$action = "load";

$lorry_id = $_POST['lorry'];
$driver = $_POST['driver'];
$root = $_POST['root'];
$helper1 = $_POST['h1'];
$helper2 = $_POST['h2'];
$helper3 = $_POST['h3'];


$result = $db->prepare("SELECT * FROM lorry WHERE lorry_id= :id");
$result->bindParam(':id', $lorry_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $lorry = $row['lorry_no'];
}


$result = $db->prepare("SELECT * FROM user WHERE EmployeeId= :id");
$result->bindParam(':id', $driver);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $uid = $row['id'];
}

$result = $db->prepare("SELECT * FROM root WHERE root_id= :id  ");
$result->bindParam(':id', $root);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $root_name = $row['root_name'];
}

$result = $db->prepare("SELECT * FROM employee WHERE id= :id  ");
$result->bindParam(':id', $driver);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $dr_name = $row['name'];
}


$term = 0;
$result = $db->prepare("SELECT * FROM loading WHERE lorry_id= :id AND action='load' ");
$result->bindParam(':id', $lorry_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $tran = $row['transaction_id'];
}

$count = 0;

if ($driver > 0) {
    $count = $count + 1;
}

if ($helper1 > 0) {
    $count = $count + 1;
}

if ($helper2 > 0) {
    $count = $count + 1;
}

if ($helper3 > 0) {
    $count = $count + 1;
}

//$$$$$$$$$$$$$$$$         edit terms      $$$$$$$$$$$$$$$$$$$$$$$
if ($tran >= 1) {
    echo $lorry . " දරන ලොරිය සදහ දැනටමත් loading එකක් තිබෙන බැවින් පලමුව එම loading එක unload කරන්න";
} else {
    $empty = 0;

    // query
    $sql = "INSERT INTO loading (lorry_no,lorry_id,root,root_id,date,action,loading_time,driver,helper1,helper2,helper3,rep,emp_count) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($lorry, $lorry_id, $root_name, $root, $date, $action, $time, $driver, $helper1, $helper2, $helper3, $dr_name, $count));




    $result = $db->prepare("SELECT * FROM loading WHERE lorry_id= :id AND action='load' ");
    $result->bindParam(':id', $lorry_id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $tran = $row['transaction_id'];
    }


    $action = "load";
    $sql = "UPDATE lorry 
        SET action=?
		WHERE lorry_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($action, $lorry_id));

    $sql = "UPDATE lorry 
        SET loading_id=?
		WHERE lorry_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($tran, $lorry_id));



    $sql = "UPDATE lorry 
        SET user_id=?
		WHERE lorry_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($uid, $lorry_id));


    header("location: loading_2.php?id=$tran");
}
