<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];
$result = $db->prepare("SELECT * FROM collection WHERE id=:id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {

  $type = $row['pay_type'];
  $amount = $row['amount'];
  $chq_no = $row['chq_no'];
  $bank = $row['bank'];
  $chq_date = $row['chq_date'];
  $sales_id = $row['invoice_no'];
  $pay_credit = $row['type'];
}

$f = "";

if ($pay_credit == "1") {
  $f = "disabled";
}
?>

<form method="POST" action="credit_collection_edit_save.php">

  <div class="row" style="display: block;">
    <div class="col-md-6">
      <div class="form-group">
        <label>Type</label>
        <input type="text" name="type" value="<?php echo $type ?>" class="form-control" autocomplete="off" disabled>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label>Amount</label>
        <input type="number" step=".01" name="amount" value="<?php echo $amount ?>" class="form-control" autocomplete="off" <?php echo $f; ?>>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label>Bank</label>
        <input type="text" name="bank" value="<?php echo $bank ?>" class="form-control" autocomplete="off">
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label>Chq No</label>
        <input type="text" name="chq_no" value="<?php echo $chq_no ?>" class="form-control" autocomplete="off">
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label>Chq Date</label>
        <input type="text" name="chq_date" value="<?php echo $chq_date ?>" class="form-control" autocomplete="off">
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="submit" style="margin-top: 23px;width: 100px;" value="Save" class="btn btn-info">
      </div>
    </div>

  </div>

</form>