<header class="main-header">
  <!-- Logo -->
  <a href="index.php" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>arm</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><i class="fa fa-cloud"></i><b>CLOUD ARM</b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>
    <?php
    include('connect.php');
    include('config.php');
    date_default_timezone_set("Asia/Colombo");

    $date =  date("Y-m-d");
    $dep = $_SESSION['SESS_DEPARTMENT'];
    $f = '';
    $menu_action = $_SESSION['SESS_FORM'];
    $pos = $_SESSION['SESS_LAST_NAME'];
    $user_level = $_SESSION['USER_LEWAL'];
    $main_menu_action = 0;
    $sub1_menu_action = 0;

    $data = select('sys_sidebar', 'main_id,type', ' id = ' . $menu_action);
    foreach ($data as $row) {
      if ($row['type'] == 'sub1') {
        $main_menu_action = $row['main_id'];
      }

      if ($row['type'] == 'sub2') {
        $sub1_menu_action = $row['main_id'];
        $data1 = select('sys_sidebar', 'main_id,type', ' id = ' . $sub1_menu_action);
        foreach ($data1 as $row1) {
          $main_menu_action = $row1['main_id'];
        }
      }
    }
    ?>
    <div class="navbar-menu">
      <ul class="nav navbar-nav">
        <?php
        $result = $db->prepare("SELECT *,sys_section.id AS sn FROM sys_section JOIN sys_permission_arm ON sys_section.id=sys_permission_arm.menu_id WHERE sys_permission_arm.user_level = '$user_level' AND sys_permission_arm.type = 'user_level' AND sys_permission_arm.section = 'header' AND sys_permission_arm.action = 1 AND sys_section.action = 1  ");
        $result->bindParam(':id', $id);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
        ?>

          <li class="<?php if ($dep == $row['name']) {
                        echo 'open';
                      } ?>">
            <a href="<?php echo $row['link']; ?>"><?php echo ucfirst($row['name']); ?></a>
          </li>

        <?php }  ?>
      </ul>
    </div>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <?php
        $userid = $_SESSION['SESS_MEMBER_ID'];
        $result1 = $db->prepare("SELECT * FROM user WHERE id='$userid' ");
        $result1->bindParam(':userid', $res);
        $result1->execute();
        for ($i = 0; $row1 = $result1->fetch(); $i++) {
          $upic1 = $row1['upic'];
        }

        ?>
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" onclick="open_profile(1)">
            <img src="../../dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
            <span class="hidden-xs user-data-header"><?php echo $_SESSION['SESS_FIRST_NAME']; ?></span>
            <span class="hidden-xs profile-data-header d-none"><i class="fa fa-times mx-2"></i></span>
          </a>
          <ul class="dropdown-menu user">
            <!-- User image -->
            <li class="user-header">
              <div>
                <span class="btn btn-default btn-xs user-data" onclick="open_profile(2)">Change Password</span>
                <span class="badge"><i class="glyphicon glyphicon-user mx-2"></i><?php echo $_SESSION['SESS_LAST_NAME']; ?></span>
                <span class="badge profile-data d-none" onclick="open_profile(3)" style="cursor: pointer;"><i class="fa fa-times mx-2"></i></span>
              </div>
              <img src="../../dist/img/user2-160x160.jpg" class="img-circle user-data" alt="User Image">
            </li>
            <!-- Menu Body -->
            <li class="user-body user-data">
              <p> <?php echo $_SESSION['SESS_FIRST_NAME']; ?></p>
              <small>Member since Nov. 2023</small>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer user-data">
              <div class="pull-right" style="width: 110px;">
                <a href=" ../../../index.php" class="btn btn-default btn-flat">Sign out</a>
              </div>
            </li>

            <!-- Menu Body -->
            <li class="user-body profile-data d-none">
              <form action="user_update.php" method="POST">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="password" name="old" class="form-control" placeholder="New Password">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="password" name="new" class="form-control" placeholder="Verify Password">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="submit" class="btn btn-default btn-flat" value="Save">
                      <input type="hidden" name="id" value="<?php echo $userid; ?>">
                    </div>
                  </div>
                </div>
              </form>
            </li>
          </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <li>
          <a href="#" onclick="clearCache()"><i class="fa fa-refresh"></i></a>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <li>
          <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
        </li>
        <!-- Control Sidebar maintains -->
        <?php if ($user_level == 1) { ?>
          <li>
            <a href="sys_sidebar.php"><i class="fa-solid fa-sliders"></i></a>
          </li>
        <?php } ?>
      </ul>
    </div>

    <div class="navbar-search">
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="" id="search-txt" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
          </span>
        </div>
      </form>
    </div>
  </nav>
</header>



