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
        $_SESSION['SESS_FORM'] = '29';

        include_once("sidebar.php");
        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    VAT REGENERATION
                    <small>Preview</small>
                </h1>

            </section>
            <!-- Main content -->
            <section class="content">
                <!-- SELECT2 EXAMPLE -->
                <div class="row" style="display: flex;justify-content:center;">
                    <?php $pra = 0;
                    $result = $db->prepare("SELECT sum(amount) FROM vat_account ");
                    $result->bindParam(':id', $date);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                        $sum = $row['sum(amount)'];
                    }

                    $result = $db->prepare("SELECT * FROM vat_account ");
                    $result->bindParam(':id', $date);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {

                        $pra = $row['amount'] / $sum * 100;
                    ?>
                        <div class="col-lg-3 col-xs-6" style="margin: 10px 0;">
                            <div class="info-box bg-gray">
                                <span class="info-box-icon">
                                    <i class="fa fa-dollar" style="color:rgb(var(--bg-light-100));font-size: 50px;"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 13px; text-align: end; padding-right: 10px;"><?php echo ucfirst($row['vat_no']) ?></span>
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
                                <h3 class="box-title">Select Account</h3>
                                <!-- /.box-header -->
                            </div>

                            <?php


                            if (isset($_GET['account'])) {
                                $acc = $_GET['account'];
                                $dates = $_GET['dates'];
                            } else {
                                $acc = 0;
                                $dates = date('Y/m/d-Y/m/d');
                            }

                            $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                            $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");


                            $sql = "SELECT * FROM vat_record WHERE acc_id = '$acc' AND date BETWEEN '$d1' AND '$d2'";

                            ?>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form action="" method="GET">
                                        <div class="row">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Accounts</label>

                                                    <select class="form-control select2 hidden-search" name="account" style="width: 100%;" tabindex="1" autofocus>

                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM vat_account ");
                                                        $result->bindParam(':userid', $res);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['id']; ?>">
                                                                <?php echo $row['vat_no']; ?>
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
                                <h3 class="box-title">Reg List</h3>
                                <!-- /.box-header -->
                            </div>

                            <div class="box-body d-block">
                                <table id="example1" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Trans: Type</th>
                                            <th>Date</th>
                                            <th>Invoice</th>
                                            <th>Vat (Rs.)</th>
                                            <th>Credit (Rs.)</th>
                                            <th>Debit (Rs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $vat = 0;
                                        $value = 0;
                                        $result = $db->prepare($sql);
                                        $result->bindParam(':userid', $res);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td>
                                                    <?php if ($row['type'] == 'Credit') { ?>
                                                        <span class="badge bg-blue"><?php echo $row['type']; ?></span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-red"><?php echo $row['type']; ?></span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $row['date']; ?></td>
                                                <td><?php echo $row['invoice_no']; ?></td>
                                                <td>
                                                    <?php echo number_format($row['vat'], 2); ?>
                                                </td>

                                                <td>
                                                    <?php if ($row['type'] == 'Credit') { ?>
                                                        <?php echo number_format($row['value'], 2); ?>
                                                    <?php } ?>
                                                </td>

                                                <td>
                                                    <?php if ($row['type'] == 'Debit') { ?>
                                                        <?php echo number_format($row['value'], 2); ?>
                                                    <?php } ?>
                                                </td>

                                                <?php $vat += $row['vat']; ?>
                                                <?php $value += $row['value']; ?>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>

                                </table>
                                <h4>Total Vat <small> Rs. </small><b><?php echo number_format($vat, 2); ?></h4>
                                <h4>Without Vat Amount <small> Rs. </small><b><?php echo number_format($value, 2); ?></h4>

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
            $('#example2').DataTable({
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