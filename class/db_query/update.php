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

        // create error respond 
        return array("status" => "failed", "message" => $e->getMessage());
    }
}
