<?php
require 'class/Exception.php';
require 'class/PHPMailer.php';
require 'class/SMTP.php';

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'invoice@rhntrading.com';
    $mail->Password = 'gx~0e~X]Clr}';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    //Recipient
    $mail->setFrom('invoice@rhntrading.com', 'Narangoda Group');
    $mail->addAddress('ga.ashenhansaka@gmail.com', 'Ashen Hansaka');

    //Attachments
    $invoiceFilePath = '../pdf/bin/invoice.pdf';
    $mail->addAttachment($invoiceFilePath, 'invoice.pdf');

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Invoice';
    $mail->Body = 'Please find attached the invoice.';

    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo "Email sending failed: {$mail->ErrorInfo}";
}
?>
