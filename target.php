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
        date_default_timezone_set("Asia/Colombo");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '38';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Target & Achievement
                    <small>Preview</small>
                </h1>
            </section>

            <section class="content">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Target List</h3>
                        <small class="btn btn-success btn-sm mx-2" style="padding: 5px 10px;" title="Add New Target" onclick="click_open(1)">Add New Target</small>
                    </div>

                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-hover">
                            <thead>

                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Target</th>
                                    <th>Achievement</th>
                                    <th>Balance</th>
                                    <th>#</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $month = date('Y-m');
                                $d1 = $month . '-01';
                                $d2 = $month . '-31';
                                $result = $db->prepare("SELECT * FROM target WHERE month = '$month' ");
                                $result->bindParam(':id', $d);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {

                                    $achieve = 0;
                                    $balance = 0;
                                    $bonus = 0;
                                    $target = $row['target'];

                                    $res = $db->prepare("SELECT sum(qty) FROM sales_list WHERE product_id = :id AND date BETWEEN '$d1' AND '$d2' ");
                                    $res->bindParam(':id', $row['product_id']);
                                    $res->execute();
                                    for ($k = 0; $ro = $res->fetch(); $k++) {
                                        $achieve = $ro['sum(qty)'];
                                    }

                                    if ($target > $achieve) {
                                        $balance = $target - $achieve;
                                    } else {
                                        $bonus = $achieve - $target;
                                    }

                                    $sql = "UPDATE target  SET achievement = ?, bonus = ?, balance = ? WHERE id=?";
                                    $q = $db->prepare($sql);
                                    $q->execute(array($achieve, $bonus, $balance, $row['id']));
                                ?>
                                    <tr class="record">
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['product_name']; ?></td>
                                        <td><?php echo $row['target']; ?></td>
                                        <td>
                                            <?php echo $row['achievement']; ?>
                                            <?php if ($row['bonus'] > 0) { ?>
                                                <br> <span class="badge bg-yellow">Bonus: <?php echo $row['bonus']; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td> <?php echo $row['balance']; ?> </td>
                                        <td>
                                            <a href="#" id="<?php echo $row['id']; ?>" title="Click to Delete" class="btn btn-danger btn-sm btn_dll">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php  }  ?>
                            </tbody>


                        </table>

                    </div>
                    <!-- /.box-body -->
                </div>

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php
        include("dounbr.php");
        ?>

        <div class="container-up d-none" id="container_up">
            <div class="container-close" onclick="click_close()"></div>
            <div class="row">
                <div class="col-md-12">

                    <div class="box box-success popup d-none" id="popup_1" style="width: 500px;">
                        <div class="box-header with-border">
                            <h3 class="box-title w-100" style="display: flex;justify-content: space-between;align-items: center;">
                                New Target
                                <label style="color: rgb(var(--bg-theme-yellow-100));margin: 0;">Target Month: <?php echo date("Y-M"); ?></label>
                                <i onclick="click_close()" class="btn me-2  fa fa-remove" style="font-size: 25px"></i>
                            </h3>
                        </div>

                        <div class="box-body d-block">
                            <form method="POST" action="target_save.php">

                                <div class="row" style="display: block;">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Product</label>
                                            <select class="form-control select2" name="product" style="width: 100%;" autofocus>
                                                <?php
                                                $result = $db->prepare("SELECT * FROM products ORDER by product_id ASC ");
                                                $result->bindParam(':id', $date);
                                                $result->execute();
                                                for ($i = 0; $row = $result->fetch(); $i++) {
                                                ?>
                                                    <option value="<?php echo $row['product_id']; ?>"><?php echo $row['gen_name']; ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 hidden">
                                        <div class="form-group">
                                            <label>Month</label>
                                            <input type="hidden" value="<?php echo date("Y-m"); ?>" name="month">
                                            <select class="form-control " name="" style="width: 100%;" tabindex="1" autofocus>
                                                <?php for ($x = 1; $x <= 12; $x++) { ?>
                                                    <option> <?php echo sprintf("%02d", $x); ?> </option>
                                                <?php  } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Target</label>
                                            <input type="number" step=".01" value="" name="target" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group" style="display: flex;justify-content:center">
                                            <input type="submit" style="width: 80%;margin-top:20px;" value="Save" class="btn btn-info">
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

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
        function click_open(i) {
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }
    </script>
    <!-- page script -->
    <script>
        $(function() {
            $(".btn_dll").click(function() {
                var element = $(this);
                var del_id = element.attr("id");
                var info = 'id=' + del_id;
                if (confirm("Sure you want to delete this Target? There is NO undo!")) {
                    $.ajax({
                        type: "GET",
                        url: "target_dll.php",
                        data: info,
                        success: function() {

                        }
                    });
                    $(this).parents(".record").animate({
                            backgroundColor: "#fbc7c7"
                        }, "fast")
                        .animate({
                            opacity: "hide"
                        }, "slow");

                }

                return false;

            });

        });



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
        });
    </script>

    <script>
        $(function() {
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
            $('#daterange-btn').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function(start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }
            );

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker').datepicker({
                autoclose: true
            });


            $('#datepicker_set').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker_set').datepicker({
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