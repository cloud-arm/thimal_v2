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
  <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='hr_etf_rp.php?year=<?php echo $_GET['year']; ?>&month=<?php echo $_GET['month']; ?>'">
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
            <th>No</th>
            <th>Employees Name</th>
            <th>NIC</th>
            <th>Member No</th>
            <th>Contribution</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total = 0;
          $a = 1;
          $result = $db->prepare($sql);
          $result->bindParam(':userid', $date);
          $result->execute();
          for ($i = 0; $row = $result->fetch(); $i++) {

            $emp_id = $row['emp_id'];
            $etf = $row['etf'];

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
              <td><?php echo $a;
                  $a++ ?></td>
              <td><?php echo $name  ?></td>
              <td><?php echo $nic;  ?></td>
              <td><?php echo $id  ?></td>
              <td><?php echo $etf;
                  $total += $etf; ?></td>
            </tr>
          <?php } ?>

        </tbody>
        <tbody style="border-top: 0;">
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>
              <h4> Total</h4>
            </td>
            <td>
              <h4>Rs.<?php echo $total; ?>.00</h4>
            </td>
          </tr>
        </tbody>
      </table>

    </section>
</body>

</html>