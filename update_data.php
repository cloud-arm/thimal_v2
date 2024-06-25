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


$result = $db->prepare("SELECT * FROM payment WHERE type = 'credit' AND amount > pay_amount AND credit_balance = 0 ");
$result->bindParam(':id', $row);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $id = $row['transaction_id'];
    $pay_type = $row['type'];
    $balance = $row['amount'] - $row['pay_amount'];

    $sql = "UPDATE payment SET credit_balance = ?, action = ?, paycose = ?, pay_type = ?  WHERE transaction_id = ? ";
    $ql = $db->prepare($sql);
    $ql->execute(array($balance, 2, 'invoice_payment', $pay_type, $id));
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
