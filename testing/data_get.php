<?php
include('../connect.php');

product($db);
customer($db);
damage($db);
employee($db);


function employee($db)
{
    echo "<h1>Employee List Testing</h1>";

    // Fetch data from the API
    $api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/get_employee.php';
    $post_data = array('id' => 123);

    $api_data = api_data($api_url, $post_data);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT loading.transaction_id AS id, loading.action AS action, employee.id AS emp_id, employee.name AS name
              FROM loading 
              JOIN employee ON loading.driver = employee.id 
              WHERE loading.transaction_id = :id AND loading.driver > 0");
    $result->execute();
    $result->bindParam(':id', 123, PDO::PARAM_INT);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $row["type"] = "Driver";
        $db_data[] = $row;
    }
    $result = $db->prepare("SELECT loading.transaction_id AS id, loading.action AS action, employee.id AS emp_id, employee.name AS name
              FROM loading 
              JOIN employee ON loading.helper1 = employee.id 
              WHERE loading.transaction_id = :id AND loading.helper1 > 0");
    $result->execute();
    $result->bindParam(':id', 123, PDO::PARAM_INT);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $row["type"] = "Helper1";
        $db_data[] = $row;
    }
    $result = $db->prepare("SELECT loading.transaction_id AS id, loading.action AS action, employee.id AS emp_id, employee.name AS name
              FROM loading 
              JOIN employee ON loading.helper2 = employee.id 
              WHERE loading.transaction_id = :id AND loading.helper2 > 0");
    $result->execute();
    $result->bindParam(':id', 123, PDO::PARAM_INT);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $row["type"] = "Helper2";
        $db_data[] = $row;
    }
    $result = $db->prepare("SELECT loading.transaction_id AS id, loading.action AS action, employee.id AS emp_id, employee.name AS name
              FROM loading 
              JOIN employee ON loading.helper3 = employee.id 
              WHERE loading.transaction_id = :id AND loading.helper3 > 0");
    $result->execute();
    $result->bindParam(':id', 123, PDO::PARAM_INT);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $row["type"] = "Helper3";
        $db_data[] = $row;
    }

    // Display the fetched API data
    test_array($api_data, "employee");

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);
}


function damage($db)
{
    echo "<h1>Damage List Testing</h1>";

    // Fetch data from the API
    $api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/get_damage.php';

    $api_data = api_data($api_url);

    // Fetch data from the database
    $db_data = array();
    $result = $db->prepare("SELECT cylinder_type AS product_name, id, complain_no, customer_name, customer_id, product_id, reason, location, action, cylinder_no  FROM damage ");
    $result->execute();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $db_data[] = $row;
    }

    // Display the fetched API data
    test_array($api_data, "damage");

    // Compare API data and database data
    $api_data_normalized = normalize_data($api_data);
    $db_data_normalized = normalize_data($db_data);

    $differences = array_diff($api_data_normalized, $db_data_normalized);


    test_data($differences);
}


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
    test_array($api_data, "customer");

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
    test_array($api_data, "product");

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
