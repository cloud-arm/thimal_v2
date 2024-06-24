<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$name = $_POST['name'];
$message = $_POST['message'];
$customer = $_POST['customer'];
$type = $_POST['type'];
// $img = $_POST['image'];

$date = date("Y-m-d");
$time = date("H:i:s");

$campaign_id = 1;
$result = $db->prepare("SELECT MAX(campaign_id) FROM `sms_campaign`");
$result->bindParam(':userid', $ttr);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $campaign_id = $row['MAX(campaign_id)'] + 1;
}

if ($type == 'sms') {
    $imageUploadPath = '';
} else {

    $imageUploadPath = '';
    // if (isset($_POST["image"])) {
    function compressImage($source, $destination, $quality)
    {
        // Get image info 
        $imgInfo = getimagesize($source);
        $mime = $imgInfo['mime'];

        // Create a new image from file 
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                imagejpeg($image, $destination, $quality);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                imagepng($image, $destination, $quality);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                imagegif($image, $destination, $quality);
                break;
            default:
                $image = imagecreatefromjpeg($source);
                imagejpeg($image, $destination, $quality);
        }

        // Return compressed image 
        return $destination;
    }

    // File upload path 
    $uploadPath = "campaign/img/";

    // Check if the directory exists, if not, create it
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true); // Create the directory with full permissions (0777)
    }


    // If file upload form is submitted 
    $status = $statusMsg = '';
    if (!empty($_FILES["image"]["name"])) {
        $status = 'error';

        // File info 
        $fileName = $campaign_id . '.' . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $imageUploadPath = $uploadPath . $fileName;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);

        // Allow certain file formats 
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Image temp source 
            $imageTemp = $_FILES["image"]["tmp_name"];

            // Compress size and upload image 
            $compressedImage = compressImage($imageTemp, $imageUploadPath, 60);

            if ($compressedImage) {
                $status = 'success';
                $statusMsg = "Image compressed successfully.";
            } else {
                $statusMsg = "Image compress failed!";
            }
        } else {
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        }
    } else {
        $statusMsg = 'Please select an image file to upload.';
    }
    echo $statusMsg;
    // }
}

$count = 0;
$contact = 'contact';
if ($type == 'sms') {
    $contact = 'contact';
}
if ($type == 'whatsapp') {
    // $contact = 'whatsapp';
}
if ($type == 'email') {
    $contact = 'email';
}

$result = $db->prepare("SELECT COUNT(*) AS count FROM customer WHERE $contact != ''  ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $count = $row['count'];
}

$sql = "INSERT INTO sms_campaign (campaign_id,campaign_name,message,customer_id,action,type,schedule,img,date,time) VALUES (?,?,?,?,?,?,?,?,?,?)";
$request = $db->prepare($sql);
$request->execute(array($campaign_id, $name, $message, $customer, 'pending', $type, $count, $imageUploadPath, $date, $time));


header("location: campaign.php");
