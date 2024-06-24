<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
date_default_timezone_set("Asia/Colombo");
?>

<body class="hold-transition skin-yellow sidebar-mini">
    <div class="wrapper" style="overflow-y: hidden;">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '33';

        include_once("sidebar.php");
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    SMS
                    <small>Campaign</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- SELECT2 EXAMPLE -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">New campaign</h3>
                    </div>

                    <div class="box-body">
                        <form method="POST" action="campaign_save.php" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Campaign name</label>
                                        <input type="text" class="form-control" name="name">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control select2 hidden-search" name="type" onchange="selectType(this.options[this.selectedIndex].getAttribute('value'))">
                                            <option value="sms">SMS </option>
                                            <option value="whatsapp">Whatsapp </option>
                                            <option value="email">Email </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3" id="img_sec" style="display: none;">
                                    <div class="form-group">
                                        <label>Upload Image</label>
                                        <label class="form-control" for="img-file" style="cursor: pointer;">
                                            <input accept=".jpg, .jpeg, .png" name="image" id="img-file" type="file" style="opacity: 0;position: absolute;cursor: pointer;">
                                            <i class="fa fa-cloud-upload"></i> Image Upload
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input name="customer" value="0" type="checkbox" checked> All Customer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Enter Message</label>
                                        <textarea class="form-control" name="message" rows="3" placeholder="Enter ..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 23px;">
                                    <div class="form-group">
                                        <input class="btn btn-info" type="submit" value="Submit">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>


            <?php ?>
            <?php ?>

            <section class="content">

                <div class="row">

                    <?php
                    // $ttr="incomplete";
                    $result = $db->prepare("SELECT * FROM sms_campaign GROUP BY campaign_id");
                    $result->bindParam(':userid', $ttr);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                    ?>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box" style="background: rgb(var(--bg-light-50)); border-radius: 10px;position: relative;height: 90px;">
                                <div id="loader<?php echo $row['campaign_id']; ?>" style="position: absolute;width: 100%;height: 100%;background-color: rgba(256,256,256,0.1);display: none; align-items: center;justify-content: center;">
                                    <i class="fa fa-spinner fa-spin" style="font-size: 50px;"></i>
                                </div>
                                <span class="info-box-icon" style="background: rgb(var(--bg-light-50));">
                                    <?php if ($row['type'] == 'sma') {
                                        if ($row['img'] == '') { ?>
                                            <img style="border-radius: 10px 0 0 10px;" src="campaign/img/message.png" alt="">
                                        <?php } else { ?>
                                            <img style="border-radius: 10px 0 0 10px;" src="<?php echo $row['img']; ?>" alt="">
                                    <?php }
                                    } ?>
                                    <?php if ($row['type'] == 'whatsapp') {
                                        if ($row['img'] == '') { ?>
                                            <img style="border-radius: 10px 0 0 10px;" src="campaign/img/whatsapp.png" alt="">
                                        <?php } else { ?>
                                            <img style="border-radius: 10px 0 0 10px;" src="<?php echo $row['img']; ?>" alt="">
                                    <?php }
                                    } ?>
                                    <?php if ($row['type'] == 'email') {
                                        if ($row['img'] == '') { ?>
                                            <img style="border-radius: 10px 0 0 10px;" src="campaign/img/email.png" alt="">
                                        <?php } else { ?>
                                            <img style="border-radius: 10px 0 0 10px;" src="<?php echo $row['img']; ?>" alt="">
                                    <?php }
                                    } ?>
                                </span>
                                <div class="info-box-content" id="camp_det<?php echo $row['campaign_id']; ?>">
                                    <span class="info-box-text">
                                        <?php echo $row['campaign_name']; ?>
                                        <?php if ($row['type'] == 'sms') { ?>
                                            <i class="fa fa-envelope pull-right"></i>
                                        <?php } ?>
                                        <?php if ($row['type'] == 'whatsapp') { ?>
                                            <i class="fa fa-whatsapp pull-right"></i>
                                        <?php } ?>
                                        <?php if ($row['type'] == 'email') { ?>
                                            <i class="fa fa-envelope-o pull-right"></i>
                                        <?php } ?>
                                    </span>
                                    <span style="width: 100%;margin: 5px 0;" class="btn btn-xs btn-success camp_btn" onclick="run_campaign(<?php echo $row['campaign_id']; ?>)">
                                        Run
                                    </span>
                                    <span class="progress-description" style="font-size: 80%;">
                                        Schedule: <?php echo $row['schedule']; ?>
                                    </span>
                                    <span class="progress-description" style="font-size: 80%;display: flex;justify-content: space-around;">
                                        <span>Send: <?php echo $row['send']; ?></span>
                                        <span>Failed: <?php echo $row['failed']; ?></span>
                                    </span>
                                </div>

                            </div>
                        </div>
                    <?php } ?>

                </div>

            </section>
        </div>

        <!-- /.content-wrapper -->
        <?php
        include("dounbr.php");
        ?>

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
    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>

    <script type="text/javascript">
        function run_campaign(id) {
            $('.camp_btn').attr('disabled', '');
            $('#loader' + id).css('display', 'flex');

            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("camp_det" + id).innerHTML = xmlhttp.responseText;
                    $('.camp_btn').removeAttr('disabled');
                    $('#loader' + id).css('display', 'none');
                }
            }

            xmlhttp.open("GET", "campaign_run.php?id=" + id, true);
            xmlhttp.send();
        }

        function selectType(val) {
            if (val == 'whatsapp' || val == 'email') {
                $('#img_sec').css('display', 'block');
            } else {
                $('#img_sec').css('display', 'none');
            }
        }

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
                format: 'yyyy/mm/dd '
            });
            $('#datepicker').datepicker({
                autoclose: true
            });



            $('#datepickerd').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy/mm/dd '
            });
            $('#datepickerd').datepicker({
                autoclose: true
            });


        });
    </script>


</body>

</html>