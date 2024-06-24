<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
?>

<body class="hold-transition skin-yellow sidebar-mini">
  <div class="wrapper" style="overflow-y: hidden;">
    <?php
    include_once("auth.php");
    $r = $_SESSION['SESS_LAST_NAME'];
    $_SESSION['SESS_FORM'] = '8';

    include_once("sidebar.php");

    ?>

    <style>
      th.th {
        vertical-align: bottom;
        text-align: center;
      }

      th.th span {
        -ms-writing-mode: tb-rl;
        -webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
      }
    </style>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Loading Report
          <small>Preview</small>
        </h1>
      </section>


      <?php

      date_default_timezone_set("Asia/Colombo");
      if (isset($_GET['id'])) {
        $id = $_GET['id'];
      } else {
        $result = $db->prepare("SELECT * FROM loading ORDER BY transaction_id DESC LIMIT 1  ");
        $result->bindParam(':userid', $res);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
          $id = $row['transaction_id'];
        }
      }

      $result = $db->prepare("SELECT * FROM loading WHERE transaction_id='$id'  ");
      $result->bindParam(':userid', $res);
      $result->execute();
      for ($i = 0; $row = $result->fetch(); $i++) {
        $action = $row['action'];
        $root = $row['root'];
        $lorry = $row['lorry_no'];
        $lorry_id = $row['lorry_id'];
        $driver = $row['driver'];
        $helper1 = $row['helper1'];
        $helper2 = $row['helper2'];
        $helper3 = $row['helper3'];
        $load_date = $row['date'];
      }

      $result = $db->prepare("SELECT * FROM employee  ");
      $result->bindParam(':userid', $res);
      $result->execute();
      for ($i = 0; $row = $result->fetch(); $i++) {
        if ($row['id'] == $driver) {
          $driver = $row['username'];
          $driver_pic = $row['pic'];
        }

        if ($row['id'] == $helper1) {
          $helper1 = $row['username'];
          $helper1_pic = $row['pic'];
        }

        if ($row['id'] == $helper2) {
          $helper2 = $row['username'];
          $helper2_pic = $row['pic'];
        }

        if ($row['id'] == $helper3) {
          $helper3 = $row['username'];
          $helper3_pic = $row['pic'];
        }
      }

      ?>

      <!-- Main content -->
      <section class="content">

        <form action="" method="GET">
          <div class="row" style="margin-bottom: 50px;display: flex;align-items: end;">
            <div class="col-lg-3"></div>
            <div class="col-lg-3">
              <label>Loading id :</label>
              <input type="text" class="form-control pull-right" name="id" value="<?php echo $id; ?>">
            </div>

            <div class="col-lg-2">
              <button class="btn btn-info" type="submit">
                <i class="fa fa-search "></i> Search
              </button>
            </div>
          </div>
        </form>

        <div class="row">
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-yellow"><i class="fa fa-truck"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Lorry Number</span>
                <span class="info-box-number" style="margin-top: 10px;"><?php echo $lorry; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-yellow">
                <div class="info-box-img">
                  <img src="<?php echo $driver_pic; ?>" alt="">
                </div>
              </span>

              <div class="info-box-content">
                <span class="info-box-text">Driver</span>
                <span class="info-box-number" style="margin-top: 10px;"><?php echo ucfirst($driver); ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix visible-sm-block"></div>

          <?php if ($helper1 > '0') { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-light">
                  <div class="info-box-img">
                    <img src="<?php echo $helper1_pic; ?>" alt="">
                  </div>
                </span>

                <div class="info-box-content">
                  <span class="info-box-text">Helper-1</span>
                  <span class="info-box-number" style="margin-top: 10px;"><?php echo ucfirst($helper1); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
          <?php } ?>

          <?php if ($helper2 > '0') { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-light">
                  <div class="info-box-img">
                    <img src="<?php echo $helper2_pic; ?>" alt="">
                  </div>
                </span>

                <div class="info-box-content">
                  <span class="info-box-text">Helper-2</span>
                  <span class="info-box-number" style="margin-top: 10px;"><?php echo ucfirst($helper2); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
          <?php } ?>

        </div>
      </section>


      <!-- Main content -->
      <section class="content">

        <!-- SELECT2 EXAMPLE -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Loading Report</h3>
            <?php if ($action == 'unload') { ?>
              <a href="unloading_print.php?id=<?php echo $id; ?>" style="margin-left: 10px;" class="btn btn-danger btn-sm"><i class="fa fa-print"></i> Loading Summary Print</a>
            <?php } ?>
            <label style="margin-right: 50px;" class="pull-right"><small style="margin-right: 10px;">Root: </small><?php echo $root; ?></label>
            <label style="margin-right: 50px;" class="pull-right"><small style="margin-right: 10px;">Date: </small><?php echo $load_date; ?></label>
            <label style="margin-right: 50px;" class="pull-right"><small style="margin-right: 10px;">Loading ID: </small><?php echo $id; ?></label>
            <?php if ($helper1 > '0') { ?>
              <label style="margin-right: 50px;" class="pull-right"><small style="margin-right: 10px;">Helper 3: </small><?php echo ucfirst($helper3); ?></label>
            <?php } ?>
          </div>

          <!-- /.box-header -->
          <div class="box-body">

            <div class="row">

              <?php
              $loading_list = array();

              $result = $db->prepare("SELECT * FROM loading_list WHERE  loading_id='$id' AND product_code < 9  ORDER by product_code ASC");
              $result->bindParam(':userid', $date);
              $result->execute();
              for ($i = 0; $row = $result->fetch(); $i++) {

                $data = array('id' => $row['product_code'], 'qty' => $row['qty'], 'st_qty' => $row['qty_sold']);

                array_push($loading_list, $data);
              }

              function check_img($id)
              {
                if ($id == 1 | $id == 5) {
                  return array('12.5.png', '40');
                }
                if ($id == 2 | $id == 6) {
                  return array('5.png', '40');
                }
                if ($id == 3 | $id == 7) {
                  return array('37.5.png', '27');
                }
                if ($id == 4 | $id == 8) {
                  return array('2.png', '40');
                }
              }

              function get_empty($lists, $id)
              {
                foreach ($lists as $list) {

                  if ($list['id'] == $id + 4) {
                    return $list['st_qty'];
                  }
                }
              }
              ?>

              <?php foreach ($loading_list as $list) {
                if ($list['id'] < 5) { ?>

                  <?php $img = check_img($list['id']); ?>

                  <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-yellow">
                        <img style="width: <?php echo $img[1]; ?>px;" src="icon/<?php echo $img[0]; ?>" alt="">
                      </span>

                      <div class="info-box-content">
                        <span class="info-box-label">Load Qty: <?php echo $list['qty']; ?></span>

                        <div class="info-box-content-set" style="margin-top: 5px;">
                          <span class="info-box-text">Gas:</span>
                          <span class="info-box-number"><?php echo $list['st_qty']; ?></span>
                        </div>

                        <div class="info-box-content-set">
                          <span class="info-box-text">Empty:</span>
                          <span class="info-box-number"><?php echo get_empty($loading_list, $list['id']) - $list['st_qty']; ?></span>
                        </div>
                      </div>
                    </div>
                  </div>

              <?php }
              } ?>

            </div>

            <div class="box-header with-border">
              <h3 class="box-title">
                Lorry Sales Report
                <span class="pull-right badge bg-muted">New</span>
                <span class="pull-right badge bg-yellow">Refill</span>
              </h3>
            </div>

            <div class="box-body">
              <table id="" class="table table-bordered table-striped">

                <thead>

                  <tr>
                    <th colspan="2"></th>
                    <th colspan="2">12.5kg</th>
                    <th colspan="2">5kg</th>
                    <th colspan="2">37.5kg</th>
                    <th colspan="2">2kg</th>

                    <?php
                    $ass_list = array();
                    $result = $db->prepare("SELECT *  FROM sales_list WHERE sales_list.loading_id=:id AND sales_list.product_id > 9 AND sales_list.action = 0 GROUP BY sales_list.product_id ");
                    $result->bindParam(':id', $id);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                      array_push($ass_list, $row['product_id']);
                    ?>
                      <th class="th"><span> <?php echo $row['name']; ?></span></th>
                    <?php } ?>

                  </tr>

                  <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>N</th>
                    <th>R</th>
                    <th>N</th>
                    <th>R</th>
                    <th>N</th>
                    <th>R</th>
                    <th>N</th>
                    <th>R</th>

                    <?php
                    foreach ($ass_list as $list) { ?>
                      <th></th>
                    <?php } ?>

                  <tr>

                </thead>

                <tbody>

                  <?php
                  $sales_list = array();
                  $sales = array();
                  $product = array();

                  $result = $db->prepare("SELECT *  FROM sales_list WHERE sales_list.loading_id=:id AND sales_list.action=0  ORDER BY sales_list.product_id ");
                  $result->bindParam(':id', $id);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {

                    $data = array('invo' => $row['invoice_no'], 'pid' => $row['product_id'], 'qty' => $row['qty']);

                    array_push($sales_list, $data);
                  }

                  $result = $db->prepare("SELECT * FROM products  ORDER BY product_id  ");
                  $result->bindParam(':id', $id);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                    array_push($product, $row['product_id']);
                  }

                  $result = $db->prepare("SELECT * FROM sales WHERE loading_id=:id AND action=1 ");
                  $result->bindParam(':id', $id);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) { //row
                    $invo = $row['invoice_number'];
                    $cus = $row['name'];
                    $sales_id = $row['transaction_id'];

                    $temp = array();

                    $temp['invo'] =  $invo;
                    $temp['cus'] =  $cus;

                    foreach ($product as $p_id) { //colum
                      $temp[$p_id] = '';
                    }

                    foreach ($sales_list as $list) {

                      if ($list['invo'] == $invo) {

                        foreach ($product as $p_id) { //colum

                          if ($p_id == $list['pid']) {
                            if ($p_id > 4) {
                              $temp[$p_id] = "<span class='pull-right badge bg-muted'> " . $list['qty'] . "</span>";
                            } else {
                              $temp[$p_id] = "<span class='pull-right badge bg-yellow'> " . $list['qty'] . "</span>";
                            }
                          } else {
                          }
                        }
                      }
                    }

                    array_push($sales, $temp);
                  }
                  ?>

                  <?php foreach ($sales as $list) { ?>

                    <tr>

                      <td> <?php echo $list['invo']; ?> </td>
                      <td> <?php echo $list['cus']; ?> </td>

                      <td> <?php echo $list['5']; ?></td>
                      <td> <?php echo $list['1']; ?> </td>

                      <td> <?php echo $list['6']; ?></td>
                      <td><?php echo $list['2']; ?></td>

                      <td> <?php echo $list['7']; ?></td>
                      <td><?php echo $list['3']; ?></td>

                      <td> <?php echo $list['8']; ?> </td>
                      <td> <?php echo $list['4']; ?> </td>

                      <?php foreach ($ass_list as $ass) { ?>
                        <td> <?php echo $list[$ass]; ?>
                        </td>
                      <?php } ?>

                    </tr>
                  <?php } ?>
                </tbody>

                <?php
                $total = array();

                foreach ($product as $p_id) {
                  $total[$p_id] = '';
                }

                $result = $db->prepare("SELECT  product_id,sum(sales_list.qty)  FROM sales_list WHERE sales_list.loading_id=:id AND sales_list.action = 0 GROUP BY sales_list.product_id ");
                $result->bindParam(':id', $id);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $total[$row['product_id']] = $row['sum(sales_list.qty)'];
                }
                ?>

                <tfoot class=" bg-black">
                  <tr>
                    <td colspan="2">Total</td>

                    <td>
                      <span class="pull-right badge bg-muted"> <?php echo $total['5']; ?> </span>
                    </td>
                    <td>
                      <span class="pull-right badge bg-yellow"> <?php echo $total['1']; ?> </span>
                    </td>
                    <td>
                      <span class="pull-right badge bg-muted"> <?php echo $total['6']; ?> </span>
                    </td>
                    <td>
                      <span class="pull-right badge bg-yellow"> <?php echo $total['2']; ?> </span>
                    </td>
                    <td>
                      <span class="pull-right badge bg-muted"> <?php echo $total['7']; ?> </span>
                    </td>
                    <td>
                      <span class="pull-right badge bg-yellow"> <?php echo $total['3']; ?> </span>
                    </td>
                    <td>
                      <span class="pull-right badge bg-muted"> <?php echo $total['8']; ?> </span>
                    </td>
                    <td>
                      <span class="pull-right badge bg-yellow"> <?php echo $total['4']; ?> </span>
                    </td>

                    <?php

                    foreach ($total as $i => $tot) {
                      if ($i > 9  && $tot > 0) { ?>
                        <td>
                          <span class="pull-right badge bg-muted">
                            <?php
                            echo $tot;
                            ?>
                          </span>
                        </td>

                    <?php }
                    } ?>

                  </tr>

                </tfoot>

              </table>
            </div>

            <br>

            <div class="box-body">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                  <tr>

                    <th>Invoice no </th>
                    <th>Customer</th>
                    <th>Pay type</th>
                    <th>Amount </th>
                    <th>Chq no</th>
                    <th>Chq Date</th>
                    <th>Bank</th>
                  </tr>

                </thead>
                <tbody>
                  <?php

                  $result = $db->prepare("SELECT * FROM payment WHERE  loading_id='$id' and action>'0'  ORDER by transaction_id DESC");
                  $result->bindParam(':userid', $date);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                    $invo = $row['invoice_no'];

                    $cus = '';
                    $result1 = $db->prepare("SELECT * FROM sales WHERE  invoice_number='$invo' and action='1' ");
                    $result1->bindParam(':userid', $c);
                    $result1->execute();
                    for ($k = 0; $row1 = $result1->fetch(); $k++) {

                      $in = $row1['transaction_id'];
                      $cus = $row1['name'];
                    }

                    $paycose = $row['paycose'];

                    $cr = '';
                    $color_code = '';
                    if ($paycose == 'credit_payment') {
                      $color_code = 'background-color:#7FB3D5';
                      $cr = '(credit)';
                    }
                  ?>

                    <tr style="<?php echo $color_code; ?>">
                      <td>
                        <?php echo $invo . ' ' . $cr; ?>
                      </td>
                      <td><?php echo $cus; ?></td>
                      <td><?php echo $row['type']; ?></td>
                      <td><?php echo $row['amount']; ?></td>
                      <td><?php echo $row['chq_no']; ?></td>
                      <td><?php echo $row['chq_date']; ?></td>
                      <td><?php echo $row['chq_bank']; ?> </td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                </tfoot>
              </table>
              <?php

              $result = $db->prepare("SELECT sum(amount) FROM payment WHERE  loading_id='$id' AND type='cash' and action >'0'  ORDER by transaction_id DESC");
              $result->bindParam(':userid', $c);
              $result->execute();
              for ($i = 0; $row = $result->fetch(); $i++) {
                $cash = $row['sum(amount)'];
              }

              $result = $db->prepare("SELECT sum(amount) FROM payment WHERE  loading_id='$id' AND pay_type='chq' and action >'0'  ORDER by transaction_id DESC");
              $result->bindParam(':userid', $c);
              $result->execute();
              for ($i = 0; $row = $result->fetch(); $i++) {
                $chq = $row['sum(amount)'];
              }

              $result = $db->prepare("SELECT sum(amount) FROM payment WHERE  loading_id='$id' AND pay_type='credit' and action >'0'  ORDER by transaction_id DESC");
              $result->bindParam(':userid', $c);
              $result->execute();
              for ($i = 0; $row = $result->fetch(); $i++) {
                $credit = $row['sum(amount)'];
              }
              ?>

              <h3 style="color: green">Cash- Rs.<?php echo $cash; ?></h3>
              <h3>CHQ- Rs.<?php echo $chq; ?></h3>
              <h3 style="color: red">Credit- Rs.<?php echo $credit; ?></h3>

              <div class="row">
                <div class="col-md-6">
                  <div class="box-header">
                    <h3 class="box-title">Remove bill</h3>
                  </div>
                  <div class="box-body">
                    <table id="example10" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Invoice no</th>
                          <th>Type</th>
                          <th>Amount (Rs.)</th>

                        </tr>
                      </thead>
                      <tbody>

                        <?php $result = $db->prepare("SELECT * FROM payment WHERE loading_id='$id' and action='0'  ");
                        $result->bindParam(':userid', $date);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                        ?>
                          <tr>
                            <td><?php echo $row['invoice_no'];   ?> </td>
                            <td><?php echo $row['type'];   ?> </td>
                            <td>Rs.<?php echo $row['amount'];   ?></td>

                          </tr>
                        <?php }   ?>

                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="box-header">
                    <h3 class="box-title">Expenses</h3>
                  </div>
                  <div class="box-body">
                    <table id="example10" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Type</th>
                          <th>Amount (Rs.)</th>
                          <th>Comment</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php $result = $db->prepare("SELECT * FROM expenses_records WHERE loading_id='$id' AND paycose = 'expenses' AND dll='0'  ");
                        $result->bindParam(':userid', $date);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                        ?>
                          <tr>
                            <td><?php echo $row['id'];
                                ?> </td>
                            <td><?php echo $row['sub_type_name'];
                                ?> </td>
                            <td>Rs.<?php echo $row['amount'];
                                    ?></td>
                            <td><?php echo $row['comment'];
                                ?></td>
                          </tr>
                        <?php }
                        ?>

                        <?php $result = $db->prepare("SELECT * FROM petty_topup WHERE loading_id='$id' and action='0'");
                        $result->bindParam(':userid', $date);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                        ?>
                          <tr style="background-color:cadetblue">
                            <td>Non </td>
                            <td>Patty cash TOPUP</td>
                            <td>Rs.<?php echo $row['amount'];   ?></td>
                            <td><?php echo $row['date'];   ?></td>
                          </tr>
                        <?php }   ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="box-body">
                    <table id="example10" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th><i class="fa fa-money"></i></th>
                          <th>QTY</th>
                          <th>Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $result = $db->prepare("SELECT * FROM loading WHERE transaction_id='$id'   ");
                        $result->bindParam(':userid', $res);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                          $tid = $row['transaction_id'];
                          $tto = 0; ?>


                          <tr>
                            <td>5000</td>
                            <td><?php echo $row['r5000']; ?></td>
                            <td><?php $tto += $row['r5000'] * 5000;
                                echo $row['r5000'] * 5000; ?></td>
                          </tr>
                          <tr>
                            <td>2000</td>
                            <td><?php echo $row['r2000']; ?></td>
                            <td><?php $tto += $row['r2000'] * 2000;
                                echo $row['r2000'] * 2000; ?></td>
                          </tr>
                          <tr>
                            <td>1000</td>
                            <td><?php echo $row['r1000']; ?></td>
                            <td><?php $tto += $row['r1000'] * 1000;
                                echo $row['r1000'] * 1000; ?></td>
                          </tr>
                          <tr>
                            <td>500</td>
                            <td><?php echo $row['r500']; ?></td>
                            <td><?php $tto += $row['r500'] * 500;
                                echo $row['r500'] * 500; ?></td>
                          </tr>
                          <tr>
                            <td>100</td>
                            <td><?php echo $row['r100']; ?></td>
                            <td><?php $tto += $row['r100'] * 100;
                                echo $row['r100'] * 100; ?></td>
                          </tr>
                          <tr>
                            <td>50</td>
                            <td><?php echo $row['r50']; ?></td>
                            <td><?php $tto += $row['r50'] * 50;
                                echo $row['r50'] * 50; ?></td>
                          </tr>
                          <tr>
                            <td>20</td>
                            <td><?php echo $row['r20']; ?></td>
                            <td><?php $tto += $row['r20'] * 20;
                                echo $row['r20'] * 20; ?></td>
                          </tr>
                          <tr>
                            <td>10</td>
                            <td><?php echo $row['r10']; ?></td>
                            <td><?php $tto += $row['r10'] * 10;
                                echo $row['r10'] * 10; ?></td>
                          </tr>
                          <tr>
                            <td><i class="fa fa-database"></i> Coine (කාසි)</td>
                            <td><?php echo $row['coins']; ?></td>
                            <td><?php $tto += $row['coins'];
                                echo $row['coins']; ?></td>
                          </tr>
                        <?php } ?>

                      </tbody>
                      <tfoot>
                        <tr>
                          <td>Total</td>
                          <td><?php echo  $tto; ?></td>
                        </tr>
                        <tr>
                          <td>Balance</td>
                          <td><?php echo  $row['cash_total']; ?></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col -->
      </section>
      <!-- /.content -->
    </div>

    <!-- /.content-wrapper -->
    <?php
    include("dounbr.php");
    ?>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div>
  <!-- ./wrapper -->

  <!-- jQuery 2.2.3 -->
  <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
  <!-- Bootstrap 3.3.6 -->
  <script src="../../bootstrap/js/bootstrap.min.js"></script>
  <!-- Select2 -->
  <script src="../../plugins/select2/select2.full.min.js"></script>
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
  <!-- AdminLTE App -->
  <script src="../../dist/js/app.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../dist/js/demo.js"></script>
  <!-- Dark Theme Btn-->
  <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>


  <script>
    $(function() {
      $("#example1").DataTable();
      $("#example2").DataTable();
      $('#example3').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false
      });
    });
  </script>
  <!-- Page script -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();

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
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
              'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function(start, end) {
          $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
            'MMMM D, YYYY'));
        }
      );

      //Date picker
      $('#datepicker').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy/mm/dd '
      });
      $('#datepicker').datepicker({
        autoclose: true
      });



      $('#datepickerd').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy/mm/dd '
      });
      $('#datepickerd').datepicker({
        autoclose: true
      });

    });
  </script>

</body>

</html>