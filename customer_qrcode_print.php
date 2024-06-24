<!DOCTYPE html>
<html>
<?php include("connect.php"); ?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CLOUD ARM | QRCODE</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <style>
        @media print {
            body img {
                width: 100px;
                border-radius: 10px;
            }


            body .div {
                padding: 2px;
                border-radius: 10px;
                border: 1px solid;
                width: 24%;
                display: flex;
            }

            body .div .span {
                margin-top: 10px;
                display: flex;
                width: 100%;
                flex-direction: column;
                justify-content: space-between;
                gap: 5px;
                margin-left: 5px;
                position: relative;
            }

            body .content {
                width: 100%;
                display: flex;
                gap: 20px;
                position: relative;
            }

            body h1 {
                font-size: 11px;
                margin: 0;
            }

            body h2 {
                font-size: 7px;
                margin: 0;
                width: 95%;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            body h3 {
                font-size: 7px;
                margin: 0 0 10px 0;
                width: 80%;
            }

            body h3>.icon {
                width: 11px;
            }
        }
    </style>

    <?php
    $id = $_GET['file'];
    $rq = $db->prepare("SELECT * FROM customer WHERE customer_id=:id ");
    $rq->bindParam(':id', $id);
    $rq->execute();
    for ($k = 0; $r = $rq->fetch(); $k++) {;
        $name = $r['customer_name'];
    }
    ?>
</head>

<body onload="window.print() ">
    <?php
    $sec = "1";
    ?>
    <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='customer.php'">
    <div class="wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="div">
                <span class="img">
                    <img src="qrcode/customer.png">
                </span>
                <span class="span">
                    <h1><b>Narangoda Group</b></h1>
                    <h2><?php echo substr($name, 0, 22); ?>...</h2>
                    <h3> <img src="icon/r.png" class="icon"> CLOUD ARM</h3>
                </span>
            </div>
            <div class="div">
                <span class="img">
                    <img src="qrcode/customer.png">
                </span>
                <span class="span">
                    <h1><b>Narangoda Group</b></h1>
                    <h2><?php echo substr($name, 0, 22); ?>...</h2>
                    <h3> <img src="icon/r.png" class="icon"> CLOUD ARM</h3>
                </span>
            </div>
        </section>
    </div>
</body>

</html>