<?php
$api_url = 'https://thimal.cloudarmsoft.com/main/pages/v2/api/product.php';

$curl = curl_init($api_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

// Display the fetched data
if (is_array($data)) {
    echo "<h1>Product List</h1><ul>";
    foreach ($data as $product) {
        echo "<li>{$product['name']} - \${$product['price']} (Quantity: {$product['qty']})</li>";
    }
    echo "</ul>";
} else {
    echo "Failed to fetch product data.";
}
?>
