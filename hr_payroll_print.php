<!DOCTYPE html>
<html>

<head>
    <?php
    include("connect.php");

    $invo = $_GET['id'];
    $co = substr($invo, 0, 2);
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
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        body {
            font-family: 'Poppins';
        }
    </style>
</head>

<body onload="window.print() " style=" font-size: 13px; font-family: 'Poppins';">

    <div class="wrapper">

        <!-- Main content -->
        <section class="invoice">


            <?php $oth_ded = 0.00;
            function AddPlayTime($times)
            {
                $minutes = 0; //declare minutes either it gives Notice: Undefined variable
                // loop throught all the times
                foreach ($times as $time) {
                    list($hour, $minute) = explode('.', $time);
                    $minutes += $hour * 60;
                    $minutes += $minute;
                }

                $hours = floor($minutes / 60);
                $minutes -= $hours * 60;

                // returns the time already formatted
                return sprintf('%02d.%02d', $hours, $minutes);
            }

            function TimeSet($times)
            {

                list($hour, $minute) = explode('.', $times);
                $minutes = $minute + $hour * 60;

                return $minutes / 60;
            }



            $ids = $_GET["id"];
            $date = $_GET["date"];
            $result = $db->prepare("SELECT * FROM hr_payroll WHERE emp_id ='$ids' AND date='$date' ORDER BY id ASC");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $id = $row['emp_id'];
                $adv = $row['advance'];
                $hr_pay_amount = $row['amount'];
                $work_h = $row['day'];
                $rate = $row['day_rate'];
                $commission = $row['commis'];
            }

            $d1 = $_GET['date'] . '-01';
            $d2 = $_GET['date'] . '-31';
            $h = 0;
            $m = 0;
            $result = $db->prepare("SELECT work_time,ot FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $hour[] = $row['work_time'];
                $ot[] = $row['ot'];
            }

            $result = $db->prepare("SELECT count(id) FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $day = $row['count(id)'];
            }

            $result = $db->prepare("SELECT * FROM employee WHERE id='$id' ");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $name = $row['name'];
                // $rate=$row['hour_rate'];
                $epf_no = $row['epf_no'];
                $epf = $row['epf_amount'];
                $epf_8 = $row['epf_amount'];
                $basic = $row['basic'];
                $well = $row['well'];
            }




            ?>
            <h2 align="center">SALARY SLIP</h2>




            <?php

            $invo = $_GET['id'];
            $tot_amount = 0;
            $result = $db->prepare("SELECT sum(dic) FROM sales_list WHERE   invoice_no='$invo'");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $dis_tot = $row['sum(dic)'];
            }
            ?>
            <div class="box-body">

                <div>

                    <div class="pull-right" style="width: 48%; ">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td>ATTENDANCE DAYS</td>
                                    <td align="right"><?php echo $day; ?></td>
                                </tr>
                                <tr>
                                    <td>Work Hours</td>
                                    <td align="right"><?php echo $work_h;
                                                        $hour = AddPlayTime($hour); ?></td>
                                </tr>
                                <tr>
                                    <td>Hour Rate</td>
                                    <td align="right">Rs.<?php echo number_format($rate, 2); ?></td>
                                </tr>
                            </tbody>


                            <thead>
                                <tr style="font-size: 16px;font-weight: 600;">
                                    <th align="left">Earnings</th>
                                    <th align="right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Basic</td>
                                    <td align="right">Rs.<?php $basic = $rate * $work_h;
                                                            echo number_format($basic, 2) ?></td>
                                </tr>

                                <tr>
                                    <td>OT</td>
                                    <td align="right">Rs.<?php $ot_tot = ($rate * 142.86) / 100 * AddPlayTime($ot);
                                                            echo number_format($ot_tot, 2); ?></td>
                                </tr>
                                <tr>
                                    <td>Job Commission</td>
                                    <td align="right">Rs.<?php echo number_format($commission, 2); ?></td>
                                </tr>

                                <?php $allowances = 0;
                                $result = $db->prepare("SELECT * FROM hr_allowances WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
                                $result->bindParam(':userid', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) { ?>


                                <?php //$allowances += $row['amount'];
                                } ?>

                                <tr style="font-size: 15px;font-weight: 600;">
                                    <td align="right">Total</td>
                                    <td align="right">Rs.<?php $er_tot = $basic + $ot_tot + $allowances + $commission;
                                                            echo number_format($er_tot, 2); ?></td>
                                </tr>

                                <tr>
                                    <td>No-pay</td>
                                    <td align="right">Rs.<?php echo $no_pay = "0.00"; ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="font-size: 15px;font-weight: 600;">
                                    <td align="right">GROSS PAY</td>
                                    <td align="right">Rs.<?php echo number_format($er_tot - $no_pay, 2); ?></td>
                                </tr>
                            </tfoot>


                            <thead>
                                <tr style="font-size: 16px;font-weight: 600;">
                                    <th align="left">Deductions</th>
                                    <th align="right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>EPF - 8%</td>
                                    <td align="right">Rs.<?php echo number_format($epf_8, 2); ?></td>
                                </tr>

                                <tr>
                                    <td>Welfare</td>
                                    <td align="right">Rs.<?php echo number_format($well, 2); ?></td>
                                </tr>

                                <tr>
                                    <td>Advance/Loan</td>
                                    <td align="right">Rs.<?php echo number_format($adv, 2); ?></td>
                                </tr>

                                <tr>
                                    <td>Other Deduction</td>
                                    <td align="right"><?php echo number_format($oth_ded, 2); ?></td>
                                </tr>

                                <tr style="font-size: 15px;font-weight: 600;">
                                    <td align="right">Total</td>
                                    <td align="right">Rs.<?php $de_tot = $epf_8 + $well + $adv + $oth_ded;
                                                            echo number_format($de_tot, 2); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="font-size: 15px;font-weight: 600;">
                                    <td align="right">NET Salary</td>
                                    <td align="right">Rs.<?php echo number_format($hr_pay_amount, 2); ?></td>
                                </tr>
                            </tfoot>

                            <tbody>
                                <tr>
                                    <td>EPF - 12%</td>
                                    <td align="right">Rs.<?php $epf_12 = $epf_8 / 8 * 12;
                                                            echo number_format($epf_12, 2); ?></td>
                                </tr>

                                <tr>
                                    <td>ETF - 3%</td>
                                    <td align="right">Rs.<?php $etf = $epf_8 / 8 * 3;
                                                            echo number_format($etf, 2); ?></td>
                                </tr>

                                <tr style="font-size: 15px;font-weight: 600;">
                                    <td align="right">Total</td>
                                    <td align="right">Rs.<?php $oth_tot = $epf_12 + $etf;
                                                            echo number_format($oth_tot, 2) ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="font-size: 15px;font-weight: 600;">
                                    <td align="right">Total Earnings</td>
                                    <td align="right">Rs.<?php echo number_format($er_tot + $oth_tot, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>


                        <div class="box box-danger">
                            <div class="box-header">
                                <h3 class="box-title">Salary Advance List</h3>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">
                                <table id="example2" class="table table-bordered table-striped">

                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>DATE</th>
                                            <th>AMOUNT</th>
                                            <th>NOTE</th>
                                        </tr>

                                    </thead>

                                    <tbody>
                                        <?php
                                        $result = $db->prepare("SELECT * FROM salary_advance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
                                        $result->bindParam(':userid', $date);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['date'] ?></td>
                                                <td>Rs.<?php echo  $row['amount']; ?></td>
                                                <td><?php echo $row['note']; ?></td>

                                            <?php    } ?>
                                            </tr>
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th>Rs.<?php echo number_format($adv, 2); ?></th>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>

                    <div style="width:48%;">
                        <h2>JANATHA MOTORS</h2>
                        <H5>No.57, Bodi Mawatha, Tangalle</H5>
                        <h4>Employee Name : <?php echo $name; ?></h4>
                        <h4>EPF No. :<?php echo $epf_no; ?></h4>
                        <h3><?php echo $_GET['date'] ?></h3>

                        <!-- /.box-header -->

                        <div class="box-body">
                            <table id="example2" class="table table-bordered table-striped">

                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>DATE</th>
                                        <th>IN</th>
                                        <th>OUT</th>
                                        <th>DAY</th>
                                        <th>OT</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                    $result = $db->prepare("SELECT * FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
                                    $result->bindParam(':userid', $date);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                    ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['date'] ?></td>
                                            <td><?php echo $row['IN_time']; ?></td>
                                            <td><?php echo $row['OUT_time']; ?></td>
                                            <td><?php echo $row['work_time']; ?></td>
                                            <td><?php echo $row['ot']; ?></td>

                                        <?php    } ?>
                                        </tr>
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?php echo $work_h; ?></th>
                                    <th><?php echo $ot_tot ?></th>
                                </tfoot>
                            </table>
                        </div>




                        <!-- /.box -->
                    </div>




                </div>
                <br><br><br><br><br><br><br><br><br>
                <h4 align="center">AUTHORIZED</h4>

            </div>




    </div>
    </section>
    <?php if (isset($_GET['type'])) { ?>
        <meta http-equiv="refresh" content="1;URL='hr_payroll.php">
        <?php
    } else {
        $sec = "1";
        $id = $_GET['id'];
        $empid = 0;
        $result = $db->prepare("SELECT * FROM hr_payroll WHERE emp_id > '$id' AND date='$date' ORDER BY emp_id ASC LIMIT 1");
        $result->bindParam(':userid', $date);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $empid = $row['emp_id'];
        }

        if ($empid > 0) {
        ?>
            <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='hr_payroll_print.php?id=<?php echo $empid; ?>&date=<?php echo $_GET['date']; ?>">
        <?php } else { ?>
            <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='hr_payroll.php">
        <?php } ?>
    <?php } ?>
    </div>
</body>

</html>