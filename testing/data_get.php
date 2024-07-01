<?php
include('../connect.php');

product($db);
customer($db);

function customer($db)
{
    echo "<h1>Customer List Testing</h1>";

    // Fetch data from the API
    $api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/get_customer.php';

    $api_data = api_data($api_url);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT customer_id AS id, customer_name AS name, price_12 AS price12, price_5 AS price5, price_37 AS price37, price_2 AS price2, address, contact, area, vat_no, root_id, root  FROM customer");
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $db_data[] = $row;
    }

    // Display the fetched API data
    if (is_array($api_data)) {

        echo "Success to fetch customer data from API.";
    } else {
        echo "Failed to fetch customer data from API.";
    }

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);
}

function product($db)
{
    echo "<h1>Product List Testing</h1>";

    // Fetch data from the API
    $api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/product.php';

    $api_data = api_data($api_url);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT product_id AS id, gen_name AS name, price, qty FROM products");
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $db_data[] = $row;
    }

    // Display the fetched API data
    if (is_array($api_data)) {

        echo "Success to fetch product data from API.";
    } else {
        echo "Failed to fetch product data from API.";
    }

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
function api_data($api_url)
{
    $curl = curl_init($api_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
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
