<?php
session_start();
include('connect.php');
include("config.php");
include('log/log.php');
date_default_timezone_set("Asia/Colombo");

$date = date("Y-m-d");
$time = date('H:i:s');

$driver = $_SESSION['SESS_MEMBER_ID'];
$driver_name = $_SESSION['SESS_FIRST_NAME'];


// $result = $db->prepare("SELECT * FROM payment WHERE type = 'credit' AND amount > pay_amount AND credit_balance = 0 ");
// $result->bindParam(':id', $row);
// $result->execute();
// for ($i = 0; $row = $result->fetch(); $i++) {
//     $id = $row['transaction_id'];
//     $pay_type = $row['type'];
//     $balance = $row['amount'] - $row['pay_amount'];

//     $sql = "UPDATE payment SET credit_balance = ?, action = ?, paycose = ?, pay_type = ?  WHERE transaction_id = ? ";
//     $ql = $db->prepare($sql);
//     $ql->execute(array($balance, 2, 'invoice_payment', $pay_type, $id));
// }



// $result = $db->prepare("SELECT * FROM payment WHERE type = 'credit' AND `credit_pay_id` > 0 ");
// $result->bindParam(':id', $row);
// $result->execute();
// for ($i = 0; $row = $result->fetch(); $i++) {
//     $id = $row['credit_pay_id'];

//     $sql = "UPDATE payment SET paycose = ?, pay_type = ?  WHERE transaction_id = ? ";
//     $ql = $db->prepare($sql);
//     $ql->execute(array('credit', 'credit_payment', $id));
// }




if (isset($_GET['sql'])) {

    // Path to the .sql file
    $sqlFile = trim($_GET['sql']) . '.sql';

    if (reading_file($sqlFile)) {
        exicute_file($db, $sqlFile);
    } else {
        echo "SQL file executed Failed.";
    }
}

function reading_file($sqlFile)
{
    // Read the file contents
    $sqlContent = file_get_contents($sqlFile);

    // Check if file reading was successful
    if ($sqlContent === false) {
        return false;
    } else {
        return true;
    }
}

function exicute_file($db, $sqlFile)
{
    // Path to the log file
    $logFile = 'log/query_log.txt';

    // Directory path
    $directory = 'log/bin/';

    // Create directory if it doesn't exist
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true); // Creates the directory recursively with full permissions
    }

    // File name
    $logFile = $directory . 'query_log.txt';


    // Split the content into individual queries
    $queries = explode(';', $sqlFile);

    // Execute each query
    try {
        $db->beginTransaction();

        foreach ($queries as $query) {
            // Trim any extra whitespace
            $query = trim($query);

            // Skip empty queries
            if (empty($query)) {
                continue;
            }

            // Log the query
            if (file_put_contents($logFile, $query . PHP_EOL, FILE_APPEND) === false) {
                die("Error writing to log file.");
            }

            // Execute the query
            $db->exec($query);
        }

        $db->commit();
        echo "SQL file executed successfully.";
    } catch (PDOException $e) {
        $db->rollBack();
        die("Error executing query: " . $e->getMessage());
    }
}


foreach ($payment[$cus] as $row) {

    $invoice = $row['invoice_no'];
    $limit = $row['credit_period'];

    if ($lorry == 'all') {
      $lorry_fill = " ";
    } else {
      $lorry_fill = " AND lorry_no='$lorry' ";
    }

    $result2 = $db->prepare("SELECT date,name,lorry_no,invoice_number FROM sales WHERE action='1' AND invoice_number='$invoice' " . $lorry_fill);
    $result2->bindParam(':userid', $d2);
    $result2->execute();
    for ($i = 0; $row2 = $result2->fetch(); $i++) {

      $pay_type = $row['type'];
      $action = $row['action'];

      $date = $row2['date'];
      $now =  date("Y-m-d");
      $start = strtotime($date);
      $end = strtotime($now);
      $time_dff = abs($end - $start);
      $intval = $time_dff / 86400;
      $rs1 = intval($intval);

      if ($type == 'due') {
        $level = $rs1 - $limit;
      } else {
        $level = $rs1;
      }
      $coo = $limit;
      $rs1 = $rs1 - $limit;

      if ($level >= 0) { ?>

        <tr>
          <td><?php echo $row['customer_id']; ?></td>
          <td><?php echo $row2['name']; ?></td>
          <td><?php echo $row['invoice_no']; ?><br>
            <span class="pull-right badge bg-green"><?php echo $row2['lorry_no']; ?> </span>
          </td>
          <td><?php echo $row2['date']; ?></td>
          <?php
          $tot += $row['amount'] - $row['pay_amount'];
          ?>
          <td><?php echo $row['credit_period'];  ?> Day</td>
          <td><?php echo number_format($row['amount'] - $row['pay_amount'], 2);
              $b_tot += $row['amount'] - $row['pay_amount'];
              if ($row['pay_amount'] > '0') { ?><span class="pull-right badge bg-black"><?php echo $row['pay_amount']; ?></span><?php } ?></td>
          <td><?php echo $rs1;  ?> Day</td>
          <td><?php echo $row['memo']; ?></td>
          <td>
            <a href="bill2.php?invo=<?php echo base64_encode($row2['invoice_number']); ?>" title="Click to View" class="btn btn-primary btn-sm fa fa-eye"></a>
          </td>
        </tr>
    <?php
      }
    }
  }

  if ($b_tot > 1) {
    ?>
    <tr style="background-color: rgb(var(--bg-light-70));">
      <th><?php echo $cus; ?></th>
      <th>Total</th>
      <td></td>
      <td></td>
      <td></td>
      <td><span class="pull-right badge bg-red"><?php echo number_format($b_tot, 1); ?></span></td>
      <td></td>
      <td></td>
      <td>
        <a href="sales_credit_rp_print.php?id=<?php echo base64_encode($cus); ?>" title="Click to View " class="btn btn-warning btn-sm fa fa-eye"></a>
      </td>
    </tr>
<?php
  }