<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <?php if ($dep=='sidebar') {
      } else { ?>
        <li class="header">MAIN NAVIGATION</li>


        <!-- <span class="pull-right-container">
          <i class="fa fa-spinner fa-spin pull-right"></i>
        </span> -->

        <?php
        $result = $db->prepare("SELECT *,sys_sidebar.id AS sn FROM sys_sidebar JOIN sys_permission_arm ON sys_sidebar.id=sys_permission_arm.menu_id WHERE sys_permission_arm.user_level = '$user_level' AND `sys_sidebar`.`$dep` = 1 AND sys_permission_arm.type = 'user_level' AND sys_permission_arm.section = 'sidebar' AND sys_sidebar.type='main' AND sys_sidebar.action = 1 AND sys_permission_arm.action = 1 ORDER BY sys_sidebar.order_id ");
        $result->bindParam(':id', $id);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
          $linkR = explode('.', $row['link']);
          $link = $linkR[0];
          if ($row['sub']) {

            $con = '';
            $con0 = '';
            $dis = 'none';
            if ($main_menu_action == $row['sn']) {
              $con = 'active';
              $con0 = 'menu-open';
              $dis = 'block';
            }
        ?>

            <li class="treeview  <?php echo $con; ?>">
              <a href="#"><i class="<?php echo $row['icon']; ?>"></i><span><?php echo ucfirst($row['name']); ?></span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu  <?php echo $con0; ?>" style="display:  <?php echo $dis; ?>;">

                <?php
                $result1 = $db->prepare("SELECT *,sys_sidebar.id AS sn FROM sys_sidebar JOIN sys_permission_arm ON sys_sidebar.id=sys_permission_arm.menu_id WHERE `sys_sidebar`.`$dep` = 1 AND  sys_sidebar.type='sub1' AND sys_sidebar.main_id = :id AND sys_permission_arm.user_level = '$user_level' AND sys_permission_arm.type = 'user_level' AND sys_permission_arm.section = 'sidebar' AND  sys_sidebar.action = 1 AND sys_permission_arm.action = 1 ORDER BY sys_sidebar.order_id ");
                $result1->bindParam(':id', $row['sn']);
                $result1->execute();
                for ($i = 0; $row1 = $result1->fetch(); $i++) {
                  $linkR = explode('.', $row1['link']);
                  $link = $linkR[0];
                  if ($row1['sub']) {

                    $con = '';
                    if ($sub1_menu_action == $row1['sn']) {
                      $con = 'active';
                    } ?>

                    <li class="treeview <?php echo $con; ?>">
                      <a href="#">
                        <i class="<?php echo $row1['icon']; ?>"></i>
                        <span><?php echo ucfirst($row1['name']); ?></span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                      </a>

                      <ul class="treeview-menu">
                        <?php
                        $result2 = $db->prepare("SELECT *,sys_sidebar.id AS sn FROM sys_sidebar JOIN sys_permission_arm ON sys_sidebar.id=sys_permission_arm.menu_id WHERE `sys_sidebar`.`$dep` = 1 AND  sys_sidebar.type='sub2' AND sys_sidebar.main_id = :id AND sys_permission_arm.user_level = '$user_level' AND sys_permission_arm.type = 'user_level' AND sys_permission_arm.section = 'sidebar' AND  sys_sidebar.action = 1 AND sys_permission_arm.action = 1 ORDER BY sys_sidebar.order_id ");
                        $result2->bindParam(':id', $row1['sn']);
                        $result2->execute();
                        for ($i = 0; $row2 = $result2->fetch(); $i++) {
                          $linkR = explode('.', $row2['link']);
                          $link = $linkR[0];
                        ?>
                          <li class="<?php if ($menu_action == $row2['sn']) {
                                        echo 'active';
                                      } ?>"><a href="<?php echo $row2['link']; ?>"><i class="<?php echo $row2['icon']; ?>"></i> <?php echo ucfirst($row2['name']); ?> </a></li>
                        <?php }  ?>
                      </ul>

                    </li>

                  <?php } else { ?>

                    <li class="<?php if ($menu_action == $row1['sn']) {
                                  echo 'active';
                                } ?>">
                      <a href="<?php echo $row1['link']; ?>"><i class="<?php echo $row1['icon']; ?>"></i> <?php echo ucfirst($row1['name']); ?> </a>
                    </li>

                <?php }
                } ?>
              </ul>
            </li>

          <?php } else { ?>

            <li class="<?php if ($menu_action == $row['sn']) {
                          echo 'active';
                        } ?>">
              <a href="<?php echo $row['link']; ?>">
                <i class="<?php echo $row['icon']; ?>"></i> <span><?php echo ucfirst($row['name']); ?></span>
              </a>
            </li>

        <?php }
        } ?>

      <?php } ?>

    </ul>
  </section>
</aside>
