<?php
include('../connect.php');


$api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/product.php';

$curl = curl_init($api_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

// Display the fetched data
if (is_array($data)) {
    echo "<h1>Product List for API</h1><ul>";
    foreach ($data as $product) {
        echo "<li>[{$product['id']}]{$product['name']} - Rs.{$product['price']} (Quantity: {$product['qty']})</li>";
    }
    echo "</ul>";
} else {
    echo "Failed to fetch product data.";
}

echo "<br><br><br>";

echo "<h1>Product List for Datatable</h1><ul>";

$result = $db->prepare("SELECT * FROM products ");
$result->bindParam(':userid', $res);
$result->execute();
for ($i = 0; $product = $result->fetch(); $i++) {
    echo "<li>[{$product['id']}]{$product['name']} - Rs.{$product['price']} (Quantity: {$product['qty']})</li>";
}

echo "</ul>";


if ($data == $result) {
    echo "Testing successfull..!";
} else {
    echo "Testing faild..!";
}
