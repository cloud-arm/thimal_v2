<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];
$type = $_GET['type'];

if ($type == 'tbl_get') {
    $style = '';
    $result = $db->prepare("SELECT * FROM bulk_payment WHERE invoice_no =:id  ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $dll = $row['dll'];
        if ($dll == 1) {
            $style = 'opacity: 0.5;cursor: default;';
        } else {
            $style = '';
        } ?>

        <tr id="record_<?php echo $row['id']; ?>" style="<?php echo $style; ?>">
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['invoice_no']; ?></td>
            <td><?php echo $row['supplier_invoice']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo $row['forward_balance']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td> <?php if ($dll == 0) { ?><span onclick="dll_btn ('<?php echo $row['id']; ?>')" class="btn btn-danger" title="Click to Delete"> X</span> <?php } ?></td>
        </tr>
<?php
    }
}
