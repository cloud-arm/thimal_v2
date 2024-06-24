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
        $_SESSION['SESS_FORM'] = '73';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Root Details
                    <small>Preview</small>
                </h1>
            </section>

            <section class="content">

                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">
                            Root Customer
                        </h3>
                        <small class="btn btn-success mx-2" style="padding: 5px 10px;" title="Add Customer" onclick="click_open(1)">Add Customer</small>

                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">

                            <thead>
                                <tr>
                                    <th>ID.</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>contact</th>
                                    <th>#</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php

                                $result = $db->prepare("SELECT * FROM customer  WHERE root_id = :id ");
                                $result->bindParam(':id', $_GET['id']);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                ?>
                                    <tr class="record">
                                        <td><?php echo $row['customer_id']; ?></td>
                                        <td><?php echo $row['customer_name']; ?></td>
                                        <td><?php echo $row['address']; ?></td>
                                        <td><?php echo $row['contact']; ?></td>
                                        <td>
                                            <a href="#" id="<?php echo $row['customer_id']; ?>" class="delbutton" title="Click to Delete">
                                                <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php  } ?>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

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

                    <div class="box box-success popup d-none" id="popup_1">
                        <div class="box-header with-border">
                            <h3 class="box-title w-100">New Customer <i onclick="click_close()" class="btn p-0 me-2  pull-right fa fa-remove" style="font-size: 25px;"></i></h3>
                        </div>

                        <div class="box-body d-block">
                            <form method="POST" action="root_customer_save.php">
                                <div class="row" style="display: block;">

                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <select class="form-control select2" style="width: 100%;" name="id">
                                                <?php
                                                $result = $db->prepare("SELECT * FROM customer  ");
                                                $result->bindParam(':id', $res);
                                                $result->execute();
                                                for ($i = 0; $row = $result->fetch(); $i++) {
                                                ?>
                                                    <option value="<?php echo $row['customer_id']; ?>"><?php echo $row['customer_name']; ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="hidden" name="root" value="<?php echo $_GET['id']; ?>">
                                            <input type="submit" style="margin-top: 23px;" value="Add" class="btn btn-info">
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
    <!-- page script -->
    <script>
        function click_open(i, id) {
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }
        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();

            $("#example1").DataTable({
                "language": {
                    "paginate": {
                        "next": "<i class='fa fa-angle-double-right'></i>",
                        "previous": "<i class='fa fa-angle-double-left'></i>"
                    }
                }
            });
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
    <script type="text/javascript">
        $(function() {


            $(".delbutton").click(function() {

                //Save the link in a variable called element
                var element = $(this);

                //Find the id of the link that was clicked
                var del_id = element.attr("id");

                //Built a url to send
                var info = 'id=' + del_id;
                if (confirm("Sure you want to delete this Root? There is NO undo!")) {

                    $.ajax({
                        type: "GET",
                        url: "root_customer_dll.php",
                        data: info,
                        success: function(res) {
                            console.log(res);
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
    </script>
</body>

</html>