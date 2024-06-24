<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");



$id = $_POST['id'];
$note = $_POST['note'];
$amount = $_POST['amount'];
$date = $_POST['date'];


$now = date('Y-m-d');

$result = $db->prepare("SELECT * FROM employee WHERE id='$id'");
$result->bindParam(':userid', $res);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['name'];
}


$sql = "INSERT INTO salary_advance (emp_id,name,amount,date,now,note) VALUES (?,?,?,?,?,?)";
$q = $db->prepare($sql);
$q->execute(array($id, $name, $amount, $date, $now, $note));


header("location: hr_salary_advance.php");
