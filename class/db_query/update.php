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
        return "Data updated successfully!";
    } catch (PDOException $e) {
        return "Update failed: " . $e->getMessage();
    }
}
