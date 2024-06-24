<!DOCTYPE html>
<html>

<head>
	<?php
	session_start();
	include("connect.php");
	date_default_timezone_set("Asia/Colombo");
	$invo = $_GET['id'];
	$co = substr($invo, 0, 2);
	?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>CLOUD ARM | Invoice</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	<style>
		@media print {
			h5 {
				line-height: 1.5;
			}

			#btn-box {
				display: none !important;
			}
		}
	</style>
</head>

<?php
$sec = "1";
$return = $_SESSION['SESS_BACK'];
?>

<?php if (isset($_GET['print'])) { ?>

	<body onload="window.print()" style=" font-size: 13px;font-family: arial;">
	<?php } else { ?>

		<body style=" font-size: 13px; font-family: arial;margin: 0 10px;overflow-x: hidden;">
		<?php } ?>

		<?php if (isset($_GET['print'])) { ?>
			<meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='<?php echo $return; ?>'">
		<?php } ?>
		<div class="wrapper">
			<!-- Main content -->
			<section class="invoice">

				<?php
				$pay_id = $_GET['id'];
				$url = 'id=' . $_GET['id'];
				$result = $db->prepare("SELECT * FROM info ");
				$result->bindParam(':userid', $date);
				$result->execute();
				for ($i = 0; $row = $result->fetch(); $i++) {
					$info_name = $row['name'];
					$info_add = $row['address'];
					$info_vat = $row['vat_no'];
					$info_con = $row['phone_no'];
				}

				$result = $db->prepare("SELECT * FROM payment WHERE  transaction_id=:id  ");
				$result->bindParam(':id', $pay_id);
				$result->execute();
				for ($i = 0; $row = $result->fetch(); $i++) {
					$invo = $row['invoice_no'];
					$date = $row['date'];
					$type = $row['type'];
					$amount = $row['amount'];
					$chq_no = $row['chq_no'];
					$chq_date = $row['chq_date'];
					$bank = $row['chq_bank'];
					$chq_action = $row['chq_action'];
					$reserve_date = $row['reserve_date'];
					$deposit_date = $row['deposit_date'];
				}
				?>


				<div class="row">
					<div class="col-xs-12" style="display: flex;justify-content: center;">
						<img src="icon/Logo-Laugfs-Gas.png" alt="Logo" style="width:300px;"><br>
					</div>

					<div class="col-xs-8">
						<h5>
							<?php echo $info_name; ?> <br>
							<?php echo $info_add; ?> <br>
							<?php echo "VAT Reg: " . $info_vat; ?><br>
							<b>Invoice: <?php echo $invo; ?> </b> <br>
							Invoice Date: <?php echo $date; ?><br>
							Print Date: <?php echo date("Y-m-d"); ?>
							Time- <?php echo date("H:i:s"); ?>
						</h5>
					</div>
					<!-- /.col -->
					<div class="col-xs-4 ">
						<small class="pull-right">
							<h3>
								Payment Receipt
							</h3>
							<h5>
								<b>Type: </b> <?php echo $type; ?> <br>
								<b>Amount: </b> <?php echo $amount; ?> <br>
								<?php if ($type == 'chq' | $type == 'Chq') { ?>
									<b>CHQ No: </b> <?php echo $chq_no; ?> <br>
									<b>Bank: </b> <?php echo $bank; ?> <br>
									<b>CHQ Date: </b> <?php echo $chq_date; ?> <br>
									<?php
									if ($chq_action == 1) {
										echo '<b>Deposit</b>: ' . $deposit_date . '<br>';
									} else if ($chq_action == 2) {
										echo '<b>Realize</b>: ' . $reserve_date . '<br>';
									} else if ($chq_action == 3) {
										echo '<b>Return</b>: ' . $reserve_date . '<br>';
									} else {
										echo '<b>In Hand</b>';
									}
									?>
								<?php } ?>
							</h5>
						</small>
					</div>
				</div>

				<div class="box-body" style="margin-top: 25px;">
					<div class="row">
						<?php if (isset($_GET['print'])) { ?>
							<div class="col-xs-12">
							<?php } else { ?>
								<div class="col-xs-8">
								<?php } ?>
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>ID</th>
												<th>Customer</th>
												<th>Invoice no</th>
												<th>Credit Amount (Rs.)</th>
												<th>Pay Amount (Rs.)</th>
												<th>Balance (Rs.)</th>

											</tr>
										</thead>
										<tbody>

											<?php
											$result = $db->prepare("SELECT * FROM credit_payment WHERE pay_id=:id AND action='0' ");
											$result->bindParam(':id', $pay_id);
											$result->execute();
											for ($i = 0; $row = $result->fetch(); $i++) {
											?>
												<tr>

													<td><?php echo $row['id'];   ?> </td>
													<td><?php echo $row['cus'];   ?> </td>
													<td><?php echo $row['invoice_no'];   ?> </td>
													<td><?php echo number_format($row['credit_amount'], 2);   ?></td>
													<td><?php echo number_format($row['pay_amount'], 2);   ?></td>
													<td><?php echo number_format($row['credit_amount'] - $row['pay_amount'], 2);   ?></td>

												</tr>
											<?php }   ?>
										</tbody>

									</table>
								</div>
								</div>

								<div class="col-xs-3" id="btn-box" style="display: flex;gap: 15px;justify-content: center;flex-direction: column;">
									<a href="bulk_payment_print.php?<?php echo $url; ?>&print" class="btn btn-danger"> <i class="fa fa-print"></i> Print</a>
									<span>
										<form action="pdf/bulk.php" method="GET" style="display: flex;gap: 15px;justify-content: center;" id="form">
											<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
											<input type="number" class="form-control" style="width: 75%;" name="number" id="num" onkeyup="typing()" value="" placeholder="947******">

											<a disabled href="#" id="btn" class="btn btn-success" onclick="btn_whatsapp()"> <i class="fa fa-whatsapp"></i> Whatsapp</a>
										</form>
									</span>
								</div>
							</div>
					</div>

					<br><br>
					<div class="row">
						<div class="col-xs-12">
							__________________ <br> DEALER SIGNATURE
						</div>
					</div>
			</section>
		</div>
		</body>

		<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
		<script>
			function typing() {
				if ($('#num').val() > 0) {
					$('#btn').removeAttr('disabled');
				} else {
					$('#btn').attr('disabled', '');
				}
			}

			function btn_whatsapp() {
				$('#form').submit();
			}
		</script>

</html>