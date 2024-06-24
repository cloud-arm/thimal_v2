<?php

function select($table, $columns = "*", $where = "")
{
    include('connect.php');

    $sql = "SELECT $columns FROM $table";

    if (!empty($where)) {
        $sql .= " WHERE " . $where;
    }

    try {
        $result = $db->prepare($sql);
        $result->execute();
        return $result;
    } catch (PDOException $e) {
        echo "Selection failed: " . $e->getMessage();
        return false;
    }
}

function select_query($sql){
    include('connect.php');

    try {
        $result = $db->prepare($sql);
        $result->execute();
        return $result;
    } catch (PDOException $e) {
        echo "Selection failed: " . $e->getMessage();
        return false;
    }
}