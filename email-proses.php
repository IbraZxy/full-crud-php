<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer autoloader
require 'vendor/autoload.php';

// Cek apakah tombol kirim ditekan
if (isset($_POST['kirim'])) {

    $mail = new PHPMailer(true);

    try {
        // Server settings PHPMailer
        $mail->SMTPDebug   = 0; // Ubah ke 0 agar alert JavaScript tidak terganggu log debug
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ramadhan.ibnu1933@gmail.com'; // Email Pengirim
        $mail->Password   = 'bmpjogkmtyawmgro';            // Passcode App Password (16 karakter) milik ramadhan.ibnu1933
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('ramadhan.ibnu1933@gmail.com', '@ramadhan.ibnu1933@gmail.com - Ibnu Ramadhan');
        $mail->addAddress($_POST['email_penerima']); 

        // Content
        $mail->isHTML(true);
        $mail->Subject = $_POST['subject'];
        $mail->Body    = $_POST['pesan'];

        $mail->send();

        echo "<script>
                alert('Email Berhasil Dikirim');
                document.location.href = 'email.php';
              </script>";

    } catch (Exception $e) {
        echo "<script>
                alert('Email Gagal Dikirim: {$mail->ErrorInfo}');
                document.location.href = 'email.php';
              </script>";
    }
} else {
    header("Location: email.php");
    exit;
}