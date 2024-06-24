<?php
session_start();
date_default_timezone_set("Asia/Colombo");
include('config.php');

$user_id = $_SESSION['SESS_MEMBER_ID'];

$id = $_POST['id'];
$old = $_POST['old'];
$new = $_POST['new'];

if ($user_id == $id) {
    if ($old == $new) {
        query("UPDATE user  SET password='".$new."' WHERE id= '".$id."' ");
        header("location: ../../../index.php"); 
    } else {
        header("location: index.php?user_err=Invalid_User_Password");
    }
} else {
    header("location: index.php?user_err=Invalid_User_ID");
}
