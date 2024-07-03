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


// --------------------------------------------------------------------
// $a = 0;
// $result = $db->prepare("SELECT transaction_id,invoice_no FROM payment WHERE type = 'credit' AND paycose = 'invoice_payment' AND  action = 2 ");
// $result->bindParam(':id', $row);
// $result->execute();
// for ($i = 0; $row = $result->fetch(); $i++) {
//   $id = $row['transaction_id'];
//   $invoice = $row['invoice_no'];

//   $con = 0;
//   $result1 = $db->prepare("SELECT transaction_id FROM sales WHERE action = 5 AND invoice_number = :id ");
//   $result1->bindParam(':id', $invoice);
//   $result1->execute();
//   for ($i = 0; $row1 = $result1->fetch(); $i++) {
//     $con = $row1['transaction_id'];
//   }

//   if ($con) {
//     $sql = "UPDATE payment SET credit_balance = ?, action = ?, paycose = ?  WHERE transaction_id = ? ";
//     $ql = $db->prepare($sql);
//     $ql->execute(array(0, 8, 'invoice_delete', $id));
//     $a++;
//   }
// }

// echo $a . '<br>';

// $a = 0;
// $result = $db->prepare("SELECT transaction_id,invoice_no FROM payment WHERE type = 'credit' AND paycose = 'invoice_payment' AND  action = 2 ");
// $result->bindParam(':id', $row);
// $result->execute();
// for ($i = 0; $row = $result->fetch(); $i++) {
//   $id = $row['transaction_id'];
//   $invoice = $row['invoice_no'];

//   $con = 0;
//   $result1 = $db->prepare("SELECT transaction_id FROM sales WHERE action = 0 AND invoice_number = :id ");
//   $result1->bindParam(':id', $invoice);
//   $result1->execute();
//   for ($i = 0; $row1 = $result1->fetch(); $i++) {
//     $con = $row1['transaction_id'];
//   }

//   if ($con) {
//     $sql = "UPDATE payment SET credit_balance = ?, action = ?, paycose = ?  WHERE transaction_id = ? ";
//     $ql = $db->prepare($sql);
//     $ql->execute(array(0, 8, 'invoice_delete', $id));
//     $a++;
//   }
// }

// echo $a;
// ------------------------------------------------------------------------




// // Fetch data from the database
// $db_data = array();
// $result = $db->prepare("SELECT transaction_id,sales_id,action FROM payment WHERE type = 'credit' AND action = 2 AND paycose = 'invoice_payment' ");
// $result->bindParam(':id', $id);
// $result->execute();
// while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
//   $db_data[] = $row;
// }






// // Fetch data from the database
// $api_data = array();
// $result = $db1->prepare("SELECT transaction_id,sales_id,action FROM payment WHERE type = 'credit' AND action = 2  ");
// $result->bindParam(':id', $id);
// $result->execute();
// while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
//   $api_data[] = $row;
// }




// // Normalize data for comparison
// $api_data_normalized = normalize_data($api_data);
// $db_data_normalized = normalize_data($db_data);

// // Find differences
// $differences_db_api = array_diff($db_data_normalized, $api_data_normalized);
// $differences_api_db = array_diff($api_data_normalized, $db_data_normalized);

// $total_differences = array_merge($differences_db_api, $differences_api_db);

// // Test and display results
// test_data($total_differences, $db_data, $api_data);






// Normalize and compare API data and database data
function normalize_data($data)
{
  return array_map('json_encode', $data);
}





// testing result
function test_data($differences, $db_data, $api_data)
{
  echo "<br><br>";

  if (!count($differences)) {
    echo '<h3 style="color: green;">Testing successful..! Differences not found.</h3>';
  } else {
    echo '<h3 style="color: red;">Testing failed..! Found ' . count($differences) . ' differences.</h3>';
    foreach ($differences as $difference) {
      echo "<pre>$difference</pre>";
    }
  }
}




















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
