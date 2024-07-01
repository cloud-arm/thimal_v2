<?php
include('../connect.php');
echo "<title>TESTING</title>";

check_loading($db);
sync_sp_price($db);



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
