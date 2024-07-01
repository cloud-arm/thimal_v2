<?php
include('../connect.php');

// Fetch data from the API
$api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/product.php';

$curl = curl_init($api_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$api_data = json_decode($response, true);

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
$api_data_normalized = array_map(function ($product) {
    return "{$product['id']}_{$product['name']}_{$product['price']}_{$product['qty']}";
}, $api_data);

$db_data_normalized = array_map(function ($product) {
    return "{$product['id']}_{$product['name']}_{$product['price']}_{$product['qty']}";
}, $db_data);

echo "<br><br>";

if (count(array_diff($api_data_normalized, $db_data_normalized)) === 0 && count(array_diff($db_data_normalized, $api_data_normalized)) === 0) {
    echo '<h3 style="color: green;">Testing successful..!</h3>';
} else {
    echo '<h3 style="color: red;">Testing failed..!</h3>';
}
