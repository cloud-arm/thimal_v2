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
        $_SESSION['SESS_FORM'] = '26';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Chq Realizing
                    <small>Preview</small>
                </h1>

            </section>
            <!-- Main content -->
            <section class="content">
                <!-- SELECT2 table -->
                <div class="row">

                    <div class="col-md-12">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Deposit Chq</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <table id="table1" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Invoice no</th>
                                                <th>Chq No</th>
                                                <th>Chq Bank</th>
                                                <th>Chq Date</th>
                                                <th>Amount (Rs.)</th>
                                                <th>Realize</th>
                                                <th>Return</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0;
                                            $style = "";
                                            $result = $db->prepare("SELECT *, payment.invoice_no AS sn, payment.transaction_id AS pid , payment.amount AS payamount FROM payment JOIN bank_balance ON payment.bank_id = bank_balance.id WHERE payment.chq_action = 1 AND payment.paycose = 'invoice_payment' AND payment.pay_type='chq' ORDER BY payment.chq_date ASC ");
                                            $result->bindParam(':userid', $res);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {

                                            ?>
                                                <tr id="re_<?php echo $row['pid']; ?>">
                                                    <td><?php echo $row['pid']; ?></td>
                                                    <td>
                                                        <?php echo $row['sn']; ?>
                                                    </td>
                                                    <td><?php echo $row['chq_no']; ?></td>
                                                    <td><?php echo $row['chq_bank']; ?></td>
                                                    <td><?php echo $row['chq_date']; ?></td>
                                                    <td><?php echo $row['payamount']; ?></td>
                                                    <td align="center"> <a href="#" id="<?php echo $row['pid']; ?>" onclick="dep_realize(<?php echo $row['pid']; ?>)" class="btn btn-success" title="Click to Realize"> <i class="fa-solid fa-money-bill-transfer"></i></a></td>
                                                    <td align="center"> <a href="#" id="<?php echo $row['pid']; ?>" onclick="dep_return(<?php echo $row['pid']; ?>)" class="btn btn-danger" title="Click to Return"> <i class="fa-solid fa-rotate-left"></i> </a></td>
                                                    <?php $total += $row['payamount']; ?>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>

                                    </table>
                                    <h4>Total Rs <b><?php echo number_format($total, 2); ?></h4>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Expenses Issue Chq</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <table id="table23" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Invoice no</th>
                                                <th>Chq No</th>
                                                <th>Chq Bank</th>
                                                <th>Chq Date</th>
                                                <th>Issue</th>
                                                <th>Amount (Rs.)</th>
                                                <th>Realize</th>
                                                <th>Return</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0;
                                            $style = "";
                                            $result = $db->prepare("SELECT *,payment.invoice_no AS sn, payment.amount AS pamount,payment.transaction_id AS pid FROM payment JOIN bank_balance ON payment.bank_id = bank_balance.id WHERE  payment.chq_action = 1 AND payment.paycose = 'expenses_issue' AND payment.pay_type='chq'  ORDER BY payment.chq_date ASC ");
                                            $result->bindParam(':userid', $res);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                                $date1 = date_create(date('Y-m-d'));
                                                $date2 = date_create($row['chq_date']);
                                                $date_diff = date_diff($date1, $date2);
                                                $date_diff = $date_diff->format("%R%a");

                                                $note = '';
                                                $result2 = $db->prepare('SELECT * FROM expenses_records WHERE  invoice_no=:id ');
                                                $result2->bindParam(':id', $row['invoice_no']);
                                                $result2->execute();
                                                for ($i = 0; $row2 = $result2->fetch(); $i++) {
                                                    $note = $row2['comment'];
                                                }

                                            ?>
                                                <tr id="re_<?php echo $row['pid']; ?>">
                                                    <td><?php echo $row['pid']; ?></td>
                                                    <td>
                                                        <?php echo $row['sn']; ?>
                                                    </td>
                                                    <td><?php echo $row['chq_no']; ?></td>
                                                    <td><?php echo $row['chq_bank']; ?></td>
                                                    <td><?php echo $row['chq_date']; ?><span class="badge bg-blue"><?php echo $date_diff; ?></span></td>
                                                    <td><?php echo $note; ?></td>
                                                    <td><?php echo $row['pamount']; ?></td>
                                                    <?php if ($date_diff <= 0) { ?>
                                                        <td align="center"> <a href="#" id="<?php echo $row['pid']; ?>" unit="exp" onclick="iss_realize(<?php echo $row['pid']; ?>,'exp')" class="btn btn-success" title="Click to Realize"> <i class="fa-solid fa-money-bill-transfer"></i></a></td>
                                                        <td align="center"> <a href="#" id="<?php echo $row['pid']; ?>" unit="exp" onclick="iss_return(<?php echo $row['pid']; ?>,'exp')" class="btn btn-danger" title="Click to Return"> <i class="fa-solid fa-rotate-left"></i> </a></td>
                                                    <?php } else { ?>
                                                        <td align="center" colspan="2"> <span class="badge bg-yellow">Pending</span> </td>

                                                    <?php }
                                                    $total += $row['pamount']; ?>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>

                                    </table>
                                    <h4>Total Rs <b><?php echo number_format($total, 2); ?></h4>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">GRN Issue Chq</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <table id="table" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th style="width: 20%;">Invoice no</th>
                                                <th>Chq No</th>
                                                <th>Chq Bank</th>
                                                <th>Chq Date</th>
                                                <th>Issue</th>
                                                <th>Amount (Rs.)</th>
                                                <th>Realize</th>
                                                <th>Return</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0;
                                            $style = "";
                                            $result = $db->prepare("SELECT *,supply_payment.supplier_invoice AS si, supply_payment.invoice_no AS invo,supply_payment.id AS pid,supply_payment.amount AS pamount FROM supply_payment JOIN bank_balance ON supply_payment.bank_id = bank_balance.id WHERE  supply_payment.action = 1 AND supply_payment.pay_type='Chq'  ORDER BY supply_payment.chq_date ASC ");
                                            $result->bindParam(':userid', $res);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                                $date1 = date_create(date('Y-m-d'));
                                                $date2 = date_create($row['chq_date']);
                                                $date_diff = date_diff($date1, $date2);
                                                $date_diff = $date_diff->format("%R%a");

                                            ?>
                                                <tr id="re_<?php echo $row['pid']; ?>">
                                                    <td><?php echo $row['pid']; ?></td>
                                                    <td>
                                                        <?php if ($row['type'] == 'bulk_payment') {
                                                            $result1 = $db->prepare("SELECT bulk_payment.supplier_invoice AS si FROM bulk_payment WHERE invoice_no = :id ");
                                                            $result1->bindParam(':id', $row['invo']);
                                                            $result1->execute();
                                                            for ($i = 0; $row1 = $result1->fetch(); $i++) {
                                                        ?>
                                                                <span class="badge bg-olive" style="margin: 0;"> <?php echo $row1['si']; ?> </span>
                                                            <?php }
                                                        } else { ?>
                                                            <span class="badge bg-olive"> <?php echo $row['si']; ?> </span>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $row['chq_no']; ?></td>
                                                    <td><?php echo $row['chq_bank']; ?></td>
                                                    <td>
                                                        <?php echo $row['chq_date']; ?>
                                                        <span class="badge bg-blue"><?php echo $date_diff; ?></span>
                                                    </td>
                                                    <td><?php echo $row['supply_name']; ?></td>
                                                    <td><?php echo $row['pamount']; ?></td>
                                                    <?php if ($date_diff <= 0) { ?>
                                                        <td align="center"> <a href="#" id="<?php echo $row['pid']; ?>" unit="grn" onclick="iss_realize(<?php echo $row['pid']; ?>,'grn')" class="btn btn-success" title="Click to Realize"> <i class="fa-solid fa-money-bill-transfer"></i></a></td>
                                                        <td align="center"> <a href="#" id="<?php echo $row['pid']; ?>" unit="grn" onclick="iss_return(<?php echo $row['pid']; ?>,'grn')" class="btn btn-danger" title="Click to Return"> <i class="fa-solid fa-rotate-left"></i> </a></td>
                                                    <?php } else { ?>
                                                        <td colspan="2" align="center"> <span class="badge bg-yellow">Pending</span> </td>
                                                        <!-- <td></td> -->
                                                    <?php }
                                                    $total += $row['pamount']; ?>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>

                                    </table>
                                    <h4>Total Rs <b><?php echo number_format($total, 2); ?></h4>

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
    <!-- SlimScroll -->
    <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="../../plugins/fastclick/fastclick.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>


    <script type="text/javascript">
        function dep_realize(id) {
            var info = 'type=dep_realize&id=' + id;

            $.ajax({
                type: "POST",
                url: "acc_bank_transfer_save.php",
                data: info,
                success: function(res) {
                    console.log(res);
                }
            });

            $('#re_' + id).animate({
                    backgroundColor: "#fbc7c7"
                }, "fast")
                .animate({
                    opacity: "hide"
                }, "slow");
        }

        function iss_realize(id, unit) {
            var info = 'type=iss_realize&id=' + id + '&unit=' + unit;
            $.ajax({
                type: "POST",
                url: "acc_bank_transfer_save.php",
                data: info,
                success: function(res) {
                    console.log(res);
                }
            });

            $('#re_' + id).animate({
                    backgroundColor: "#fbc7c7"
                }, "fast")
                .animate({
                    opacity: "hide"
                }, "slow");
        }

        function dep_return(id) {
            var info = 'type=dep_return&id=' + id;

            $.ajax({
                type: "POST",
                url: "acc_bank_transfer_save.php",
                data: info,
                success: function(res) {
                    console.log(res);
                }
            });

            $('#re_' + id).animate({
                    backgroundColor: "#fbc7c7"
                }, "fast")
                .animate({
                    opacity: "hide"
                }, "slow");
        }

        function iss_return(id, unit) {
            var info = 'type=iss_return&id=' + id + '&unit=' + unit;

            $.ajax({
                type: "POST",
                url: "acc_bank_transfer_save.php",
                data: info,
                success: function(res) {
                    console.log(res);
                }
            });

            $('#re_' + id).animate({
                    backgroundColor: "#fbc7c7"
                }, "fast")
                .animate({
                    opacity: "hide"
                }, "slow");
        }

        $(function() {
            $("#table1").DataTable();
            $("#table2").DataTable();
            $("#table3").DataTable();
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