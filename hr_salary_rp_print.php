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
  <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='hr_salary_rp.php?date=<?php echo $_GET['date']; ?>'">
  <div class="wrapper">
    <!-- Main content -->
    <section class="invoice">

      <div class="box-body">

        <table id="example1" class="table table-bordered table-striped">

          <thead>
            <tr>
              <th>id</th>
              <th>Name</th>
              <th>Day Pay</th>
              <th>Day rate</th>
              <th>OT</th>
              <th>OT Rate</th>
              <th>Commission</th>
              <th>Sub Total</th>
              <th>Advance</th>
              <th>EPF</th>
              <th>Deduction</th>
              <th>Balance</th>
              <th>Day</th>
              <th>OT</th>


            </tr>

          </thead>

          <tbody>
            <?php
            $date = $_GET["date"];
            $tot = 0;
            $result = $db->prepare("SELECT * FROM hr_payroll WHERE  date='$date'  ");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
              $id = $row['emp_id'];



            ?>
              <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo number_format($row['day_pay'], 2); ?></td>
                <td><?php echo number_format($row['day_rate'], 2); ?></td>
                <td><?php echo number_format($row['ot'], 2); ?></td>
                <td><?php echo number_format($row['ot_rate'], 2); ?></td>
                <td><?php echo number_format($row['commis'], 2); ?></td>
                <td><?php echo number_format($row['day_pay'] + $row['ot'] + $row['commis'], 2); ?></td>
                <td><?php echo number_format($row['advance'], 2); ?></td>
                <td><?php echo number_format($row['epf'], 2); ?></td>
                <td><?php echo number_format($row['advance'] + $row['epf'], 2); ?></td>
                <td><?php echo number_format($row['amount'], 2); ?></td>
                <td><?php echo $row['day']; ?></td>
                <td><?php echo $row['ot_time']; ?></td>
              <?php
              if ($row['amount'] > 0) {
                $tot += $row['amount'];
              }
            }

              ?>
              </tr>


          </tbody>
          <tfoot>
            <?php
            $result = $db->prepare("SELECT sum(day_pay),sum(ot),sum(commis),sum(advance),sum(epf),sum(amount) FROM hr_payroll WHERE  date='$date'  ");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
            ?>
              <tr>
                <th></th>
                <th></th>
                <th>Rs.<?php echo $row['sum(day_pay)'] ?></th>
                <th></th>
                <th>Rs.<?php echo $row['sum(ot)'] ?></th>
                <th></th>
                <th>Rs.<?php echo $row['sum(commis)'] ?></th>
                <th>Rs.<?php echo $row['sum(commis)'] + $row['sum(ot)'] + $row['sum(day_pay)'] ?></th>
                <th>Rs.<?php echo $row['sum(advance)'] ?></th>
                <th>Rs.<?php echo $row['sum(epf)'] ?></th>
                <th>Rs.<?php echo $row['sum(advance)'] + $row['sum(epf)'] ?></th>
                <th>Rs.<?php echo $tot ?></th>
                <th></th>
                <th></th>
              </tr>
            <?php } ?>






          </tfoot>
        </table>
      </div>
    </section>
  </div>
</body>

</html>