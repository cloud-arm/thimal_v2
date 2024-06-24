<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
date_default_timezone_set("Asia/Colombo");
?>

<body class="hold-transition skin-yellow sidebar-mini">
  <div class="wrapper" style="overflow-y: hidden;">
    <?php
    include_once("auth.php");
    $r = $_SESSION['SESS_LAST_NAME'];
    $_SESSION['SESS_FORM'] = '35';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Expenses
          <small>Preview</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">

        <!-- SELECT2 EXAMPLE -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Expenses</h3>
            <small class="btn btn-sm btn-success mx-2" style="padding: 5px 10px;" title="Add Vendor" onclick="click_open(5)">Add Vendor</small>
            <small class="btn btn-sm btn-warning mx-2" style="padding: 5px 10px;" title="Add Expenses Type" onclick="click_open(1)">Add Expenses Type</small>
            <small class="btn btn-sm btn-danger mx-2 util_sec" style="padding: 5px 10px;" title="Add Utility Bill" onclick="click_open(2)">Add Utility Bill</small>
            <small class="btn btn-sm btn-primary mx-2 load_sec" style="display: none;padding: 5px 10px;" title="Add Root Expenses Sub Type" onclick="click_open(3)">Add Root Expenses</small>
            <small class="btn btn-sm btn-primary mx-2 pur_sec" style="display: none;padding: 5px 10px;" title="Add Purchase Expenses Sub Type" onclick="click_open(4)">Add Purchase Expenses</small>
            <small class="btn btn-sm btn-primary mx-2 sub_sec" style="display: none;padding: 5px 10px;" title="Add Expenses Sub Type" onclick="click_open(7)">Add Sub Type</small>
          </div>

          <!-- /.box-header -->
          <div class="box-body d-block">
            <form method="post" action="expenses_save.php">
              <div class="row">

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Vendor</label>
                    <select class="form-control select2" name="vendor" style="width: 100%;" tabindex="8">
                      <option value="0">None</option>
                      <?php
                      $result = $db->prepare("SELECT * FROM vendor WHERE  action=1  ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?> </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Paycose</label>
                    <select class="form-control select2 hidden-search" name="paycose" id="paycose" onchange="select_cose()" style="width: 100%;" tabindex="8">
                      <option value="asset">Asset</option>
                      <option value="expenses">Expenses</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-3 ass_sec" style="display: block;">
                  <div class="form-group">
                    <label>Duration Month</label>
                    <input type="number" step=".01" name="due" class="form-control" tabindex="10" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Type</label>
                    <select class="form-control select2" name="type" style="width: 100%;" id="ex_type" onchange="select_type(this.value)" tabindex="1">

                      <?php
                      $result = $db->prepare("SELECT * FROM expenses_types ");
                      $result->bindParam(':id', $ttr);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $id = $row['sn']; ?>"> <?php echo $row['type_name']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>

                  </div>
                </div>

                <div class="col-md-3 adv_sec" style="display: none;">
                  <div class="form-group">
                    <label>Employee</label>
                    <select class="form-control select2" name="emp_id" style="width: 100%;" tabindex="8">
                      <option value="0" disabled selected></option>
                      <?php
                      $result = $db->prepare("SELECT * FROM employee WHERE action = 1 ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['username']); ?> </option>
                      <?php
                      }
                      ?>
                    </select>

                  </div>
                </div>

                <div class="col-md-3 adv_sec" style="display: none;">
                  <div class="form-group">
                    <label>Pay Type</label>
                    <select class="form-control select2 hidden-search" name="pay_type" onchange="select_paycose(this.options[this.selectedIndex].getAttribute('value'))" style="width: 100%;" tabindex="8">
                      <option value="cash">Cash</option>
                      <option value="lorry_collection">Lorry Collection</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-3 load_sec lor_sec" style="display: none;">
                  <div class="form-group">
                    <label>Loading ID</label>
                    <select class="form-control select2" name="load_id" style="width: 100%;" tabindex="8">
                      <option value="0" disabled selected></option>
                      <?php
                      $result = $db->prepare("SELECT * FROM loading WHERE  action='load'  ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['transaction_id']; ?>"><?php echo $row['transaction_id'] . ' / ' . $row['lorry_no']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>

                  </div>
                </div>

                <div class="col-md-3 pur_sec" style="display: none;">
                  <div class="form-group">
                    <label>Lorry No</label>
                    <select class="form-control select2" name="lorry" style="width: 100%;" tabindex="8">
                      <option value="0" disabled selected></option>
                      <?php
                      $result = $db->prepare("SELECT * FROM lorry  WHERE type = '0' ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['lorry_id']; ?>"><?php echo $row['lorry_no']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>

                  </div>
                </div>

                <div class="col-md-3 sub_sec" id="sub_sec" style="display: none;">
                  <div class="form-group">
                    <label>Sub Type</label>
                    <select class="form-control select2" id="sub_type" name="sub_type" style="width: 100%;" tabindex="8"></select>
                  </div>
                </div>

                <div class="col-md-3 util_sec" style="display: block;">
                  <div class="form-group">
                    <label>Utility Bill</label> <span id="blc" class="badge bg-red"></span>
                    <select class="form-control select2" name="util_id" style="width: 100%;" tabindex="8" onchange="select_bill(this.options[this.selectedIndex].getAttribute('balance'))">
                      <option value="0" balance=""></option>
                      <?php
                      $result = $db->prepare("SELECT * FROM utility_bill  ");
                      $result->bindParam(':userid', $ttr);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['id']; ?>" balance="<?php echo $row['credit']; ?>"> <?php echo $row['name']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>

                  </div>
                </div>

                <div class="col-md-3 util_sec" style="display: block;">
                  <div class="form-group">
                    <label>Utility Date</label>
                    <input type="text" id="datepickerd" name="util_date" class="form-control" tabindex="9" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3 util_sec" style="display: block;">
                  <div class="form-group">
                    <label>Invoice No:</label>
                    <input type="text" name="util_invo" class="form-control" tabindex="10" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Bill Amount</label>
                    <input type="number" name="amount" step=".01" class="form-control" tabindex="11" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Comment</label>
                    <input type="text" value='' name="comment" class="form-control" tabindex="13" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-1 pe-2 me-2" style="height: 70px;display: flex;align-items: end;" id="btn_sub">
                  <div class="form-group">
                    <input class="btn btn-info" style="width: 100px;" type="submit" value="Save">
                    <input name="unit" type="hidden" value="1">
                  </div>
                </div>
              </div>
            </form>

          </div>
        </div>
      </section>


      <!-- /.box -->

      <section class="content">

        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Expenses List</h3>
          </div>

          <form action="" method="POST">
            <div class="row" style="margin-top: 20px;display: flex;align-items: center;">
              <div class="col-lg-3"></div>
              <div class="col-md-2">
                <div class="form-group">
                  <select class="form-control select2 hidden-search" name="year" style="width: 100%;" tabindex="1">
                    <option> <?php echo date('Y') - 1 ?> </option>
                    <option selected> <?php echo date('Y') ?> </option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <select class="form-control select2 hidden-search " name="month" style="width: 100%;" tabindex="1">
                    <?php for ($x = 1; $x <= 12; $x++) {
                      $mo = sprintf("%02d", $x); ?>
                      <option <?php if (isset($_POST['year'])) {
                                if ($mo == $_GET['month']) {
                                  echo 'selected';
                                }
                              } else {
                                if ($mo == date('m')) {
                                  echo 'selected';
                                }
                              } ?>> <?php echo $mo; ?> </option>
                    <?php  } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <input class="btn btn-info" type="submit" value="Search">
                </div>
              </div>
            </div>
          </form>

          <?php
          include("connect.php");
          date_default_timezone_set("Asia/Colombo");

          $d1 = date('Y-m') . '-01';
          $d2 = date('Y-m') . '-31';

          if (isset($_POST['year'])) {

            $d1 = $_POST['year'] . '-' . $_POST['month'] . '-01';
            $d2 = $_POST['year'] . '-' . $_POST['month'] . '-31';
          }

          $sql = " SELECT * FROM expenses_records  WHERE dll=0 AND date BETWEEN '$d1' AND '$d2' ORDER BY close_date  ";
          ?>

          <div class="box-body d-block">
            <table id="example" class="table table-bordered " style="border-radius: 0;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Comment</th>
                  <th>Pay Type</th>
                  <th>Vendor</th>
                  <th>Amount (Rs.)</th>
                  <th>#</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $tot = 0;
                $blc = 0;
                $pay = 0;
                $result = $db->prepare($sql);
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $dll = $row['dll'];
                  $type = $row['type_id'];
                  $pay_type = $row['pay_type'];
                  if ($dll == 1) {
                    $style = 'opacity: 0.5;cursor: default;';
                  } else {
                    $style = '';
                  }

                  if ($row['paycose'] == 'asset') {
                    $paycose = 'navy';
                  } else
                if ($row['paycose'] == 'expenses') {
                    $paycose = 'maroon';
                  } else
                if ($row['paycose'] == 'payment') {
                    $paycose = 'purple';
                    $style = 'background-color: rgb(var(--bg-light-70));';
                  } else {
                    $paycose = '';
                  }

                  if ($row['pay_type'] == 'credit' & $row['pay_amount'] == 0) {
                    $dll = 0;
                  } else {
                    $dll = 1;
                  }
                ?>

                  <tr class="record" style="<?php echo $style; ?>">
                    <td>
                      <?php echo $row['id']; ?>.
                      <span class="badge bg-<?php echo $paycose; ?>"> <?php echo ucfirst($row['paycose']); ?> </span>
                    </td>
                    <td>
                      <?php echo $row['date']; ?> <br>
                      <?php if ($pay_type == 'credit' && $row['close_date'] == '' && $row['credit_balance'] > 0) { ?>
                        <span class="badge bg-red"> <i class="fa fa-ban"></i> Unpaid </span>
                      <?php } else { ?>
                        <span class="badge bg-gray"> <i class="fa fa-check"></i> Paid </span> <br>
                        <?php echo $row['close_date']; ?>
                      <?php } ?>
                    </td>
                    <td>
                      <?php if ($row['util_id'] > 0) {
                        echo $row['util_name'];
                      } else if ($row['sub_type'] > 0) {
                        echo $row['sub_type_name'];
                      } else {
                        echo $row['type'];
                      }  ?>
                      <?php if ($type == 2) { ?> <br> <span class="badge bg-blue">Loading ID: <?php echo $row['loading_id']; ?> </span> <br> <span class="badge bg-green"> <i class="fa fa-truck"></i> <?php echo $row['lorry_no']; ?> </span> <?php } ?>
                      <?php if ($type == 3) { ?> <br> <span class="badge bg-green"> <i class="fa fa-truck"></i> <?php echo $row['lorry_no']; ?> </span> <?php } ?>
                    </td>
                    <td>
                      <?php if ($type == 1) { ?> <span class="badge bg-maroon"> Utility </span> <br> <?php } else 
                     if ($type == 2) { ?> <span class="badge bg-olive"> Root </span> <br> <?php } else  
                     if ($type == 3) { ?> <span class="badge bg-orange"> Purchase </span> <br> <?php } else {  ?> <span class="badge bg-gray"> Expenses </span> <br> <?php } ?>
                      <?php echo $row['comment'];   ?>
                    </td>
                    <td>
                      <?php echo ucfirst($pay_type); ?> <br>
                      <?php if ($pay_type == 'chq') { ?>
                        NO: <span class="badge bg-blue"><?php echo $row['chq_no']; ?> </span> <br>
                        Date: <span class="badge bg-green"><?php echo $row['chq_date']; ?> </span> <br>
                      <?php } ?>
                    </td>
                    <td>
                      <?php echo $row['vendor_name']; ?>
                    </td>
                    <td>
                      Rs.<?php echo $row['amount'];  ?> <br>
                      Pay Amount: <?php echo $row['pay_amount']; ?> <br>
                      <?php if ($type == 1 || $pay_type == 'credit') { ?>Balance: <?php echo $row['credit_balance']; ?> <br> <?php } ?>
                    <?php if ($type == 1) { ?>Forward Balance: <?php echo $row['util_forward_balance']; ?> <?php } ?>
                    </td>
                    <td>
                      <?php if ($pay_type == 'credit' && $dll == 0 && $row['pay_amount'] == 0) { ?>
                        <a href="#" id="<?php echo $row['id']; ?>" class="btn_dll btn btn-sm btn-danger" title="Click to Delete">
                          <i class="fa fa-trash"></i>
                        </a>
                      <?php } else if ($dll == 0 && $pay_type != 'credit') { ?>
                        <a href="#" id="<?php echo $row['id']; ?>" class="btn_dll btn btn-sm btn-danger" title="Click to Delete">
                          <i class="fa fa-trash"></i>
                        </a>
                      <?php }
                      if ($pay_type == 'credit' && $row['close_date'] == '' && $row['credit_balance'] > 0) { ?>
                        <a class="btn btn-primary btn-sm" href="expenses.php?id=<?php echo $row['invoice_no']; ?>"> <i class="fa fa-money"></i> Pay </a>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php if ($row['pay_type'] == 'credit') {
                    $tot += $row['amount'];
                    $pay += $row['pay_amount'];
                  } ?>
                <?php }   ?>
              </tbody>
            </table>
            <h4>Credit: <small class="ms-2">Rs.</small><?php echo number_format($tot, 2); ?> </h4>
            <h4>Payment: <small class="ms-2">Rs.</small><?php echo number_format($pay, 2); ?> </h4>
            <h4>Balance: <small class="ms-2">Rs.</small><?php echo number_format($tot - $pay, 2); ?> </h4>
          </div>
        </div>

      </section>
      <!-- /.content -->
    </div>

    <!-- /.content-wrapper -->
    <?php include("dounbr.php"); ?>

    <?php
    $co = 'd-none';
    $closer = '<div id="closer" class="container-close" onclick="click_close()"></div>';
    if (isset($_GET['id'])) {
      $co = '';
      $closer = '';

      $re = $db->prepare("SELECT * FROM expenses_records WHERE invoice_no = :id AND action = 1 ");
      $re->bindParam(':id', $_GET['id']);
      $re->execute();
      for ($k = 0; $r = $re->fetch(); $k++) {
        $bill = $r['amount'];
        $type = $r['type'];
        $paycose = $r['paycose'];
        $term_amount = $r['term_amount'];
        $blc = $r['credit_balance'];
      }

      $pay = '';
      if ($paycose == 'asset') {
        $pay = $term_amount;
        $co = 'd-none';
      }
    }
    ?>

    <div class="container-up <?php echo $co; ?>" id="container_up">
      <?php echo $closer; ?>
      <div class="row">
        <div class="col-md-12">

          <div class="box box-success popup d-none" id="popup_1" style="width: 450px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Expenses Type
                <i onclick="click_close()" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="expenses_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Expenses Type</label>
                      <input type="text" name="type" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="hidden" name="unit" value="3">
                      <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_2" style="width: 450px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Utility Type
                <i onclick="click_close()" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="expenses_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Utility Name</label>
                      <input type="text" name="util_name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="hidden" name="unit" value="2">
                      <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_3" style="width: 450px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Root Expenses Sub Type
                <i onclick="click_close()" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="expenses_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Sub Type Name</label>
                      <input type="text" name="name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="hidden" name="unit" value="4">
                      <input type="hidden" name="typeid" value="2">
                      <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_4" style="width: 450px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Purchase Expenses Sub Type
                <i onclick="click_close()" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="expenses_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Sub Type Name</label>
                      <input type="text" name="name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="hidden" name="unit" value="4">
                      <input type="hidden" name="typeid" value="3">
                      <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_5" style="width: 600px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Vendor
                <i onclick="click_close()" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="expenses_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Vendor Name</label>
                      <input type="text" name="name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Address</label>
                      <input type="text" name="address" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Contact</label>
                      <input type="text" name="contact" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Note</label>
                      <input type="text" name="note" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group pull-right">
                      <input type="hidden" name="unit" value="5">
                      <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup <?php echo $co; ?>" id="popup_6" style="width: 600px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Payment
                <i onclick="confirm_close(6)" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="expenses_save.php">

                <div class="row" style="display: block;">

                  <div class="col-md-12">
                    <div class="form-group" style="display: flex; justify-content: space-between;">
                      <h5>Bill Amount: <small>Rs. </small> <?php echo $bill; ?> </h5>
                      <h5>Type: <?php echo $type; ?> </h5>
                      <h5>Balance: <small>Rs. </small> <?php echo $blc; ?> </h5>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Pay Type</label>
                      <select class="form-control select2 hidden-search" id="pay_type" name="pay_type" onchange="select_pay()" style="width: 100%;" tabindex="2">
                        <option id="pay_cash" value="cash"> Cash </option>
                        <option id="pay_chq" value="chq"> Chq </option>
                        <!-- <option value="bank" disabled> Bank </option> -->
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6" id="acc_sec">
                    <div class="form-group">
                      <label>Account</label>
                      <select class="form-control select2 hidden-search" name="acc" id="cash_acc" style="width: 100%;" tabindex="3">
                        <?php
                        $result = $db->prepare("SELECT * FROM cash WHERE id != 2 ");
                        $result->bindParam(':id', $ttr);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                        ?>
                          <option value="<?php echo $id = $row['id']; ?>" <?php if ($id == 1) { ?> selected <?php } ?>> <?php echo $row['name']; ?> </option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6 bank_sec" style="display: none;">
                    <div class="form-group">
                      <label>Account</label>
                      <select class="form-control select2 hidden-search" name="bank" style="width: 100%;" tabindex="4">

                        <?php
                        $result = $db->prepare("SELECT * FROM bank_balance ");
                        $result->bindParam(':id', $ttr);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                        ?>
                          <option value="<?php echo $id = $row['id']; ?>" <?php if ($id == 1) { ?> selected <?php } ?>> <?php echo $row['name'] . '__' . $row['ac_no']; ?> </option>
                        <?php
                        }
                        ?>
                      </select>

                    </div>
                  </div>

                  <div class="col-md-6 bank_sec" style="display: none;">
                    <div class="form-group">
                      <label>Chq No:</label>
                      <input type="text" name="chq_no" class="form-control" tabindex="5" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6 bank_sec" style="display: none;">
                    <div class="form-group">
                      <label>Chq Date</label>
                      <input type="text" name="chq_date" id="datepicker" class="form-control" tabindex="6" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Pay Amount</label>
                      <input type="text" name="pay_amount" value="<?php echo $pay; ?>" step=".01" class="form-control" tabindex="12" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="hidden" name="invo" value="<?php echo $_GET['id']; ?>">
                      <input type="hidden" name="unit" value="6">
                      <input type="submit" value="Save" style="margin-top: 23px; width: 100px;" class="btn btn-info">
                    </div>
                  </div>

                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_7" style="width: 450px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Sub Type
                <i onclick="click_close()" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="expenses_save.php">

                <div class="row" style="display: block;">

                  <div class="col-md-12" id="acc_sec">
                    <div class="form-group">
                      <label>Mani Type</label>
                      <select class="form-control select2" name="typeid" style="width: 100%;" tabindex="3">
                        <?php
                        $result = $db->prepare("SELECT * FROM expenses_types ");
                        $result->bindParam(':id', $ttr);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                        ?>
                          <option value="<?php echo $id = $row['sn']; ?>"> <?php echo $row['type_name']; ?> </option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Sub Type Name</label>
                      <input type="text" name="name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" name="unit" value="4">
                      <input type="submit" style="margin-top: 23px;width: 100%;" value="Save" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="confirm_close" style="width: 358px;display: flex;flex-direction: column;justify-content: space-between;">

            <h4 class="form-control" style="font-weight: 600;">Sure you want to cancel this payment ? </h4>
            <div style="display: flex;align-items:center;justify-content:space-around;margin:10px 0;">
              <button onclick="check_process('no')" style="width: 100px;" class="btn btn-sm btn-primary">No</button>
              <button onclick="check_process('yes')" style="width: 100px;" class="btn btn-sm btn-danger">Yes</button>
            </div>
            <input type="hidden" id="confirm_popup">
          </div>
        </div>

      </div>
    </div>

    <div class="control-sidebar-bg"></div>
  </div>

  <?php include_once("script.php"); ?>

  <!-- DataTables -->
  <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
  <!-- date-range-picker -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
  <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- bootstrap datepicker -->
  <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
  <!-- SlimScroll 1.3.0 -->
  <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
  <!-- iCheck 1.0.1 -->
  <script src="../../plugins/iCheck/icheck.min.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>


  <script>
    function click_open(i) {
      $("#popup_" + i).removeClass("d-none");
      $("#container_up").removeClass("d-none");
    }

    function click_close() {
      $(".popup").addClass("d-none");
      $("#container_up").addClass("d-none");
    }

    function confirm_close(i) {
      $("#popup_" + i).addClass("d-none");
      $("#closer").addClass("d-none");
      $("#confirm_close").removeClass("d-none");
      $("#confirm_popup").val(i);
    }

    function check_process(type) {
      let i = $("#confirm_popup").val();
      if (type == 'yes') {
        $(".popup").addClass("d-none");
        $("#container_up").addClass("d-none");
        $("#confirm_close").addClass("d-none");
      } else {
        $("#popup_" + i).removeClass("d-none");
        $("#container_up").removeClass("d-none");
        $("#confirm_close").addClass("d-none");
      }
    }
  </script>

  <script type="text/javascript">
    function select_paycose(val) {
      if (val == 'lorry_collection') {
        $('.lor_sec').css('display', 'block');
      } else {
        $('.lor_sec').css('display', 'none');
      }
    }

    function select_bill(val) {
      $('#blc').text(val);
      if (val == '') {
        $('#btn').attr('disabled', '');
      } else {
        $('#btn').removeAttr('disabled');
      }
    }

    function model_btn(i) {
      $('#model_btn_' + i).css('display', 'none');
      $('.model_add_' + i).css('display', 'block');
    }

    function model_cl(i) {
      $('#model_add_' + i).css('display', 'none');
      $('#model_btn_' + i).css('display', 'inline-block');
    }

    function select_cose() {
      let val = $('#paycose').val();

      if (val == 'asset') {
        $('.ass_sec').css('display', 'block');
      } else {
        $('.ass_sec').css('display', 'none');
      }
    }

    function select_pay() {
      let val = $('#pay_type').val();

      if (val == 'cash') {
        $('#acc_sec').css('display', 'block');
      } else {
        $('#acc_sec').css('display', 'none');
      }
      if (val == 'chq') {
        $('.bank_sec').css('display', 'block');
      } else {
        $('.bank_sec').css('display', 'none');
      }

    }

    function select_type(val) {

      if (val == 1) {
        $('.load_sec').css('display', 'none');
        $('.pur_sec').css('display', 'none');
        $('.adv_sec').css('display', 'none');
        $('.sub_sec').css('display', 'none');
        $('.util_sec').css('display', 'block');
        $('.util_sec.btn').css('display', 'inline-block');
      } else
      if (val == 2) {
        $('.util_sec').css('display', 'none');
        $('.pur_sec').css('display', 'none');
        $('.adv_sec').css('display', 'none');
        $('.load_sec').css('display', 'block');
        $('.load_sec.btn').css('display', 'inline-block');
      } else
      if (val == 3) {
        $('.util_sec').css('display', 'none');
        $('.load_sec').css('display', 'none');
        $('.adv_sec').css('display', 'none');
        $('.pur_sec').css('display', 'block');
        $('.pur_sec.btn').css('display', 'inline-block');
      } else
      if (val == 4) {
        $('.util_sec').css('display', 'none');
        $('.pur_sec').css('display', 'none');
        $('.load_sec').css('display', 'none');
        $('.adv_sec').css('display', 'block');
      } else {
        $('.util_sec').css('display', 'none');
        $('.load_sec').css('display', 'none');
        $('.pur_sec').css('display', 'none');
        $('.adv_sec').css('display', 'none');
        $('.sub_sec').css('display', 'none');
      }

      if (val == 1 | val == 2 | val == 3) {
        $('.sub_sec.btn').css('display', 'none');
      } else {
        $('.sub_sec.btn').css('display', 'inline-block');
      }

      if (val > 1) {
        var xmlhttp;
        if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        } else { // code for IE6, IE5
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("sub_type").innerHTML = xmlhttp.responseText;
            $('#sub_sec').css('display', 'block');
          }
        }

        xmlhttp.open("GET", "expenses_get.php?id=" + val, true);
        xmlhttp.send();
      }
    }

    $(function() {


      $(".btn_dll").click(function() {

        //Save the link in a variable called element
        var element = $(this);

        //Find the id of the link that was clicked
        var del_id = element.attr("id");

        //Built a url to send
        var info = 'id=' + del_id;
        if (confirm("Sure you want to delete this Credit? There is NO undo!")) {

          $.ajax({
            type: "GET",
            url: "expenses_dll.php",
            data: info,
            success: function() {}
          });
          $(this).parents(".record").animate({
              backgroundColor: "#fbc7c7"
            }, "fast")
            .animate({
              opacity: "hide"
            }, "slow");
          $(this).remove();

        }

        return false;

      });

    });



    $(function() {
      $("#example1").DataTable();
      $('#example').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": false,
        "autoWidth": true
      });
    });
  </script>

  <!-- Page script -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();
      $('.select2.hidden-search').select2({
        minimumResultsForSearch: -1
      });

      //Date range picker
      $('#reservation').daterangepicker();
      //Date range picker with time picker
      //$('#datepicker').datepicker({datepicker: true,  format: 'yyyy/mm/dd '});
      //Date range as a button
      $('#daterange-btn').daterangepicker({
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function(start, end) {
          $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
      );

      //Date picker
      $('#datepicker').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy-mm-dd '
      });
      $('#datepicker').datepicker({
        autoclose: true
      });


      $('#datepicker_set').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy-mm-dd '
      });
      $('#datepicker_set').datepicker({
        autoclose: true
      });


      $('#datepickerd').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy-mm-dd '
      });
      $('#datepickerd').datepicker({
        autoclose: true
      });


    });
  </script>

</body>

</html>