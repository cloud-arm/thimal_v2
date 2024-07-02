<?php


function update($table, $data, $where, $path = "")
{
    include($path . 'connect.php');

    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "$key = '$value'";
    }
    $set = implode(", ", $set);

    $sql = "UPDATE $table SET $set WHERE $where";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute();

        // create success respond 
        return array("status" => "success", "message" => "Data updated successfully..!");
    } catch (PDOException $e) {

        // Get the database name
        $stmt = $db->query("SELECT DATABASE()");
        $dbName = $stmt->fetchColumn();

        // create message
        $message = "Data update failed..!    ( File: " . $e->getFile() . " On line: " . $e->getLine() . " )  ( Message: " . $e->getMessage() . " )  ( Table Name: "  . $table . " )  ( Database Name: "  . $dbName . " )";

        // create discord alert
        discord($message);

        // create error respond 
        return array("status" => "failed", "message" => $e->getMessage());
    }
}
