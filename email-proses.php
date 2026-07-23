use PHPMailer\PHPMailer\PHPMailer;

// Load Composer autoloader
require 'vendor/autoload.php';

$mail = new PHPMailer(true);

// Server settings PHPMailer
$mail->SMTPDebug = 2; // Enable verbose debug output
$mail->isSMTP(); // Send using SMTP
$mail->Host = 'smtp.gmail.com'; // Set the SMTP server
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'tutormubatekno@gmail.com'; // SMTP username (Email Pengirim)
$mail->Password = 'hekuvjimgpsabydd'; // SMTP password (App Password)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
$mail->Port = 465;

// Cek apakah tombol kirim ditekan
if (isset($_POST['kirim'])) {
try {
// Recipients
$mail->setFrom('tutormubatekno@gmail.com', 'Tutorial Muba Teknologi');
$mail->addAddress($_POST['email_penerima']); // Penerima email dari input form

// Content
$mail->isHTML(true);
$mail->Subject = $_POST['subject'];
$mail->Body = $_POST['pesan'];

if ($mail->send()) {
echo "<script>
alert('Email Berhasil Dikirim');
document.location.href = 'email.php';
</script>";
} else {
echo "<script>
alert('Email Gagal Dikirim');
document.location.href = 'email.php';
</script>";
}
} catch (Exception $e) {
echo "<script>
alert('Email Gagal Dikirim: {$mail->ErrorInfo}');
document.location.href = 'email.php';
</script>";
}
}

/* Kode lama yang di-comment di modul:
if (isset($_POST['kirim'])) {
if (create_barang($_POST) > 0) {
...
}
}
*/