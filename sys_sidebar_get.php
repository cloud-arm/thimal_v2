<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$unit = $_GET['unit'];
$val = $_GET['val'];

if ($unit == 1) {

    $result = $db->prepare("SELECT * FROM sys_sidebar WHERE main_id = :id AND type = 'sub1' AND sub = 1 ");
    $result->bindParam(':id', $val);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) { ?>
        <option value="<?php echo $row['id']; ?>"> <?php echo ucfirst($row['name']); ?> </option>
        <?php }
}

if ($unit == 2) {
    if ($val == 'sidebar') {

        $result = $db->prepare("SELECT * FROM sys_sidebar ORDER BY id DESC  ");
        $result->bindParam(':userid', $date);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) { ?>
            <option value="<?php echo $row['id']; ?>"> <?php echo ucfirst($row['name']); ?> (<?php echo $row['type']; ?>) </option>
        <?php }
    }

    if ($val == 'header') {

        $result = $db->prepare("SELECT * FROM sys_section   ");
        $result->bindParam(':userid', $date);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) { ?>
            <option value="<?php echo $row['id']; ?>"> <?php echo ucfirst($row['name']); ?> </option>
<?php }
    }
}
