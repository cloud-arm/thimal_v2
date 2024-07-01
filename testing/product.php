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
echo "<h1>Product List for API</h1><ul>";
if (is_array($api_data)) {
    foreach ($api_data as $product) {
        echo "<li>[{$product['id']}] {$product['name']} - Rs. {$product['price']} (Quantity: {$product['qty']})</li>";
    }
    echo "</ul>";
} else {
    echo "Failed to fetch product data from API.";
}

echo "<br><br><br>";

// Display the fetched database data
echo "<h1>Product List for Datatable</h1><ul>";
foreach ($db_data as $product) {
    echo "<li>[{$product['product_id']}] {$product['name']} - Rs. {$product['price']} (Quantity: {$product['qty']})</li>";
}
echo "</ul>";

// Compare API data and database data
$api_data_normalized = array_map(function ($product) {
    return "{$product['id']}_{$product['name']}_{$product['price']}_{$product['qty']}";
}, $api_data);

$db_data_normalized = array_map(function ($product) {
    return "{$product['id']}_{$product['name']}_{$product['price']}_{$product['qty']}";
}, $db_data);

if (count(array_diff($api_data_normalized, $db_data_normalized)) === 0 && count(array_diff($db_data_normalized, $api_data_normalized)) === 0) {
    echo "Testing successful..!";
} else {
    echo "Testing failed..!";
}
