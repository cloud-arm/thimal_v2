<!DOCTYPE html>
<html>

<head>
	<?php
	session_start();
	include("connect.php");
	include("config.php");
	date_default_timezone_set("Asia/Colombo");
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
				margin-bottom: 0;
			}

			h4 span {
				float: right;
			}

			h3 {
				line-height: 1.5;
				font-weight: 600;
				text-decoration: underline;
			}

			#btn-box {
				display: none !important;
			}

			a {
				color: #3c8dbc !important;
				text-decoration: underline;
			}

			hr {
				border-color: #000 !important;
				text-decoration: underline;
				margin: 0 !important;
			}

			table thead tr th {
				text-align: center;
			}
		}
	</style>
</head>

<body>
	<?php
	$sec = "1";

	$return =  $_SESSION['SESS_BACK'];
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
					if (isset($_GET['invo'])) {

						$invo = base64_decode($_GET['invo']);
						$result = $db->prepare("SELECT * FROM sales WHERE invoice_number =:id ");
						$result->bindParam(':id', $invo);
						$result->execute();
						for ($i = 0; $row = $result->fetch(); $i++) {
							$id = $row['transaction_id'];
							$date = $row['date'];
							$cus_id = $row['customer_id'];
						}
						$url = 'invo=' . $_GET['invo'];
					} else if (isset($_GET['id'])) {

						$id = base64_decode($_GET['id']);
						$result = $db->prepare("SELECT * FROM sales WHERE transaction_id =:id ");
						$result->bindParam(':id', $id);
						$result->execute();
						for ($i = 0; $row = $result->fetch(); $i++) {
							$invo = $row['invoice_number'];
							$date = $row['date'];
							$cus_id = $row['customer_id'];
						}
						$url = 'id=' . $_GET['id'];
					}
					$vat_action = 0;
					$result = $db->prepare('SELECT * FROM customer WHERE  customer_id=:id ');
					$result->bindParam(':id', $cus_id);
					$result->execute();
					for ($i = 0; $row = $result->fetch(); $i++) {
						$vat_no = $row['vat_no'];
						$address = $row['address'];
					}
					if (strlen($vat_no) > 0) {
						$vat_action = 1;
					}

					$result = $db->prepare("SELECT * FROM info ");
					$result->bindParam(':userid', $date);
					$result->execute();
					for ($i = 0; $row = $result->fetch(); $i++) {
						$info_name = $row['name'];
						$info_add = $row['address'];
						$info_vat = $row['vat_no'];
						$info_con = $row['phone_no'];
						$info_mail = $row['email'];
					}

					?>


					<div class="row">
						<!-- accepted payments column -->
						<div class="col-xs-12 pull-right">
							<img src="icon/Logo-Laugfs-Gas.png" alt="Logo" style="width:150px;" class="pull-right"><br><br>
							<h5 style="text-align: right;">
								<b><?php echo $info_name; ?></b> <br>
								<?php if ($vat_action == 1) {
									echo "VAT Reg: " . $info_vat . " <br>";
								} ?>
								<?php echo $info_add; ?> <br>
								<?php echo $info_con; ?> <br>
								<a href="#" style="color:blue"><?php echo $info_mail; ?></a>
							</h5>
						</div>

						<div class="col-xs-12">
							<hr>
						</div>

						<div class="col-xs-12">
							<h3 style="text-align: center;">
								<?php if ($vat_action == 1) {
									echo "TAX INVOICE";
								} else {
									echo "INVOICE";
								} ?>
							</h3>
						</div>
						<!-- /.col -->
						<div class="col-xs-7">
							<h5>

								<?php
								$result = $db->prepare("SELECT * FROM sales WHERE   invoice_number='$invo'");
								$result->bindParam(':userid', $date);
								$result->execute();
								for ($i = 0; $row = $result->fetch(); $i++) {

									$cus_name = $row['name'];
									$loading_id = $row['loading_id'];
								} ?>

								<b>INVOICE TO:</b> <br>
								<?php echo $cus_name; ?> <br>
								<?php
								if ($vat_action == 1) {
									echo "<b>VAT No: </b>" . $vat_no . " <br>";
								}
								?>
								<?php echo $address; ?> <br>
								<b>Customer id: </b> <?php echo $cus_id; ?> <br>
								<b>loading id: </b> <?php echo $loading_id; ?>
							</h5>
						</div>
						<!-- /.col -->

						<div class="col-xs-5 pull-right">
							<h5 style="float:right">
								<b> Date:</b> <?php echo $date; ?> <br>
								<b>Invoice:</b> <?php echo $invo; ?> <br>
								<br>
								<small>
									Print Date: <?php echo date('Y-m-d'); ?> <br>
									Print Time- <?php echo date('H:i:s'); ?>
								</small>
							</h5>
						</div>

					</div>


					<div class="box-body">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Qty</th>
									<th>Price </th>
									<th>Amount </th>
								</tr>
							</thead>
							<tbody>
								<?php
								date_default_timezone_set("Asia/Colombo");
								$hh = date("Y/m/d");
								$tot_amount = 0;
								$num = 0;
								$result = $db->prepare("SELECT * FROM sales_list WHERE   invoice_no='$invo'");
								$result->bindParam(':userid', $date);
								$result->execute();
								for ($i = 0; $row = $result->fetch(); $i++) {
									$num += 1;

									$price = $row['price'] - ($row['dic'] / $row['qty']);
									$amount = $price * $row['qty'];
								?>
									<tr>
										<td><?php echo $num; ?></td>
										<td><?php echo $row['name']; ?></td>
										<td><?php echo $row['qty']; ?></td>
										<td>Rs.<?php if ($vat_action == 1) {
													echo number_format(((($price) / 118) * 100), 2);
												} else {
													echo $price;
												} ?></td>
										<td>Rs.<?php if ($vat_action == 1) {
													echo number_format(($amount / 118) * 100, 2);
												} else {
													echo number_format($amount, 2);
												} ?></td>
										<?php $tot_amount += $amount; ?>
									</tr>
								<?php } ?>
								<?php
								$result1 = $db->prepare("SELECT * FROM sales WHERE   invoice_number='$invo'  ");
								$result1->bindParam(':userid', $date);
								$result1->execute();
								for ($i = 0; $row1 = $result1->fetch(); $i++) {
									//	$tot_amount=$row1['amount'] - $row1['discount'];
									$balance = $row1['balance'];
								}
								?>
								<?php if ($vat_action == 1) { ?>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td>Sub Total: </td>
										<td>Rs.<?php echo number_format((($tot_amount / 118) * 100), 2); ?></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td>VAT: </td>
										<td>Rs.<?php echo number_format((($tot_amount / 118) * 18), 2); ?></td>
									</tr>
								<?php } ?>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td>Total: </td>
									<td>Rs.<?php echo number_format($tot_amount, 2); ?></td>
								</tr>
							</tbody>
							<tfoot>
							</tfoot>
						</table>

						<div class="row">
							<div class="col-xs-8">

								<div class="table-responsive">
									<table class="table">
										<tr>
											<th>Type</th>
											<th>Date</th>
											<th>CHQ No.</th>
											<th>CHQ Date</th>
											<th>CHQ Status</th>
											<th>Bank</th>
											<th>Amount</th>
										</tr>

										<?php
										$result1 = $db->prepare("SELECT * FROM payment WHERE invoice_no='$invo' AND paycose = 'invoice_payment' AND action > '0'  ");
										$result1->bindParam(':userid', $date);
										$result1->execute();
										for ($i = 0; $row1 = $result1->fetch(); $i++) {
										?>

											<tr>
												<td><?php echo $row1['type']; ?></td>
												<td><?php echo $row1['date']; ?></td>
												<td><?php echo $row1['chq_no']; ?></td>
												<td><?php echo $row1['chq_date']; ?></td>
												<td>
													<?php
													if ($row1['chq_action'] == 1) {
														echo '<span class="bg-primary badge" style="background:blue !important;">Deposit</span> <span class="bg-primary badge">' . $row1['deposit_date'] . '</span>';
													} else
													if ($row1['chq_action'] == 2) {
														echo '<span class="bg-primary badge" style="background:green !important;">Realize</span> <span class="bg-primary badge">' . $row1['reserve_date'] . '</span>';
													} else
													if ($row1['chq_action'] == 3) {
														echo '<span class="bg-primary badge" style="background:red !important;">Return</span> <span class="bg-primary badge">' . $row1['reserve_date'] . '</span>';
													}
													?>
												</td>
												<td><?php echo $row1['chq_bank']; ?></td>
												<td>Rs.<?php echo number_format($row1['amount'], 2); ?></td>

											</tr>
										<?php } ?>

										<?php
										$result1 = $db->prepare("SELECT * FROM payment WHERE invoice_no='$invo' AND type = 'credit_payment' AND action > '0'  ");
										$result1->bindParam(':userid', $date);
										$result1->execute();
										for ($i = 0; $row1 = $result1->fetch(); $i++) {
											$credit_pay_id = $row1['credit_pay_id'];
											$tr_id = $row1['credit_pay_id'];

											$result = $db->prepare("SELECT * FROM payment WHERE transaction_id='$tr_id'  ");
											$result->bindParam(':userid', $date);
											$result->execute();
											for ($i = 0; $row = $result->fetch(); $i++) {
												$type = $row['type'];
											}
										?>

											<tr>
												<td><?php echo $type; ?></td>
												<td><?php echo $row1['date']; ?></td>
												<td><?php echo $row1['chq_no']; ?></td>
												<td><?php echo $row1['chq_date']; ?></td>
												<td><?php echo $row1['chq_bank']; ?></td>
												<td>Rs.<?php echo number_format($row1['amount'], 2); ?></td>

											</tr>
										<?php } ?>

									</table>
								</div>
							</div>

							<div class="col-xs-4" id="btn-box" style="display: flex;gap: 15px;justify-content: center;">
								<a href="bill2.php?<?php echo $url; ?>&print" class="btn btn-danger"> <i class="fa fa-print"></i> Print</a>
								<a href="pdf/invoice.php?<?php echo $url; ?>" class="btn btn-success"> <i class="fa fa-whatsapp"></i> Whatsapp</a>
								<a href="index.php" class="btn btn-warning"> <i class="fa fa-home"></i> Home</a>
							</div>
						</div>

					</div>

					<br><br><br>
					<div class="row">
						<div class="col-xs-12">
							__________________ <br> DEALER SIGNATURE
						</div>
					</div>

					<br><br><br>
					<div class="row">
						<div class="col-xs-12" style="text-align: center;">
							This is a system generated document and signature is not required
						</div>
					</div>
				</section>
			</div>
			</body>

</html>