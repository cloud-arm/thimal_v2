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
    $_SESSION['SESS_FORM'] = '68';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Profile
          <small>Preview</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">

        <div class="row">

          <div class="col-md-3">
            <div class="box box-primary">
              <div class="box-body box-profile">

                <?php
                $id = $_GET['id'];
                $result = $db->prepare("SELECT * FROM damage WHERE complain_no=:id ");
                $result->bindParam(':id', $id);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) { ?>

                  <h3 class="profile-username text-center"><?php echo $name = $row['customer_name']; ?></h3>

                  <ul class="list-group list-group-unbordered">

                    <li class="list-group-item">
                      <b>Complain_no</b> <a class="pull-right"><?php echo $row['complain_no']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Product Type</b> <a class="pull-right"><?php echo $row['cylinder_type']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Cylinder no</b> <a class="pull-right"><?php echo $row['cylinder_no']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Reason</b> <a class="pull-right"><?php echo $row['reason']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Gas Weight</b> <a class="pull-right"><?php echo $row['gas_weight']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Comment</b> <a class="pull-right"><?php echo $row['comment']; ?></a>
                    </li>

                    <li class="list-group-item">
                      <b>Location</b> <a class="pull-right"><?php echo $row['location']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Date</b> <a class="pull-right"><?php echo $date = $row['date']; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>Action</b>

                      <?php
                      $action = $row['type'];
                      if ($action == "damage") {
                      ?>
                        <a class="label bg-red pull-right"><i>Damage</i></a>

                      <?php } else { ?>
                        <a class="label bg-green pull-right"><i><?php echo $action; ?></i></a>
                      <?php } ?>
                    </li>
                  </ul>
                <?php } ?>

                <i class="fa fa-rotate-right fa-spin"></i>

              </div>
              <!-- /.box-body -->
            </div>
          </div>
          <!-- /.col (left) -->

          <div class="col-md-9">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#activity" data-toggle="tab">About</a></li>
                <li class="hidden"><a href="#timeline" data-toggle="tab">Timeline</a></li>
                <li class="hidden"><a href="#settings" data-toggle="tab">Settings</a></li>
              </ul>
              <div class="tab-content">

                <div class="active tab-pane" id="activity">
                  <!-- Post -->
                  <ul class="timeline timeline-inverse">
                    <!-- timeline time label -->
                    <li class="time-label">
                      <span class="bg-blue">
                        Timeline
                      </span>
                    </li>

                    <?php

                    $id = $_GET['id'];
                    $result = $db->prepare("SELECT * FROM damage_order WHERE complain_no=:id ");
                    $result->bindParam(':id', $id);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                      $type = $row['type'];
                      $action = $row['action'];

                    ?>
                      <li>
                        <?php
                        if ($action == "register") {
                          $action_text = 'Register';
                        ?>
                          <i class="fa fa-plus-square bg-yellow"></i>
                        <?php
                        }
                        if ($action == "sent_company") {
                          $action_text = 'Sent to company';
                        ?>
                          <i class="fa fa-external-link-square bg-teal"></i>
                        <?php }    ?>
                        <?php
                        if ($action == "receive_yard") {
                          $action_text = 'Receive to yard';
                        ?>
                          <i class="fa  fa-arrow-circle-down bg-green"></i>
                        <?php
                        }
                        if ($action == "delivery_to_customer") {
                          $action_text = 'Delivery to customer';
                        ?>
                          <i class="fa fa-check-square bg-green"></i>
                        <?php } ?>

                        <div class="timeline-item">
                          <span class="time"><i class="fa fa-calendar-check-o"></i> <?php echo $row['date']; ?></span>

                          <h2 class="timeline-header"><a href="#"> <?php echo $action_text; ?></a></h2>

                          <div class="timeline-body">

                            <th><label>Location_ </label><?php echo $row['location']; ?> </th><br>
                            <th><label>Date:- </label><?php echo $row['date']; ?> </th>
                          </div>
                          <div class="timeline-footer">
                            <?php
                            if ($type == "damage") {
                            ?>
                              <a class="label bg-yellow">Damage</a>
                            <?php
                            } else {

                            ?>
                              <a class="btn btn-success btn-xs"><?php echo $type; ?></a>
                            <?php }    ?>
                          </div>
                        </div>
                      </li>

                    <?php }    ?>
                    <li>
                      <i class="fa fa-refresh fa-spin"></i>
                    </li>
                  </ul>

                  <!-- /.post -->
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane hidden" id="timeline">
                  <!-- The timeline -->
                  <ul class="timeline timeline-inverse">
                    <!-- timeline time label -->
                    <li class="time-label">
                      <span class="bg-red">
                        10 Feb. 2014
                      </span>
                    </li>
                    <!-- /.timeline-label -->
                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-envelope bg-blue"></i>

                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                        <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                        <div class="timeline-body">
                          Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                          weebly ning heekya handango imeem plugg dopplr jibjab, movity
                          jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                          quora plaxo ideeli hulu weebly balihoo...
                        </div>
                        <div class="timeline-footer">
                          <a class="btn btn-primary btn-xs">Read more</a>
                          <a class="btn btn-danger btn-xs">Delete</a>
                        </div>
                      </div>
                    </li>
                    <!-- END timeline item -->
                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-user bg-aqua"></i>

                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>

                        <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request
                        </h3>
                      </div>
                    </li>
                    <!-- END timeline item -->
                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-comments bg-yellow"></i>

                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>

                        <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                        <div class="timeline-body">
                          Take me to your leader!
                          Switzerland is small and neutral!
                          We are more like Germany, ambitious and misunderstood!
                        </div>
                        <div class="timeline-footer">
                          <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                        </div>
                      </div>
                    </li>
                    <!-- END timeline item -->
                    <!-- timeline time label -->
                    <li class="time-label">
                      <span class="bg-green">
                        3 Jan. 2014
                      </span>
                    </li>
                    <!-- /.timeline-label -->
                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-camera bg-purple"></i>

                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                        <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                        <div class="timeline-body">
                          <img src="http://placehold.it/150x100" alt="..." class="margin">
                          <img src="http://placehold.it/150x100" alt="..." class="margin">
                          <img src="http://placehold.it/150x100" alt="..." class="margin">
                          <img src="http://placehold.it/150x100" alt="..." class="margin">
                        </div>
                      </div>
                    </li>
                    <!-- END timeline item -->
                    <li>
                      <i class="fa fa-clock-o bg-gray"></i>
                    </li>
                  </ul>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane hidden" id="settings">
                  <form class="form-horizontal">
                    <div class="form-group">
                      <label for="inputName" class="col-sm-2 control-label">Name</label>

                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputName" placeholder="Name">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputName" class="col-sm-2 control-label">Name</label>

                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="Name">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                      <div class="col-sm-10">
                        <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputSkills" class="col-sm-2 control-label">Skills</label>

                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                            <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-danger">Submit</button>
                      </div>
                    </div>
                  </form>
                </div>
                <!-- /.tab-pane -->

              </div>
              <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>

        </div>
        <!-- /.row -->

      </section>
      <!-- /.content -->
    </div>

    <!-- /.content-wrapper -->
    <?php
    include("dounbr.php");
    ?>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div>
  <!-- ./wrapper -->


  <?php include_once("script.php"); ?>

  <!-- DataTables -->
  <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>
  <!-- AdminLTE App -->

  <!-- Page script -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();


      //Date range picker
      $('#reservation').daterangepicker();
      //Date range picker with time picker
      $('#reservationtime').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        format: 'YYYY/MM/DD h:mm A'
      });
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

      //iCheck for checkbox and radio inputs
      $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
      });
      //Red color scheme for iCheck
      $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
      });
      //Flat red color scheme for iCheck
      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
      });
    });
  </script>
</body>

</html>