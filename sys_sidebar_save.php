<?php
session_start();
date_default_timezone_set("Asia/Colombo");
include('connect.php');


$unit = $_POST['unit'];

if ($unit == 1) {

    if (isset($_POST['icon'])) {
        $name = $_POST['name'];
        $icon = $_POST['icon'];
        $type = $_POST['type'];
        $link = $_POST['link'];

        if ($type == 'sub1') {
            $main = $_POST['main'];
        } else if ($type == 'sub2') {
            $main = $_POST['sub1'];
        } else {
            $main = 0;
        }

        $sub = 0;
        if (isset($_POST['sub'])) {
            $sub = $_POST['sub'];
        }

        $result = $db->prepare("SELECT * FROM sys_icon WHERE sn=:id ");
        $result->bindParam(':id', $icon);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $icon_name = $row['name'];
        }

        $order_id = 1;
        $result = $db->prepare("SELECT MAX(order_id) FROM sys_sidebar WHERE order_id < 150 ");
        $result->bindParam(':id', $menu);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $order_id = $row['MAX(order_id)'] + 1;
        }

        $sec_head = 'name,icon_id,icon,link,type,main_id,action,order_id,sub';
        $sec_act = ':name, :icon_id, :icon, :link, :type, :main_id, :action, :order_id, :sub';
        $sec_val = ["name" => $name, "icon_id" => $icon, "icon" => $icon_name, "link" =>  $link, "type" =>  $type, "main_id" =>  $main, "action" => 1, "order_id" => $order_id, "sub" => $sub];

        $result = $db->prepare("SELECT * FROM sys_section WHERE action = 1  ");
        $result->bindParam(':userid', $date);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {

            if (isset($_POST[$row['name']])) {

                $sec_head .= ',' . $row['name'];
                $sec_act .= ', :' . $row['name'];
                $sec_val[$row['name']] = $_POST[$row['name']];
            }
        }

        $sql = "INSERT INTO sys_sidebar ($sec_head) VALUES ($sec_act)";
        $ql = $db->prepare($sql);
        $ql->execute($sec_val);

        header("location: sys_sidebar.php?end=4");
    } else {
        header("location: sys_sidebar.php?end=1&err=1");
    }
}

if ($unit == 2) {

    $name = $_POST['name'];

    $sql = "INSERT INTO sys_icon (name,action) VALUES (?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($name, 1));

    header("location: sys_sidebar.php?end=1");
}

if ($unit == 3) {

    $name = $_POST['name'];
    $link = $_POST['link'];

    $sql = "INSERT INTO sys_section (name,link,action) VALUES (?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($name, $link, 1));

    $sql = "ALTER TABLE `sys_sidebar` ADD `$name` INT NOT NULL";
    $q = $db->prepare($sql);
    $q->execute(array());

    header("location: sys_sidebar.php?end=1");
}

if ($unit == 4) {

    $menu = $_POST['menu'];
    $type = $_POST['type'];
    $level = $_POST['level'];
    $section = $_POST['section'];

    if ($section == 'sidebar') {

        $result = $db->prepare("SELECT * FROM sys_sidebar WHERE id=:id ");
        $result->bindParam(':id', $menu);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $name = $row['name'];
        }
    }

    if ($section == 'header') {

        $result = $db->prepare("SELECT * FROM sys_section WHERE id=:id ");
        $result->bindParam(':id', $menu);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $name = $row['name'];
        }
    }

    $sql = "INSERT INTO sys_permission_arm (menu_id,menu_name,type,user_level,user_id,section,action) VALUES (?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($menu, $name, $type, $level, 0, $section, 1));

    header("location: sys_sidebar.php?end=4");
}

if ($unit == 5) {

    $from = $_POST['from'];
    $id = $_POST['id'];


    $result = $db->prepare("SELECT * FROM sys_sidebar WHERE id = '$id'  ");
    $result->bindParam(':id', $to);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $to = $row['order_id'];
    }

    $a = $to;
    $result = $db->prepare("SELECT * FROM sys_sidebar WHERE id != '$from' AND order_id BETWEEN '$to' AND '149' ORDER BY order_id ");
    $result->bindParam(':id', $to);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {

        $sql = "UPDATE sys_sidebar SET order_id = ? WHERE id = ?";
        $q = $db->prepare($sql);
        $q->execute(array(++$a, $row['id']));
    }

    $sql = "UPDATE sys_sidebar SET order_id = ? WHERE id = ?";
    $q = $db->prepare($sql);
    $q->execute(array($to, $from));

    header("location: sys_sidebar.php");
}

if ($unit == 6) {

    $result = $db->prepare("SELECT id FROM sys_sidebar WHERE order_id < 149 ");
    $result->bindParam(':id', $to);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $sql = "UPDATE sys_sidebar SET order_id = ? WHERE id = ?";
        $q = $db->prepare($sql);
        $q->execute(array($row['id'], $row['id']));
    }

    header("location: sys_sidebar.php");
}
