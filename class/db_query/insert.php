<?php

function insert($table, $data, $path = "", $file = "")
{
    include($path . 'connect.php');

    $keys = implode(', ', array_keys($data['data']));
    $values = ':' . implode(', :', array_keys($data['data']));

    $sql = "INSERT INTO $table ($keys) VALUES ($values)";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($data['data']);

        $res = array(
            "status" => "success",
            "message" => "",
        );
        return $res;
    } catch (PDOException $e) {

        // create error json log
        $json = array(
            "file" => $file,
            "table" => $table,
            "message" => $e->getMessage(),
            "date" => date("Y-m-d"),
            "time" => date('H:i:s'),
        );
        // log_init('error', $json, 'json', $path);

        // create whatsapp alert 
        // $contact = '94762020312';
        $contact = '94772955659';
        $message = "Please check error log..!    ( File: " . $file . " )  ( Message: " . $e->getMessage() . " )  ( Table Name: "  . $table . " )";
        // whatsApp($contact, $message);

        // Create txt log
        //$content = "cloud_id: 0, app_id: " . $data['data']['app_id'] . ", data_id: " . $data['other']['data_id'] . ", data_name: " . $data['other']['data_name'] . ", status: failed, message: " . $e->getMessage() . ", Date: " . date('Y-m-d') . ", Time: " . date('H:s:i');
        // log_init($table, $content, 'txt', $path);

        $res = array(
            "status" => "failed",
            "message" => $e->getMessage(),
        );
        return $res;
    }
}
