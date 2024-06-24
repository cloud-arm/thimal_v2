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
        $u = $_SESSION['SESS_MEMBER_ID'];
        $_SESSION['SESS_FORM'] = '';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Requests Confirmation
                    <small>Preview</small>
                </h1>

            </section>
            <!-- Main content -->
            <section class="content">
                <!-- SELECT2 EXAMPLE -->
                <div class="row">
                    <?php
                    $result = $db->prepare("SELECT * FROM requests_data WHERE action=0 ");
                    $result->bindParam(":id", $date);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                    ?>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-envelope-o"></i></span>

                                <div class="info-box-content" style="display: flex;flex-direction: column;justify-content: space-between;height: 90px;padding: 10px;">
                                    <span class="info-box-text" title="<?php echo $row['customer_name']; ?>"><?php echo $row['customer_name']; ?></span>
                                    <span class="info-box-number"></span>
                                    <div style="display: flex;justify-content: space-around;">
                                        <button class="btn btn-info btn-xs" onclick="click_open(2,'<?php echo $row['id']; ?>')">Cancel</button>
                                        <button class="btn btn-danger btn-xs" onclick="click_open(1,'<?php echo $row['id']; ?>','<?php echo $row['customer_name']; ?>','<?php echo $row['note']; ?>')">Confirm</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <form method="POST" action="requests_confirm_save.php" id="process-form-<?php echo $row['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="1">
                        </form>

                        <form method="POST" action="requests_confirm_save.php" id="cancel-form-<?php echo $row['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="action" value="5">
                        </form>

                    <?php } ?>

                    <input type="hidden" id="data_id" value="0">
                </div>
            </section>

        </div>
        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>

        <?php
        $err = 'd-none';
        $err1 = 'd-none';
        $closer = '';

        if (isset($_GET['err'])) {
            if ($_GET['err'] == 1) {
                $err = '';
                $err1 = '';
                $closer = '<div class="container-close" onclick="click_close()"></div>';
            } else {
                $err = 'd-none';
                $err1 = 'd-none';
                $closer = '';
            }
        } ?>

        <div class="container-up <?php echo $err; ?>" id="container_up">
            <?php echo $closer; ?>
            <div class="row">
                <div class="col-md-12">

                    <div class="box box-success popup  <?php echo $err1; ?>" style="padding: 5px;border: 0;">
                        <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
                            <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
                            This Item already ADD ..!
                        </div>
                        <a href="#" class="btn btn-warning" onclick="click_close()" style="margin: 10px 0; width: 100%;"><b>Add New Item</b></a>
                    </div>

                    <div class="box box-success popup d-none" id="popup_1" style="width: 400px;display: flex;flex-direction: column;justify-content: space-between;">

                        <h4>Create a new invoice for this customer? </h4>
                        <hr style="margin: 10px 0;border-color:#999;">
                        <h5 id="con_cus"></h5>
                        <h5 id="con_note"></h5>
                        <hr style="margin: 10px 0;border-color:#999;">
                        <div style="display: flex;align-items:center;justify-content:space-around;margin:10px 0;">
                            <button onclick="check_process(0)" style="width: 100px;" class="btn btn-danger">Cancel</button>
                            <button onclick="check_process(1)" style="width: 100px;" class="btn btn-primary">Confirm</button>
                        </div>
                    </div>

                    <div class="box box-success popup d-none" id="popup_2" style="width: 400px;display: flex;flex-direction: column;justify-content: space-between;">

                        <h4>Are you sure this should be cancelled? </h4>
                        <hr style="margin: 10px 0;border-color:#999;">
                        <div style="display: flex;align-items:center;justify-content:space-around;margin:10px 0;">
                            <button onclick="check_cancel(1)" style="width: 100px;" class="btn btn-danger">Yes</button>
                            <button onclick="check_cancel(0)" style="width: 100px;" class="btn btn-primary">No</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="control-sidebar-bg"></div>
    </div>

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
        function click_open(i, id, name, note) {
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");

            if (i == 1) {
                $('#con_cus').text(name);
                $('#con_note').text(note);
                $('#data_id').val(id);
            }

            if (i == 2) {
                $('#data_id').val(id);
            }
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }

        function check_cancel(val) {
            let id = $('#data_id').val();
            if (val) {
                $('#cancel-form-' + id).submit();
            } else {
                click_close();
            }
        }

        function check_process(val) {
            let id = $('#data_id').val();
            if (val) {
                $('#process-form-' + id).submit();
            } else {
                click_close();
            }
        }
    </script>

    <script type="text/javascript">
        $(function() {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            //Initialize Select2 Elements
            $(".select2").select2();
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });
            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            //$('#datepicker').datepicker({datepicker: true,  format: 'yyyy/mm/dd '});
            //Date range as a button


            //Date picker
            $('#datepicker').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });

        });
    </script>

</body>

</html>