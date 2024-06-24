<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$time = date('H.i');
$date = date('Y-m-d');


$result = $db->prepare("SELECT * FROM employee ");
$result->bindParam(':id', $date);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $id = $row['id'];
    $name = $row['name'];
    $dll = $_POST['dll_' . $id];

    $emp = 0;
    if (isset($_POST['empid_' . $id])) {
        $emp = $_POST['empid_' . $id];

        $con = 0;
        $res = $db->prepare("SELECT  * FROM attendance WHERE emp_id=:id AND date = '$date' ");
        $res->bindParam(':id', $id);
        $res->execute();
        for ($i = 0; $ro = $res->fetch(); $i++) {
            $con = $ro['id'];
        }

        if ($con == 0) {

            $sql = "INSERT INTO attendance (emp_id,name,date,time) VALUES (?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($id, $name, $date, $time));
        }
    } else {
        $res = $db->prepare("DELETE FROM attendance WHERE  id= :id  ");
        $res->bindParam(':id', $dll);
        $res->execute();
    }
}


header("location: hr_attendance.php");
