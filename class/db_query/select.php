<?php

function select($table, $columns = "*", $where = "", $path = "")
{
    include($path . 'connect.php');

    $sql = "SELECT $columns FROM $table";

    if (!empty($where)) {
        $sql .= " WHERE " . $where;
    }

    try {
        $result = $db->prepare($sql);
        $result->execute();

        return $result;
    } catch (PDOException $e) {

        // create error respond 
        return array("status" => "failed", "message" => $e->getMessage());
    }
}

function select_query($sql, $path = "")
{
    include($path . 'connect.php');

    try {
        $result = $db->prepare($sql);
        $result->execute();

        return $result;
    } catch (PDOException $e) {

        // create error respond 
        return array("status" => "failed", "message" => $e->getMessage());
    }
}
