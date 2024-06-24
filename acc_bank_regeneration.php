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
        $_SESSION['SESS_FORM'] = '28';

        include_once("sidebar.php");

        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    BANK REGENERATION
                    <small>Preview</small>
                </h1>

            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row" style="display: flex;justify-content:center;">
                    <?php $pra = 0;
                    $result = $db->prepare("SELECT sum(amount) FROM bank_balance ");
                    $result->bindParam(':userid', $res);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                        $sum = $row['sum(amount)'];
                    }

                    $result = $db->prepare("SELECT * FROM bank_balance ");
                    $result->bindParam(':userid', $res);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {

                        $pra = $row['amount'] / $sum * 100;
                    ?>
                        <div class="col-lg-3 col-xs-6" style="margin: 10px 0;">
                            <div class="info-box bg-gray">
                                <span class="info-box-icon">
                                    <i class="fa fa-bank" style="color:rgb(var(--bg-light-100));font-size: 50px;"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 13px; text-align: end; padding-right: 10px;"><?php echo ucfirst($row['name']) ?></span>
                                    <span class="info-box-number" style="font-size: 25px;margin: 5px 0;"><?php echo $row['amount']; ?></span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $pra; ?>%"></div>
                                    </div>
                                    <span class="progress-description">
                                    </span>
                                </div>
                            </div>
                        </div>

                    <?php  } ?>
                </div>

                <div class="row">

                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Select Bank</h3>
                                <!-- /.box-header -->
                            </div>
                            <?php


                            if (isset($_GET['bank'])) {
                                $bank = $_GET['bank'];
                                $dates = $_GET['dates'];
                            } else {
                                $bank = 0;
                                $dates = date('Y/m/d-Y/m/d');
                            }

                            $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                            $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");

                            $sql = "SELECT * FROM bank_record WHERE debit_acc_id='$bank' OR credit_acc_no='$bank' AND date BETWEEN '$d1' AND '$d2'";

                            ?>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form action="" method="GET">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Bank Accounts</label>

                                                    <select class="form-control select2 hidden-search" name="bank" style="width: 100%;" tabindex="1" autofocus>

                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM bank_balance ");
                                                        $result->bindParam(':userid', $res);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['id']; ?>">
                                                                <?php echo $row['name']; ?> -<?php echo $row['ac_no']; ?>
                                                            </option>
                                                        <?php    } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <label>Date range:</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right" id="reservation" name="dates" value="<?php echo $dates; ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-2" style="height: 75px; display:flex; align-items:center;">
                                                <input type="submit" class="btn btn-info" value="Apply">
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Bank Reg List</h3>
                                <!-- /.box-header -->
                            </div>

                            <div class="box-body d-block">
                                <table id="example1" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Trans: Type</th>
                                            <th>CD: Name</th>
                                            <th>CD: Type</th>
                                            <th>DB: Name</th>
                                            <th>DB: Type</th>
                                            <th>Date</th>
                                            <th>Chq Details</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Acc: Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0;
                                        $result = $db->prepare($sql);
                                        $result->bindParam(':userid', $res);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['transaction_type']; ?></td>
                                                <td><?php echo $row['credit_acc_name']; ?></td>
                                                <td><?php echo $row['credit_acc_type']; ?></td>
                                                <td><?php echo $row['debit_acc_name']; ?></td>
                                                <td><?php echo $row['debit_acc_type']; ?></td>
                                                <td><?php echo $row['date']; ?></td>
                                                <td>
                                                    <span class="badge bg-blue"> <?php echo $row['chq_no']; ?> </span><br>
                                                    <span class="badge bg-red"> <?php echo $row['chq_date']; ?> </span>
                                                </td>
                                                <td>
                                                    <?php if ($row['type'] == 'Credit') { ?>
                                                        <?php echo number_format($row['amount'], 2); ?>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['type'] == 'Debit') { ?>
                                                        <?php echo number_format($row['amount'], 2); ?>
                                                    <?php } ?>
                                                </td>
                                                <?php if ($row['type'] == 'Credit') { ?>
                                                    <td><?php echo number_format($row['credit_acc_balance'], 2); ?></td>
                                                <?php } else { ?>
                                                    <td><?php echo number_format($row['debit_acc_balance'], 2); ?></td>
                                                <?php } ?>
                                                <?php $total += $row['amount']; ?>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>

                                </table>
                                <h4>Total Amount <small> Rs. </small><b><?php echo number_format($total, 2); ?></h4>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Un-realize Chq</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <table id="example2" class="table table-bordered table-hover" style="border-radius: 0;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Chq No</th>
                                                <th>Chq Bank</th>
                                                <th>Chq Date</th>
                                                <th>Amount (Rs.)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $chq_un = 0;
                                            $result = $db->prepare("SELECT * FROM payment WHERE chq_action=1 AND pay_type='Chq' ");
                                            $result->bindParam(':userid', $res);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $row['transaction_id']; ?></td>
                                                    <td><?php echo $row['chq_no']; ?></td>
                                                    <td><?php echo $row['chq_bank']; ?></td>
                                                    <td><?php echo $row['chq_date']; ?></td>
                                                    <td><?php echo $row['amount']; ?></td>
                                                    <?php $chq_un += $row['amount']; ?>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>

                                    </table>
                                    <h4>Total Rs <b><?php echo number_format($chq_un, 2); ?></h4>

                                </div>
                            </div>
                        </div>

                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Summary</h3>
                            </div>
                            <!-- /.box-header -->

                            <?php
                            $exp = 0;
                            $rq = $db->prepare("SELECT sum(amount) FROM bank_balance WHERE id=:id");
                            $rq->bindParam(':id', $bank);
                            $rq->execute();
                            for ($i = 0; $r = $rq->fetch(); $i++) {
                                $tot = $r['sum(amount)'];
                            }

                            $chq_in = 0;
                            $result = $db->prepare("SELECT sum(amount) FROM payment WHERE chq_action=0 AND pay_type='Chq' AND paycose = 'invoice_payment' ");
                            $result->bindParam(':userid', $res);
                            $result->execute();
                            for ($i = 0; $row = $result->fetch(); $i++) {
                                $chq_in = $row['sum(amount)'];
                            }

                            $chq_un = 0;
                            $result = $db->prepare("SELECT sum(amount) FROM payment WHERE chq_action=1 AND pay_type='Chq' ");
                            $result->bindParam(':userid', $res);
                            $result->execute();
                            for ($i = 0; $row = $result->fetch(); $i++) {
                                $chq_un = $row['sum(amount)'];
                            }
                            ?>
                            <div class="box-body d-block">
                                <table class="table table-borderless table-hover">
                                    <tbody>
                                        <tr>
                                            <td style="border: 0;">
                                                <h4 style="margin: 0">Chq in-hand :</h4>
                                            </td>
                                            <td style="border: 0;">
                                                <h4 style="margin: 0">Rs. <?php echo number_format($chq_in, 2); ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 style="margin: 0;">Un-realize chq :</h4>
                                            </td>
                                            <td>
                                                <h4 style="margin: 0">Rs. <?php echo number_format($chq_un, 2); ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 style="margin: 0">Account Balance :</h4>
                                            </td>
                                            <td>
                                                <h4 style="margin: 0">Rs. <?php echo number_format($tot + $chq_un, 2) ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h4 style="margin: 0">Available Balance :</h4>
                                            </td>
                                            <td>
                                                <h4 style="margin: 0">Rs. <?php echo number_format($tot, 2); ?></h4>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Chq In-hand</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <table id="example3" class="table table-bordered table-hover" style="border-radius: 0;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Chq No</th>
                                                <th>Chq Bank</th>
                                                <th>Chq Date</th>
                                                <th>Amount (Rs.)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $chq_in = 0;
                                            $result = $db->prepare("SELECT * FROM payment WHERE chq_action=0 AND pay_type='Chq' AND paycose = 'invoice_payment'  ");
                                            $result->bindParam(':userid', $res);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $row['transaction_id']; ?></td>
                                                    <td><?php echo $row['chq_no']; ?></td>
                                                    <td><?php echo $row['chq_bank']; ?></td>
                                                    <td><?php echo $row['chq_date']; ?></td>
                                                    <td><?php echo $row['amount']; ?></td>
                                                    <?php $chq_in += $row['amount']; ?>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>

                                    </table>
                                    <h4>Total Rs <b><?php echo number_format($chq_in, 2); ?></h4>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>

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


    <script type="text/javascript">
        $(function() {
            $("#example1").DataTable();
            $("#example2").DataTable();
            $("#example3").DataTable();
            $('#example4').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
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


            //Date picker
            $('#datepicker1').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker').datepicker({
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