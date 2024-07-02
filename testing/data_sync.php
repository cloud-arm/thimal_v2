<?php
include('../connect.php');
include_once('data_send.php');
echo "<title>TESTING</title>";

// check_loading($db);
// sync_sp_price($db);
// sync_customer($db);
// sync_credit($db);
set_unloading($db);


function set_unloading($db)
{

    $api_url = 'http://localhost/Thimal/main/pages/get_v2/api/sync/set_unloading.php';

    $api_data[] = array(
        "id" => 1,
        "loading_id" => "8097",
        "driver_id" => "36",
        "r5000" => "5",
        "r1000" => "10",
        "r500" => "15",
        "r100" => "20",
        "r50" => "25",
        "r20" => "30",
        "r10" => "35",
        "coins" => "100",
        "cash_amount" => "46800.00"
        // Add more data arrays as needed
    );

    $response = send_data($api_url, $api_data);

    foreach ($response as $res) {
        echo "Status: {$res["status"]} - Message: {$res["message"]}";

        if ($res["status"] == "success") {

            $id = 8097;

            // Fetch data from the database
            $db_data = array();
            $result = $db->prepare("SELECT * FROM loading WHERE transaction_id = :id  ");
            $result->bindParam(':id', $id);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $db_data[] = array(
                    "id" => 1,
                    "loading_id" => $row['transaction_id'],
                    "driver_id" => $row['driver'],
                    "r5000" => $row['r5000'],
                    "r1000" => $row['r1000'],
                    "r500" => $row['r500'],
                    "r100" => $row['r100'],
                    "r50" => $row['r50'],
                    "r20" => $row['r20'],
                    "r10" => $row['r10'],
                    "coins" => $row['coins'],
                    "cash_amount" => $row['cash_total']
                );
            }

            // Compare API data and database data
            $api_data_normalized = normalize_data($api_data);
            $db_data_normalized = normalize_data($db_data);

            $differences = array_diff($api_data_normalized, $db_data_normalized);


            test_data($differences);
        }
    }
}


function sync_credit($db)
{
    echo "<h1>Credit Testing</h1>";

    // Fetch data from the API
    $api_url = 'http://localhost/Thimal/main/pages/get_v2/api/sync/sync_credit.php';
    $id = 1010;
    $post_data = array('id' => $id);

    $api_data = api_data($api_url, $post_data);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT * FROM payment JOIN customer ON payment.customer_id=customer.customer_id WHERE payment.type='credit' AND payment.pay_amount < payment.amount AND payment.transaction_id > :id AND payment.action > 0 AND payment.dll = 0 ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $db_data[] = array(
            "name" => $row['customer_name'],
            "cus_id" => $row['customer_id'],
            "amount" => $row['amount'],
            "balance" => $row['amount'] - $row['pay_amount'],
            "type" => $row['type'],
            "invoice_no" => $row['invoice_no'],
            "date" => $row['date'],
            "id" => $row['transaction_id'],
        );
    }

    // Display the fetched API data
    test_array($api_data, "sync_credit");

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);
}


function sync_customer($db)
{
    echo "<h1>Find Customer Testing</h1>";

    // Fetch data from the API
    $api_url = 'http://localhost/Thimal/main/pages/get_v2/api/sync/sync_customer.php';
    $id = 10;
    $type = " ";
    $post_data = array('id' => $id, 'type' => $type);

    $api_data = api_data($api_url, $post_data);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT customer_id AS id, customer_name AS name,  price_12 AS price12, price_5 AS price5, price_37 AS price37, price_2 AS price2, address, contact, area, vat_no, root_id, root FROM customer WHERE customer_id > :id ");
    $result->bindParam(':id', $id);
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $db_data[] = $row;
    }

    // Display the fetched API data
    test_array($api_data, "sync_customer");

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);

    // reset arrays
    unset($api_data);
    unset($db_data);

    // Fetch data from the API
    $api_url = 'http://localhost/Thimal/main/pages/get_v2/api/sync/sync_customer.php';
    $id = 10;
    $type = "find";
    $post_data = array('id' => $id, 'type' => $type);

    $api_data = api_data($api_url, $post_data);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT customer_id AS id, customer_name AS name,  price_12 AS price12, price_5 AS price5, price_37 AS price37, price_2 AS price2, address, contact, area, vat_no, root_id, root FROM customer WHERE customer_id = :id ");
    $result->bindParam(':id', $id);
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $db_data[] = $row;
    }

    // Display the fetched API data
    test_array($api_data, "sync_customer_find");

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);
}


function sync_sp_price($db)
{
    echo "<h1>Special Price List Testing</h1>";

    // Fetch data from the API
    $api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/sync/sync_sp_price.php';
    $id = 10;
    $post_data = array('id' => $id);

    $api_data = api_data($api_url, $post_data);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT customer AS customer_name, id, product_id, product_name, customer_id, n_price, price  FROM special_price  WHERE id > :id ");
    $result->bindParam(':id', $id);
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $db_data[] = $row;
    }

    // Display the fetched API data
    test_array($api_data, "special_price");

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);
}


function check_loading($db)
{
    echo "<h1>Loading Testing</h1>";

    // Fetch data from the API
    $api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/sync/check_loading.php';
    $id = 25;
    $post_data = array('id' => $id);

    $api_data = api_data($api_url, $post_data);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT transaction_id AS loading_id, sync, action  FROM loading WHERE driver = :id  AND action = 'load' ");
    $result->bindParam(':id', $id);
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $db_data[] = $row;
    }

    // Display the fetched API data
    test_array($api_data, "check_loading");

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);
}


// Normalize and compare API data and database data
function normalize_data($data)
{
    return array_map(function ($item) {
        return json_encode($item);
    }, $data);
}

// Function to fetch data from the API
function api_data($api_url, $post_data = '')
{
    if (empty($post_data)) {

        $curl = curl_init($api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
    } else {

        $curl = curl_init($api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $response = curl_exec($curl);
        curl_close($curl);
    }

    return json_decode($response, true);
}

// testing api result
function test_array($api_data, $name)
{
    // Display the fetched API data
    if (is_array($api_data)) {

        if (isset($api_data["message"])) {
            echo $api_data["message"] . "<br>";
            echo "Failed to fetch {$name} data from API.";
        } else {

            echo "Success to fetch {$name} data from API.";
        }
    } else {
        echo "Failed to fetch {$name} data from API.";
    }
}

// testing result
function test_data($differences)
{
    echo "<br><br>";

    if (empty($differences)) {
        echo '<h3 style="color: green;">Testing successful..!</h3>';
    } else {
        echo '<h3 style="color: red;">Testing failed..!</h3>';
        foreach ($differences as $difference) {
            echo "<pre>$difference</pre>";
        }
    }
}
