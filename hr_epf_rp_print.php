<!DOCTYPE html>
<html>

<head>
  <?php
  include("connect.php");
  ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CLOUD ARM | Invoice</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body onload="window.print() " style=" font-size: 13px; font-family: arial;">
  <?php
  $sec = "1";
  ?>
  <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='hr_epf_rp.php?year=<?php echo $_GET['year']; ?>&month=<?php echo $_GET['month']; ?>'">
  <div class="wrapper">
    <!-- Main content -->
    <section class="invoice">
      <?php
      $m = $_GET['year'] . '-' . $_GET['month'];

      $sql = "SELECT * FROM `hr_payroll` WHERE date='$m' ";
      ?>
      <table id="" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th rowspan="2" style="text-align: center;">Employees Name</th>
            <th rowspan="2" style="text-align: center;">NIC</th>
            <th rowspan="2" style="text-align: center;">Member No</th>
            <th colspan="3" style="text-align: center;">Contribution</th>
            <th rowspan="2" style="text-align: center;">Total Earnings</th>
          </tr>
          <tr>
            <th style="text-align: center;">Total</th>
            <th style="text-align: center;">12%</th>
            <th style="text-align: center;">8%</th>
            <th style="display: none;"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total = 0;
          $epf_12_tot = 0;
          $epf_8_tot = 0;

          $result = $db->prepare($sql);
          $result->bindParam(':userid', $date);
          $result->execute();
          for ($i = 0; $row = $result->fetch(); $i++) {
            $epf = $row['epf'];
            $emp_id = $row['emp_id'];
            $basic = $row['day_pay'];
            $ot = $row['ot'];
            $com = $row['commis'];
            $etf = $row['etf'];
            $amount = $basic + $ot + $com + $etf;

            $rq = $db->prepare("SELECT * FROM Employees WHERE id='$emp_id' ");
            $rq->bindParam(':userid', $date);
            $rq->execute();
            for ($k = 0; $r = $rq->fetch(); $k++) {
              $name = $r['name'];
              $nic = $r['nic'];
              $id = $r['epf_no'];
            }

          ?>
            <tr>
              <td><?php echo $name ?></td>
              <td><?php echo $nic ?></td>
              <td><?php echo $id ?></td>
              <td><?php echo $tot = $epf + $epf / 8 * 12;
                  $total += $tot; ?>.00</td>
              <td><?php echo $epf_12 = $epf / 8 * 12;
                  $epf_12_tot += $epf_12; ?>.00</td>
              <td><?php echo $epf;
                  $epf_8_tot += $epf;  ?></td>
              <td><?php echo $amount + $epf_12; ?></td>
            </tr>
          <?php } ?>

        </tbody>
        <tbody style="border-top: 0;">
          <tr>
            <td></td>
            <td></td>
            <td>
              <h4> Total</h4>
            </td>
            <td>
              <h4><?php echo $total; ?>.00</h4>
            </td>
            <td>
              <h4><?php echo $epf_12_tot; ?>.00</h4>
            </td>
            <td>
              <h4><?php echo $epf_8_tot; ?>.00</h4>
            </td>
            <td></td>
          </tr>
        </tbody>
      </table>

    </section>

</body>

</html>