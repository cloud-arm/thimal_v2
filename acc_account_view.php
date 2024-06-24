<!DOCTYPE html>
<html>
<?php
include("head.php");
?>

<body class="hold-transition skin-yellow sidebar-mini">
    <div class="wrapper" style="overflow-y: hidden;">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '93';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    ACCOUNTS LEDGER
                    <small>Preview</small>
                    <a href="<?php echo $_SESSION['SESS_BACK']; ?>" class="btn btn-warning btn-sm"> <i class="fa-regular fa-circle-left"></i> Back</a>
                </h1>

            </section>
            <!-- Main content -->
            <section class="content">

                <div class="row">

                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Select Account</h3>
                                <!-- /.box-header -->
                            </div>
                            <?php

                            if (isset($_GET['dates'])) {
                                $dates = $_GET['dates'];
                            } else {
                                $dates = date('Y/m/d-Y/m/d');
                            }

                            $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                            $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");


                            if (isset($_GET['acc'])) {
                                $acc = $_GET['acc'];
                                $acc_name = $_GET['acc_name'];
                                $account = " table_id = '$acc' AND table_name= '$acc_name' AND ";
                            } else if (isset($_GET['account'])) {
                                $acc = $_GET['account'];
                                $account = " acc_id = '$acc' AND ";
                            } else {
                                $acc = 0;
                                $account = "";
                            }

                            $sql = "SELECT * FROM acc_transaction_record WHERE $account action = 1 AND date BETWEEN '$d1' AND '$d2'";

                            ?>

                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form action="" method="GET">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Accounts</label>

                                                    <select class="form-control select2 hidden-search" name="account" style="width: 100%;" tabindex="1" autofocus>
                                                        <?php
                                                        $result = select("acc_account", "id,name,source_id");
                                                        foreach ($result as $row) {
                                                            echo sprintf('<option value="%s"> %s </option>', $row['id'], ucfirst($row['name']));
                                                        } ?>
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
                                <h3 class="box-title">Account Transaction</h3>

                                <span id="tbl_btn"></span>
                            </div>

                            <div class="box-body d-block">
                                <table id="example" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Trans: Type</th>
                                            <th>Type</th>
                                            <th>Acc: Name</th>
                                            <th>Date</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>ACC: Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $credit = 0;
                                        $debit = 0;
                                        $result = select_query($sql);
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['transaction_id']; ?></td>
                                                <td><?php echo $row['transaction_type']; ?></td>
                                                <td><?php echo $row['type']; ?></td>
                                                <td><?php echo $row['acc_name']; ?></td>
                                                <td><?php echo $row['date']; ?></td>
                                                <td>
                                                    <?php if ($row['type'] == 'Credit') {
                                                        echo number_format($row['credit'], 2);
                                                        $credit += $row['credit'];
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['type'] == 'Debit') {
                                                        echo number_format($row['debit'], 2);
                                                        $debit += $row['debit'];
                                                    } ?>
                                                </td>
                                                <td><?php echo $row['balance']; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="5">Total:</th>
                                            <th><?php echo number_format($credit, 2); ?></th>
                                            <th><?php echo number_format($debit, 2); ?></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>

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
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/jszip/jszip.min.js"></script>
    <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
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
            $("#example").DataTable({
                "responsive": true,
                "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#tbl_btn');
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