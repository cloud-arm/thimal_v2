<?php

function query($sql, $path = "")
{
    include($path . 'connect.php');

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return "Data updated successfully!";
    } catch (PDOException $e) {
        return "Update failed: " . $e->getMessage();
    }
}
