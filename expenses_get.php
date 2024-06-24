<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];

$ty = '';
if (isset($_GET['end'])) {
} else {
    if ($row['id'] == 4) {
        $ty = "";
    }
}
?>

<option value="0" selected></option>
<?php
$result = $db->prepare("SELECT * FROM expenses_sub_type WHERE type_id = :id ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
?>
    <option <?php if (!isset($_GET['end'])) { ?> <?php if ($row['id'] == 4) { ?> disabled <?php } ?> <?php } ?> value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?> </option>
<?php
}
?>