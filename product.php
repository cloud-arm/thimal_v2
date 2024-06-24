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
    $_SESSION['SESS_FORM'] = '71';

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          PRODUCT
          <small>Preview</small>
        </h1>
      </section>

      <section class="content">

        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">PRODUCT Data</h3>
            <small class="btn btn-success btn-sm mx-2" style="padding: 5px 10px;" title="Add Product" onclick="click_open(1)">Add Product</small>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">

              <thead>
                <tr>
                  <th>Product_id</th>
                  <th>Name</th>
                  <th>Transport</th>
                  <th>Price</th>
                  <th>Price2</th>
                  <th>Cost Price</th>
                  <th>Sell Price</th>
                  <th>#</th>
                </tr>

              </thead>

              <tbody>
                <?php
                $result = $db->prepare("SELECT * FROM products ORDER by product_id ASC  ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                ?>
                  <tr id="record_<?php echo $row['product_id']; ?>">
                    <td><?php echo $id = $row['product_id']; ?></td>
                    <td><?php echo $row['gen_name']; ?></td>
                    <td>
                      Mabima: <span class="badge bg-olive"><?php echo $row['transport1']; ?></span><br>
                      Hambanthota: <span class="badge bg-orange"><?php echo $row['transport2']; ?></span>
                    </td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['price2']; ?></td>
                    <td><?php echo $row['o_price']; ?></td>
                    <td><?php echo $row['sell_price']; ?></td>
                    <td style="display: flex; gap:5px; align-items:center; justify-content:center;">
                      <a href="#" onclick="product_dll('<?php echo $row['product_id']; ?>')" title="Click to Delete">
                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                      </a>
                      <a href="product.php?id=<?php echo $row['product_id']; ?>">
                        <button class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></button>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
              </tfoot>
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
    <?php
    $con = 'd-none';
    $name = '';
    $price = 0;
    $price2 = 0;
    $sell = 0;
    $o_price = 0;
    if (isset($_GET['id'])) {
      $con = '';
      $id = $_GET['id'];
      $result = $db->prepare("SELECT * FROM products WHERE product_id=:id ");
      $result->bindParam(':id', $id);
      $result->execute();
      for ($i = 0; $row = $result->fetch(); $i++) {
        $name = $row['gen_name'];
        $price = $row['price'];
        $price2 = $row['price2'];
        $sell = $row['sell_price'];
        $o_price = $row['o_price'];
        $commission = $row['commission'];
        $tr1 = $row['transport1'];
        $tr2 = $row['transport2'];
      }
    }
    ?>
    <div class="container-up <?php echo $con; ?>" id="container_up">
      <a href="product.php" class="container-close" onclick="click_close()"></a>
      <div class="row">
        <div class="col-md-12">

          <div class="box box-success popup d-none" id="popup_1" style="width: 450px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Product
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="product_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Product name</label>
                      <input type="text" name="name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Cost price</label>
                      <input type="number" step=".01" name="cost" value="0" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Sell price</label>
                      <input type="number" step=".01" name="sell" value="0" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" name="id" value="0">
                      <input type="submit" style="margin-top: 23px; width:100%" value="Save" class="btn btn-success">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup <?php echo $con; ?> with-scroll" id="popup_2" style="max-width: 450px;overflow-x: hidden;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Product Update
                <a href="product.php" class="text-black"><i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i></a>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="product_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Product name</label>
                      <input type="text" name="name" value="<?php echo $name ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Dealer price</label>
                      <input type="number" step=".01" name="price" value="<?php echo $price ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Dealer price 2</label>
                      <input type="number" step=".01" name="price2" value="<?php echo $price2 ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Sell price</label>
                      <input type="number" step=".01" name="sell" value="<?php echo $sell ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Cost price</label>
                      <input type="number" step=".01" name="cost" value="<?php echo $o_price ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Employee Commission</label>
                      <input type="number" step=".01" name="commission" value="<?php echo $commission ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Mabima Transport Charge</label>
                      <input type="number" step=".01" name="transport1" value="<?php echo $tr1 ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Hambanthota Transport Charge</label>
                      <input type="number" step=".01" name="transport2" value="<?php echo $tr2 ?>" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" name="id" value="<?php echo $id ?>">
                      <input type="submit" style="margin-top: 23px; width:100%" value="Update" class="btn btn-info">
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
    });
  </script>
  <script type="text/javascript">
    function product_dll(id) {

      var info = 'id=' + id;
      if (confirm("Sure you want to delete this product? There is NO undo!")) {

        $.ajax({
          type: "GET",
          url: "product_dll.php",
          data: info,
          success: function() {

          }
        });
        $("#record_" + id).animate({
            backgroundColor: "#fbc7c7"
          }, "fast")
          .animate({
            opacity: "hide"
          }, "slow");

      }

      return false;

    }
  </script>
</body>

</html>