<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];
$type = $_GET['type'];

if ($type == 'inv_get') {

    $result = $db->prepare("SELECT * FROM supply_payment WHERE dll=0 AND supply_id=:id AND pay_type='Credit' AND credit_balance>0 GROUP BY supplier_invoice DESC");
    $result->bindParam(':id', $id);
    $result->execute(); ?>
    <option value="0" selected disabled> select invoice </option>
    <?php for ($i = 0; $row = $result->fetch(); $i++) { ?>
        <option value="<?php echo $row['invoice_no']; ?>"> <?php echo $row['supplier_invoice']; ?> </option>
        <?php }
} else 

if ($type == 'tbl_get') {
    $style = '';
    $result = $db->prepare("SELECT * FROM supply_payment WHERE dll=0 AND invoice_no = '$id' ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $t = $row['type'];
        $dll = $row['dll'];
        $pt = $row['pay_type'];
        if ($dll == 1) {
            $style = 'opacity: 0.5;cursor: default;';
        } else {
            $style = '';
        }
        if ($pt != 'Credit_Note') { ?>

            <tr id="record_<?php echo $row['id']; ?>" style="<?php echo $style; ?>">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['invoice_no']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $pt; ?></td>
                <td><?php echo $row['chq_no']; ?></td>
                <td><?php echo $row['chq_date']; ?></td>
                <?php if ($pt == 'Chq') { ?>
                    <td><?php echo $row['chq_bank']; ?></td>
                <?php } else { ?>
                    <td><?php echo $row['bank_name']; ?></td>
                <?php } ?>
                <td><?php echo $row['acc_no']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td> <?php if ($t == 'Credit_payment' & $dll == 0) { ?><span onclick="dll_btn ('<?php echo $row['id']; ?>')" class="btn btn-danger" title="Click to Delete"> X</span> <?php } ?></td>
            </tr>
    <?php
        }
    }
} else 

if ($type == 'bill_get') {
    $bill = 0;
    $blc = 0;
    $result = $db->prepare("SELECT * FROM supply_payment WHERE dll=0 AND invoice_no = '$id' AND pay_type='Credit' ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $bill = $row['amount'];
        $blc = $row['credit_balance'];
    }

    ?>
    <h4>Bill Amount <small class="ms-2">Rs.</small> <?php echo number_format($bill, 2); ?> </h4>
    <h5>Balance <small class="ms-2">Rs.</small> <?php echo number_format($blc, 2); ?> </h5>
    <input type="hidden" id="blc" value=" <?php echo $blc; ?>">
    <input type="hidden" id="cr_blc" value=" ">
<?php
} else

if ($type == 'cred_get') {

    $result = $db->prepare("SELECT * FROM supply_payment WHERE dll=0 AND supply_id=:id AND pay_type='Credit_Note' AND credit_balance>0 ");
    $result->bindParam(':id', $id);
    $result->execute(); ?>
    <option value="0" selected disabled> Select Credit Note</option>
    <?php for ($i = 0; $row = $result->fetch(); $i++) { ?>
        <option value="<?php echo $row['id']; ?>"> <?php echo $row['invoice_no']; ?> __ Rs. <?php echo $row['credit_balance']; ?> </option>
<?php }
} else

if ($type == 'cred_set') {

    $blc = 0;
    $result = $db->prepare("SELECT * FROM supply_payment WHERE id=:id");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $blc = $row['credit_balance'];
    }

    printf($blc);
}

?>