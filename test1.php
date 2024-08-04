<?php
session_start();
include('connect.php');
include("config.php");
date_default_timezone_set("Asia/Colombo");


$load_id=1;
echo select_item('loading','action','transaction_id='.$load_id);