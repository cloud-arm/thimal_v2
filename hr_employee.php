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
        $_SESSION['SESS_FORM'] = '11';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->


            <section class="content-header">
                <h1>
                    Employee
                    <small>Preview</small>
                </h1>
            </section>
            <section class="content">


                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Employee Data</h3>
                        <small class="btn btn-info btn-sm mx-2" style="padding: 5px 10px;" title="Add Employee" onclick="click_open(1)">Add Employee</small>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <?php
                        $data = [
                            "ID" => 'id',
                            "Name" => 'name',
                            "Phone_NO" => 'phone_no',
                            "NIC" => "nic",
                            "EPF" => "epf_amount@font_txt@Rs",
                            "EPF_No" => "epf_no",
                            "Designation" => "des",
                            "Hour_Rate" => "hour_rate@font_txt@Rs",
                            "#" => '<a href="hr_employee_profile.php?id=#id#" class="btn btn-info btn-sm"><i class="fa fa-user"></i></a>'
                        ];

                        echo table("table1", $data, 'employee');
                        ?>
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
                <div class="col-md-6 with-scroll">

                    <div class="box box-success popup d-none" id="popup_1">
                        <div class="box-header with-border">
                            <h3 class="box-title w-100">
                                New Employee Add
                                <i onclick="click_close()" class="btn p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
                            </h3>
                        </div>

                        <div class="box-body d-block">
                            <form method="POST" action="hr_employee_save.php" enctype="multipart/form-data">

                                <div class="row" style="display: block;">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input class="form-control" type="text" name="name">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nick Name</label>
                                            <input class="form-control" type="text" name="nickname">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone No</label>
                                            <input class="form-control" type="text" name="phone_no">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NIC</label>
                                            <input class="form-control" type="text" name="nic">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input class="form-control" type="text" name="address">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Day Rate</label>
                                            <input class="form-control" type="text" name="rate">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Designation</label>
                                            <select class="form-control select2" style="width: 100%;" onchange="des_select(this.options[this.selectedIndex].getAttribute('value'))" name="des">
                                                <?php
                                                $result = $db->prepare("SELECT * FROM employees_des ");
                                                $result->bindParam(':userid', $res);
                                                $result->execute();
                                                for ($i = 0; $row = $result->fetch(); $i++) {
                                                ?>
                                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 drive_sec" style="display: block;">
                                        <div class="form-group">
                                            <label>User Name</label>
                                            <input class="form-control" type="text" name="username">
                                        </div>
                                    </div>

                                    <div class="col-md-6 drive_sec" style="display: block;">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input class="form-control" type="text" name="password">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>EPF NO <span id="epf_err" style="color: #ff0000;display: none">* This number is duplicate !!</span></label>
                                            <input class="form-control" onkeyup="epf_get()" id="epf_txt" type="text" name="epf_no">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>EPF Amount</label>
                                            <input class="form-control" type="text" name="epf_amount">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>OT</label>

                                            <select class="form-control select2 hidden-search" style="width: 100%;" name="ot" id="">
                                                <option value="1">Eligible</option>
                                                <option value="2">Not Eligible</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Welfare Amount</label>
                                            <input class="form-control" type="text" name="well">
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="display: flex;justify-content: space-around;gap: 10px;">
                                        <div class="form-group" style="width: 100%;">
                                            <label for="img-file">Upload Image</label>
                                            <label class="form-control" for="img-file" style="cursor: pointer;">
                                                <input accept=".jpg, .jpeg, .png" name="image" id="img-file" onchange="previewImage(event)" type="file" style="opacity: 0;position: absolute;">
                                                <i class="fa fa-cloud-upload"></i> Custom Upload
                                            </label>
                                        </div>
                                        <div class="form-img" id="img-preview" style="display: none;">
                                            <img src="#" id="img" alt="user_pic">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="0">
                                            <input id="emp_save" type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info w-100">
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
    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
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
        function click_open(i) {
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }
    </script>

    <script>
        function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();

            reader.onload = function() {
                var img_preview = document.getElementById("img-preview");
                var img = document.getElementById("img");
                img.src = reader.result;
                img_preview.style.display = "flex";
            };

            reader.readAsDataURL(input.files[0]);
        }

        function des_select(id) {
            if (id == 1) {
                $('.drive_sec').css('display', 'block');
            } else {
                $('.drive_sec').css('display', 'none');
            }
        }

        function epf_get() {

            var val = document.getElementById('epf_txt').value;

            var info = 'id=' + val;
            $.ajax({
                type: "GET",
                url: "hr_epf_get.php",
                data: info,
                success: function(resp) {
                    if (resp == '1') {
                        document.getElementById("epf_err").style.display = "inline";
                        document.getElementById("emp_save").setAttribute("disabled", "");
                    } else {
                        document.getElementById("epf_err").style.display = "none";
                        document.getElementById("emp_save").removeAttribute("disabled");
                    }
                }
            });


        }

        $(function() {
            $("#table1").DataTable();
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
        });
    </script>
</body>

</html>