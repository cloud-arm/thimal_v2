<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$unit = $_GET['unit'];

if ($unit == 1) {

    $date = date("Y-m-d");
    $result = $db->prepare("SELECT * FROM employee ");
    $result->bindParam(':id', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $id = $row['id'];
        $con = 0;
        $checked = '';
        $res = $db->prepare("SELECT  * FROM attendance WHERE emp_id=:id AND date = '$date' ");
        $res->bindParam(':id', $id);
        $res->execute();
        for ($i = 0; $ro = $res->fetch(); $i++) {
            $con = $ro['id'];
        }
        if ($con > 0) {
            $checked = 'checked';
        } ?>

        <div class="col-md-3">
            <div class="form-group">
                <div class="input-group">
                    <label class="form-control"><?php echo ucfirst($row['username']); ?></label>
                    <input type="hidden" name="dll" id="dll" value="<?php echo $con; ?>">
                    <label class="input-group-addon right" style="cursor: pointer;">
                        <input type="checkbox" name="id" id="empid" value="<?php echo $row['id']; ?>" onclick="save_attendance('<?php echo $row['id']; ?>','<?php echo $con; ?>')" style="cursor: pointer;" <?php echo $checked; ?>>
                    </label>
                </div>
            </div>
        </div>
<?php  }
} ?>

<?php

if ($unit == 2) {

    $date = date('Y-m-d');

    $result = $db->prepare("SELECT * FROM attendance  WHERE date = '$date' ORDER BY id  ");
    $result->bindParam(':userid', $date);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) { ?>
        <tr class="record">
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['date'] ?></td>
            <td style="width: 5%;">
                <a href="#" id="<?php echo $row['id']; ?>" class="delbutton btn btn-danger btn-sm" title="Click to Delete">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
<?php    }
}

?>