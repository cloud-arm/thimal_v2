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
        $_SESSION['SESS_FORM'] = '14';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Payroll
                    <small>Preview</small>
                </h1>
            </section>


            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-6">

                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Payroll</h3>
                            </div>
                            <div class="box-body">
                                <form method="get" action="">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select class="form-control select2" name="id" style="width: 100%;" tabindex="1" autofocus>
                                                    <option value="0"></option>
                                                    <?php
                                                    $result = $db->prepare("SELECT * FROM employee ");
                                                    $result->bindParam(':userid', $res);
                                                    $result->execute();
                                                    for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                        <option value="<?php echo $row['id']; ?>">
                                                            <?php echo $row['name']; ?>
                                                        </option>
                                                    <?php    } ?>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select class="form-control select2 hidden-search" name="year" style="width: 100%;" tabindex="1" autofocus>
                                                    <option> <?php echo date('Y') - 1 ?> </option>
                                                    <option selected> <?php echo date('Y') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select class="form-control select2 hidden-search" name="month" style="width: 100%;" tabindex="1" autofocus>
                                                    <?php for ($x = 1; $x <= 12; $x++) { ?>
                                                        <option> <?php echo sprintf("%02d", $x); ?> </option>
                                                    <?php  } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input class="btn btn-info" type="submit" value="Submit">
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <?php
                            function AddPlayTime($times)
                            {

                                $minutes = 0; //declare minutes either it gives Notice: Undefined variable
                                // loop thought all the times
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

                            if (isset($_GET['id'])) {
                                $id = $_GET["id"];
                                $d1 = $_GET['year'] . '-' . $_GET['month'] . '-01';
                                $d2 = $_GET['year'] . '-' . $_GET['month'] . '-31';

                                $date = $_GET['year'] . '-' . $_GET['month'];

                                $con = 0;
                                $result = $db->prepare("SELECT * FROM hr_payroll WHERE emp_id='$id' AND date = '$date' ");
                                $result->bindParam(':userid', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $con = $row['id'];
                                    $name = $row['name'];
                                    $rate = $row['day_rate'];
                                    $ot = $row['ot'];
                                    $commission = $row['commis'];
                                    $epf_8 = $row['epf'];
                                    $adv = $row['advance'];
                                }

                                $oth_ded = 0.00;

                                if ($con > 0) {

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
                                        $well = $row['well'];
                                    }

                                    $h = 0;
                                    $m = 0;
                                    $result = $db->prepare("SELECT work_time,ot FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
                                    $result->bindParam(':userid', $date);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                        $hour[] = $row['work_time'];
                                    } ?>

                                    <div class="form-group">
                                        <h2><?php echo $name; ?></h2>

                                        <a href="hr_payroll.php?id=<?php echo $_GET['id'] - 1 ?>&year=<?php echo $_GET['year'] ?>&month=<?php echo $_GET['month'] ?>">
                                            <button class="btn btn-danger" <?php if ($_GET['id'] == 1) { ?>disabled <?php } ?>>Previous</button>
                                        </a>

                                        <a href="hr_payroll.php?id=<?php echo $_GET['id'] + 1 ?>&year=<?php echo $_GET['year'] ?>&month=<?php echo $_GET['month'] ?>">
                                            <button class="btn btn-danger">Next</button>
                                        </a>
                                        <h4 style="margin: 20px; 0"><?php echo $_GET['year'] . '-' . $_GET['month'] ?></h4>
                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>ATTENDANCE DAYS</td>
                                                    <td align="right"><?php echo $day; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Work Hours</td>
                                                    <td align="right"><?php echo $hour = AddPlayTime($hour); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Hour Rate</td>
                                                    <td align="right">Rs.<?php echo $rate; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">

                                            <thead>
                                                <tr style="font-size: 16px;font-weight: 600;">
                                                    <th align="left">Earnings</th>
                                                    <th align="right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Basic</td>
                                                    <td align="right">Rs.<?php echo $basic = $rate * $hour; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>OT</td>
                                                    <td align="right">Rs.<?php echo number_format($ot, 2); ?></td>
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
                                                    <td align="right">Rs.<?php $er_tot = $basic + $ot + $allowances + $commission;
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

                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr style="font-size: 16px;font-weight: 600;">
                                                    <th align="left">Deductions</th>
                                                    <th align="right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>EPF - 8%</td>
                                                    <td align="right">Rs.<?php echo $epf_8; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Welfare</td>
                                                    <td align="right">Rs.<?php echo $well; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Advance/Loan</td>
                                                    <td align="right">Rs.<?php echo $adv; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Other Deduction</td>
                                                    <td align="right"><?php echo $oth_ded; ?></td>
                                                </tr>

                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <td align="right">Total</td>
                                                    <td align="right">Rs.<?php $de_tot = $epf_8 + $well + $adv + $oth_ded;
                                                                            echo number_format($de_tot, 2) ?></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <td align="right">NET Salary</td>
                                                    <td align="right">Rs.<?php echo number_format($er_tot - $de_tot, 2); ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>EPF - 12%</td>
                                                    <td align="right">Rs.<?php echo $epf_12 = $epf_8 / 8 * 12; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>ETF - 3%</td>
                                                    <td align="right">Rs.<?php echo $etf = $epf_8 / 8 * 3; ?></td>
                                                </tr>

                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <th align="right">Total</th>
                                                    <th align="right">Rs.<?php echo $oth_tot = $epf_12 + $etf; ?></th>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <th align="right">Total Earnings</th>
                                                    <th align="center">Rs.<?php $tot_earn = $er_tot + $oth_tot;
                                                                            echo number_format($tot_earn, 2); ?></th>
                                                </tr>
                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <th align="right">Gross Pay</th>
                                                    <th align="center">Rs.<?php echo number_format($tot_earn - $oth_tot, 2); ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <form action="hr_payroll_save.php" method="post">
                                            <input type="hidden" name="date" value="<?php echo $_GET['year'] . "-" . $_GET['month'] ?>">
                                            <input type="submit" value="Already payroll create" disabled class="btn text-green" style="width: 100%; font-weight: 600; opacity: 1;">
                                        </form>
                                    </div>

                                    <div class="form-group">
                                        <form action="hr_payroll_print.php" method="get">
                                            <input type="hidden" name="date" value="<?php echo $_GET['year'] . "-" . $_GET['month'] ?>">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="type" value="1">
                                            <input type="submit" value="Print" class="btn btn-info pull-right">
                                        </form>
                                    </div>
                                <?php } else {

                                    $h = 0;
                                    $m = 0;
                                    $result = $db->prepare("SELECT work_time,ot FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
                                    $result->bindParam(':userid', $date);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                        $hour[] = $row['work_time'];
                                        $ot[] = $row['ot'];
                                    }

                                    $result = $db->prepare("SELECT count(id),count(ot) FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
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
                                        $rate = $row['hour_rate'];
                                        $epf_8 = $row['epf_amount'];
                                        $basic = $row['basic'];
                                        $well = $row['well'];
                                    }

                                    $result = $db->prepare("SELECT sum(amount) FROM salary_advance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
                                    $result->bindParam(':userid', $date);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                        $adv = $row['sum(amount)'];
                                    }


                                    $commission = 0 ?>

                                    <div class=" form-group">
                                        <h2><?php echo $name; ?></h2>

                                        <a href="hr_payroll.php?id=<?php echo $_GET['id'] - 1 ?>&year=<?php echo $_GET['year'] ?>&month=<?php echo $_GET['month'] ?>">
                                            <button class="btn btn-danger" <?php if ($_GET['id'] == 1) { ?>disabled <?php } ?>>Previous</button>
                                        </a>

                                        <a href="hr_payroll.php?id=<?php echo $_GET['id'] + 1 ?>&year=<?php echo $_GET['year'] ?>&month=<?php echo $_GET['month'] ?>">
                                            <button class="btn btn-danger">Next</button>
                                        </a>
                                        <h4 style="margin: 20px; 0"><?php echo $_GET['year'] . '-' . $_GET['month'] ?></h4>
                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>ATTENDANCE DAYS</td>
                                                    <td align="right"><?php echo $day; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Work Hours</td>
                                                    <td align="right"><?php echo $hour = AddPlayTime($hour); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Hour Rate</td>
                                                    <td align="right">Rs.<?php echo $rate; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">

                                            <thead>
                                                <tr style="font-size: 16px;font-weight: 600;">
                                                    <th align="left">Earnings</th>
                                                    <th align="right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Basic</td>
                                                    <td align="right">Rs.<?php echo $basic = $rate * $hour; ?></td>
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

                                                    <tr>
                                                        <td><?php echo $row['note'] ?></td>
                                                        <td align="right">Rs.<?php echo $row['amount']; ?></td>
                                                    </tr>
                                                <?php $allowances += $row['amount'];
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

                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr style="font-size: 16px;font-weight: 600;">
                                                    <th align="left">Deductions</th>
                                                    <th align="right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>EPF - 8%</td>
                                                    <td align="right">Rs.<?php echo $epf_8; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Welfare</td>
                                                    <td align="right">Rs.<?php echo $well; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Advance/Loan</td>
                                                    <td align="right">Rs.<?php echo $adv; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Other Deduction</td>
                                                    <td align="right"><?php echo $oth_ded; ?></td>
                                                </tr>

                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <td align="right">Total</td>
                                                    <td align="right">Rs.<?php echo $de_tot = $epf_8 + $well + $adv + $oth_ded; ?></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <td align="right">NET Salary</td>
                                                    <td align="right">Rs.<?php echo number_format($er_tot - $de_tot, 2); ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>EPF - 12%</td>
                                                    <td align="right">Rs.<?php echo $epf_12 = $epf_8 / 8 * 12; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>ETF - 3%</td>
                                                    <td align="right">Rs.<?php echo $etf = $epf_8 / 8 * 3; ?></td>
                                                </tr>

                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <td align="right">Total</td>
                                                    <td align="right">Rs.<?php echo $oth_tot = $epf_12 + $etf; ?></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-size: 15px;font-weight: 600;">
                                                    <td align="right">Total Earnings</td>
                                                    <td align="center">Rs.<?php echo number_format($er_tot + $oth_tot, 2); ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <form action="hr_payroll_save.php" method="post">
                                            <input type="hidden" name="date" value="<?php echo $_GET['year'] . "-" . $_GET['month'] ?>">
                                            <input type="submit" value="Process All" class="btn btn-danger" style="width: 100%;">
                                        </form>
                                    </div>
                                <?php } ?>
                            <?php  } ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?php if (isset($_GET['id'])) { ?>
                            <div class="box box-warning">
                                <div class="box-header">
                                    <h3 class="box-title">Attendance List</h3>
                                </div>
                                <!-- /.box-header -->

                                <div class="box-body">
                                    <table id="example2" class="table table-bordered table-striped">

                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>IN</th>
                                                <th>OUT</th>
                                                <th>W time</th>
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
                                            <th><?php echo $hour; ?></th>
                                            <th><?php echo $ot = AddPlayTime($ot); ?></th>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        <?php } ?>
                        <!-- /.box -->
                    </div>

                    <div class="col-md-6">
                        <?php if (isset($_GET['id'])) { ?>
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
                                                <th>Date</th>
                                                <th>Note</th>
                                                <th>Amount</th>

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
                                                    <td><?php echo $row['note']; ?></td>
                                                    <td>Rs.<?php echo $row['amount']; ?></td>


                                                <?php    } ?>
                                                </tr>
                                        </tbody>
                                        <tfoot>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Rs.<?php echo number_format($adv, 2); ?></th>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        <?php } ?>
                        <!-- /.box -->
                    </div>
                </div>
                <!-- /.box -->
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

    <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- SlimScroll -->
    <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="../../plugins/fastclick/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/app.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <!-- Dark Theme Btn-->
    <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>
    <!-- page script -->
    <script>
        $(function() {
            $(".select2").select2();
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });

            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
</body>

</html>