<?php
include("connect.php");
include("config.php");
date_default_timezone_set("Asia/Colombo");

$date = date("Y-m-d");
$time = date("H:i:s");

$id = $_GET['id'];

$result = $db->prepare("SELECT * FROM sms_campaign WHERE campaign_id=:id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $message = $row['message'];
    $customer_id = $row['customer_id'];
    $type = $row['type'];
    $sms_id = $row['id'];
    $url = $row['img'];
}

$cus = 0;
$result = $db->prepare("SELECT customer_id FROM sms_campaign_record WHERE campaign_id=:id ORDER BY customer_id DESC LIMIT 1 ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $cus = $row['customer_id'];
}


if ($type == 'sms') {

    $result = $db->prepare("SELECT customer_id,contact FROM customer WHERE contact != '' AND id > $cus LIMIT 100 ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {

        $number = '94' . (int)$row['contact'];
        $res = sms($number, $message);

        $action = 0;
        if ($res == 'success') {
            $action = 1;
        }

        $sql = "INSERT INTO sms_campaign_record (campaign_id,campaign_type,message,customer_id,action,type,number,sms_id,date,time) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $request = $db->prepare($sql);
        $request->execute(array($id, $type, $message, $row['customer_id'], $action, $res, $number, $sms_id, $date, $time));
    }
}

if ($type == 'whatsapp') {
    $result = $db->prepare("SELECT customer_id,contact FROM customer WHERE contact != '' AND customer_id > $cus LIMIT 20 ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {

        // $number = '0762020312';
        $number = $row['contact'];
        $number = '94' . (int)$number;
        $res = whatsApp($number, $message, $url);

        $action = 0;
        if ($res == 'success') {
            $action = 1;
        }

        $sql = "INSERT INTO sms_campaign_record (campaign_id,campaign_type,message,customer_id,action,type,number,sms_id,date,time) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $request = $db->prepare($sql);
        $request->execute(array($id, $type, $message, $row['customer_id'], $action, $res, $number, $sms_id, $date, $time));
    }
}

if ($type == 'email') {
    $result = $db->prepare("SELECT customer_id,email FROM customer WHERE email != '' AND id > $cus LIMIT 100 ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {

        $email = $row['email'];
        $res = email($email, $message, $url);

        $action = 0;
        if ($res == 'success') {
            $action = 1;
        }

        $sql = "INSERT INTO sms_campaign_record (campaign_id,campaign_type,message,customer_id,action,type,email,sms_id,date,time) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $request = $db->prepare($sql);
        $request->execute(array($id, $type, $message, $row['customer_id'], $action, $res, $email, $sms_id, $date, $time));
    }
}

?>
<!-------------------------------------------------------------------------------------------->
<?php
$result = $db->prepare("SELECT COUNT(*) AS count FROM sms_campaign_record WHERE campaign_id = :id AND action = 1 ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sql = "UPDATE sms_campaign  SET send = ? WHERE id= ?";
    $request = $db->prepare($sql);
    $request->execute(array($row['count'], $sms_id));
}

$result = $db->prepare("SELECT COUNT(*) AS count FROM sms_campaign_record WHERE campaign_id = :id AND action = 0 ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sql = "UPDATE sms_campaign  SET failed = ? WHERE id= ?";
    $request = $db->prepare($sql);
    $request->execute(array($row['count'], $sms_id));
}

$result = $db->prepare("SELECT * FROM sms_campaign WHERE campaign_id=:id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['campaign_name'];
    $schedule = $row['schedule'];
    $send = $row['send'];
    $failed = $row['failed'];
    $type = $row['type'];
}
?>

<span class="info-box-text">
    <?php echo $name; ?>
    <?php if ($type == 'sms') { ?>
        <i class="fa fa-envelope pull-right"></i>
    <?php } ?>
    <?php if ($type == 'whatsapp') { ?>
        <i class="fa fa-whatsapp pull-right"></i>
    <?php } ?>
    <?php if ($type == 'email') { ?>
        <i class="fa fa-envelope-o pull-right"></i>
    <?php } ?>
</span>
<span style="width: 100%;margin: 5px 0;" class="btn btn-xs btn-success camp_btn" onclick="run_campaign(<?php echo $id; ?>)">
    Run
</span>
<span class="progress-description" style="font-size: 80%;">
    Schedule: <?php echo $schedule; ?>
</span>
<span class="progress-description" style="font-size: 80%;display: flex;justify-content: space-around;">
    <span>Send: <?php echo $send; ?></span>
    <span>Failed: <?php echo $failed; ?></span>
</span>