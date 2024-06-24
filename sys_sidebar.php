<!DOCTYPE html>
<html>
<?php
include("head.php");
date_default_timezone_set("Asia/Colombo");
?>

<body class="hold-transition skin-yellow sidebar-mini " style="overflow-y: scroll;">
    <div class="wrapper">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '0';
        $_SESSION['SESS_DEPARTMENT'] = 'sidebar';

        include_once("sidebar.php");


        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="margin-left: 10px;z-index: 1000;position: relative;">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    SIDEBAR
                    <small>Preview</small>
                    <span onclick="reset_order()" class="btn btn-danger btn-sm pull-right mx-2">Order Reset</span>
                    <span onclick="click_open(1)" class="btn btn-primary btn-sm pull-right mx-2">New Sidebar</span>
                    <form action="sys_sidebar_save.php" method="POST" id="reset-form">
                        <input type="hidden" value="6" name="unit">
                    </form>
                </h1>
            </section>

            <section class="content">

                <div class="row">
                    <?php
                    $sections = select("sys_section", "name");
                    foreach ($sections as $i => $section) {
                        $section = $section["name"];
                    ?>

                        <div class="col-md-3">
                            <div class="box box-info">

                                <div class="box-header with-border">
                                    <h3 class="box-title" style="text-transform: capitalize;margin-bottom: 10px;"><?php echo $section; ?></h3>
                                    <span class="fa fa-times btn btn-box-tool pull-right" style="display: none;" id="close-<?php echo $section; ?>" onclick="close_form('<?php echo $section; ?>')"></span>
                                    <div style="display: none;" id="form-<?php echo $section; ?>">
                                        <form action="sys_sidebar_save.php" method="POST" style="display: flex;justify-content: space-around;">

                                            <input type="number" id="txt1-<?php echo $section; ?>" name="from" value="0" class="form-control" style="width: 30%;text-align:center;" readonly>

                                            <input type="number" id="txt2-<?php echo $section; ?>" name="id" value="0" class="form-control" style="width: 30%;text-align:center;" readonly>

                                            <input type="submit" value="save" class="btn btn-sm btn-default" style="border-radius: 10px;">
                                            <input type="hidden" value="5" name="unit">

                                        </form>
                                    </div>
                                </div>

                                <div class="box-body d-block">
                                    <table id="example<?php echo $i; ?>" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Sidebar</th>
                                                <th>Order_No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result = select("sys_sidebar", "*", "`" . strtolower($section) . "` = 1 AND type = 'main' ORDER BY order_id ");
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <tr style="cursor: pointer;" onclick="set_row('<?php echo $row['id'];  ?>','<?php echo $section; ?>')">
                                                    <td><?php echo $row['id'];  ?></td>
                                                    <td>
                                                        <i class="<?php echo $row['icon']; ?>"></i>
                                                        <?php echo $row['name'];  ?>
                                                    </td>
                                                    <td align="center"><?php echo $row['order_id']  ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                </div>
            </section>
        </div>

        <?php
        $con = 'd-none';
        ?>

        <div class="container-up <?php echo $con; ?>" id="container_up">
            <div class="row w-100">

                <!-- sidebar -->
                <div class="box box-success popup d-none" id="popup_1" style="width: 80%;">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            New Sidebar
                        </h3>
                        <small class="btn btn-sm btn-primary mx-2" style="padding: 5px 10px;" title="Add Icon" onclick="click_open(2)">New Icon</small>
                        <small class="btn btn-sm btn-warning mx-2" style="padding: 5px 10px;" title="Add Section" onclick="click_open(3)">New Section</small>
                        <small class="btn btn-sm btn-danger mx-2" style="padding: 5px 10px;" title="Permission ARM" onclick="click_open(4)">Permission ARM</small>
                        <small onclick="click_close(1)" class="btn btn-sm btn-success pull-right"><i class="fa fa-times"></i></small>
                    </div>

                    <div class="box-body d-block">
                        <form method="POST" action="sys_sidebar_save.php">

                            <div class="row" style="display: block;">

                                <div class="col-md-12">
                                    <div class="row" style="margin-top: 10px;">
                                        <?php
                                        $result = select("sys_section", "*", "action = 1  ");
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                        ?>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" name="<?php echo $row['name']; ?>" value="1" class="flat-red <?php echo $row['id']; ?>"> <?php echo ucfirst($row['name']); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" value="" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Icon</label>
                                        <i id="icon-preview" class=""></i>
                                        <select class="form-control select2" onchange="icon_preview(this.options[this.selectedIndex].getAttribute('icon-name'))" name="icon" style="width: 100%;" autofocus>
                                            <option value="0" disabled selected>Select icon</option>
                                            <?php
                                            $result = select("sys_icon", "*", "action = 1 ORDER BY sn DESC ");
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <option value="<?php echo $row['sn']; ?>" icon-name="<?php echo $row['name']; ?>"> <?php echo $row['name']; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control select2 hidden-search" onchange="select_type(this.value)" name="type" style="width: 100%;" autofocus>

                                            <option value="main"> Main </option>
                                            <option value="sub1"> Sub 01 </option>
                                            <option value="sub2"> Sub 02 </option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" name="link" value="#" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3 sub1-sec sub2-sec sub-sec" style="display:none">
                                    <div class="form-group">
                                        <label>Main Section</label>
                                        <select class="form-control select2" name="main" onchange="select_main(this.value)" style="width: 100%;" autofocus>
                                            <option value="0" disabled selected>Select main section</option>
                                            <?php
                                            $result = select("sys_sidebar", "*", "sub = 1 AND type = 'main' ");
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <option value="<?php echo $row['id']; ?>"> <?php echo ucfirst($row['name']); ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 sub2-sec sub-sec" style="display:none">
                                    <div class="form-group">
                                        <label>Sub 01 Section</label>
                                        <select class="form-control select2" name="sub1" id="sub2" style="width: 100%;" autofocus>
                                            <option value="0" disabled selected>Select Sub Section</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="hidden" name="unit" value="1">
                                        <input type="submit" style="margin-top: 23px; width: 100%;" value="Save" class="btn btn-info btn-sm">
                                    </div>
                                </div>

                                <div class="col-md-2 pull-right">
                                    <div class="form-group" style="margin: 23px 0 0;">
                                        <label>
                                            <input type="checkbox" name="sub" value="1" class="flat-red 1"> Sub Action
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Icon -->
                <div class="box box-success popup d-none" id="popup_2" style="width: 450px;">
                    <div class="box-header with-border">
                        <h3 class="box-title w-100">
                            New Icon
                            <i onclick="click_close(0)" class="btn btn-box-tool p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
                        </h3>
                    </div>

                    <div class="box-body d-block">
                        <form method="POST" action="sys_sidebar_save.php">

                            <div class="row" style="display: block;">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Icon class</label>
                                        <input type="text" name="name" id="txt_icon" value="" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" name="unit" value="2">
                                        <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Section -->
                <div class="box box-success popup d-none" id="popup_3" style="width: 450px;">
                    <div class="box-header with-border">
                        <h3 class="box-title w-100">
                            New Section
                            <i onclick="click_close(0)" class="btn btn-box-tool p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
                        </h3>
                    </div>

                    <div class="box-body d-block">
                        <form method="POST" action="sys_sidebar_save.php">

                            <div class="row" style="display: block;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Section</label>
                                        <input type="text" name="name" id="txt_sec" value="" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" name="link" value="#" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="unit" value="3">
                                        <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Permission -->
                <div class="box box-success popup d-none" id="popup_4" style="width: 600px;">
                    <div class="box-header with-border">
                        <h3 class="box-title w-100">
                            Permission arm
                            <i onclick="click_close(0)" class="btn btn-box-tool p-0 me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
                        </h3>
                    </div>

                    <div class="box-body d-block">
                        <form method="POST" action="sys_sidebar_save.php">

                            <div class="row" style="display: block;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Menu Section</label>
                                        <select class="form-control select2" id="menu_sec" name="menu" style="width: 100%;" autofocus>
                                            <!-- <option value="0" disabled selected>Select menu section</option> -->
                                            <?php
                                            $result = select("sys_sidebar", "*", "id > 0 ORDER BY id DESC  ");
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <option value="<?php echo $row['id']; ?>"> <?php echo ucfirst($row['name']); ?> (<?php echo $row['link']; ?>) </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <input type="text" name="type" value="user_level" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User Level</label>
                                        <?php $val = 0;
                                        $result = select("sys_permission_arm", "user_level", "user_level>0 ORDER BY id DESC LIMIT 1 ");
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            $val = $row['user_level'] + 1;
                                        }
                                        ?>
                                        <input type="number" name="level" id="txt_perm" value="<?php echo $val; ?>" class="form-control" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group flex-center" style="justify-content: space-around;margin-top: 23px;">
                                        <label style="cursor: pointer;" for="radio1" onclick="select_radio('sidebar')">
                                            <input type="radio" id="radio1" value="sidebar" onclick="select_radio('sidebar')" name="section" class="flat-red 1" checked>
                                            Sidebar
                                        </label>
                                        <label style="cursor: pointer;" for="radio2" onclick="select_radio('header')">
                                            <input type="radio" id="radio2" value="header" onclick="select_radio('header')" name="section" class="flat-red 3">
                                            Header
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" name="unit" value="4">
                                        <input type="submit" style="margin-top: 23px; width: 100%;" value="Save" class="btn btn-info">
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>

        <div class="control-sidebar-bg"></div>
    </div>

    <?php include_once("script.php"); ?>

    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- iCheck 1.0.1 -->
    <script src="../../plugins/iCheck/icheck.min.js"></script>
    <!-- FastClick -->
    <script src="../../plugins/fastclick/fastclick.js"></script>

    <script type="text/javascript">
        <?php
        if (isset($_GET['end'])) {
            $con = $_GET['end'];
            echo "click_open($con);";
        }
        ?>

        function icon_preview(val) {
            $('#icon-preview').removeClass($('#icon-preview').attr('class'));
            $('#icon-preview').addClass(val);
        }

        function reset_order() {
            if (confirm("Sure you want to reset sidebar ordering? There is NO undo!")) {
                $('#reset-form').submit();
            }
            return false;
        }

        function set_row(id, section) {
            $('#close-' + section).css('display', 'block');
            $('#form-' + section).css('display', 'inline-block');

            let id1 = $("#txt1-" + section).val();
            let id2 = $("#txt2-" + section).val();

            if (id1 == '0' && id2 != id) {
                $("#txt1-" + section).val(id);
            } else
            if (id2 == '0' && id1 != id) {
                $("#txt2-" + section).val(id);
            }

        }

        function close_form(section) {
            $("#close-" + section).css('display', 'none');
            $("#form-" + section).css('display', 'none');

            $("#txt1-" + section).val(0);
            $("#txt2-" + section).val(0);
        }

        function select_type(val) {
            $('.sub-sec').css('display', 'none');
            $('.' + val + '-sec').css('display', 'block');
        }

        function select_main(id) {

            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("sub2").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "sys_sidebar_get.php?unit=1&val=" + id, true);
            xmlhttp.send();

        }

        function click_open(i) {
            $(".popup").addClass("d-none");
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");

            if (i == 2) {
                $('#txt_icon').focus();
            }
            if (i == 3) {
                $('#txt_sec').focus();
            }
            if (i == 4) {
                $('#txt_perm').focus();
            }
        }

        function click_close(i) {
            if (i) {
                $(".popup").addClass("d-none");
                $("#container_up").addClass("d-none");
            } else {
                $(".popup").addClass("d-none");
                $("#popup_1").removeClass("d-none");
            }
        }

        function select_radio(val) {
            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("menu_sec").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "sys_sidebar_get.php?unit=2&val=" + val, true);
            xmlhttp.send();
        }
    </script>

    <script>
        $(function() {
            <?php
            $sections = select("sys_section", "name");
            foreach ($sections as $i => $section) {
                $section = $section["name"];

                echo '$("#example' . $i . '").DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false
                });';
            } ?>

            $("input").focus(function() {
                $(this).select();
            });

            //Initialize Select2 Elements
            $(".select2").select2();
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-navy',
                radioClass: 'iradio_flat-navy'
            });

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red.1, input[type="radio"].flat-red.1').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red.2, input[type="radio"].flat-red.2').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red.3, input[type="radio"].flat-red.3').iCheck({
                checkboxClass: 'icheckbox_flat-red',
                radioClass: 'iradio_flat-red'
            });

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red.4, input[type="radio"].flat-red.4').iCheck({
                checkboxClass: 'icheckbox_flat-orange',
                radioClass: 'iradio_flat-orange'
            });

            <?php
            if (isset($_GET['err'])) {
                if ($_GET['err'] == 1) {
                    echo "$('#name-sel + .select2 > .selection > .select2-selection').css('border-color','red');";
                }
            }
            ?>
        });
    </script>

</body>

</html>