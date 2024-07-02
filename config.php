<?php

function get_pdf($date, $name, $path)
{

  $file_name = $path . $name . '.pdf';
  $html_code = '<link rel="stylesheet" href="bootstrap.min.css">';
  $html_code .= $date;
  $pdf = new Pdf();
  $pdf->load_html($html_code);
  $pdf->render();
  $file = $pdf->output();
  file_put_contents($file_name, $file);

  return $file_name;
}

function check_sales($db, $id)
{

  $sales = 0;
  $result = $db->prepare("SELECT amount FROM sales WHERE invoice_number = :id  ");
  $result->bindParam(':id', $id);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $sales = $row['amount'];
  }
  $list = 0;
  $result = $db->prepare("SELECT sum(amount) FROM sales_list WHERE invoice_no = :id  ");
  $result->bindParam(':id', $id);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $list = $row['sum(amount)'];
  }
  $payment = 0;
  $result = $db->prepare("SELECT amount FROM payment WHERE invoice_no = :id  ");
  $result->bindParam(':id', $id);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $payment = $row['amount'];
  }

  if ($sales == $list && $payment > 0) {
    return 1;
  } else {
    return 0;
  }
}

include("pdf/pdf.php");
include("class/whatsapp.php");
include("class/sms.php");
include("class/discord.php");
include("class/email.php");
include("class/table.php");
include("class/form.php");
include("class/invoice.php");
include("class/db_query/select.php");
include("class/db_query/insert.php");
include("class/db_query/update.php");
include("class/db_query/query.php");
