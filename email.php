<?php

session_start();
// membatasi halaman sebelum login
if (!isset($_SESSION["login"])) {
    echo "<script>
            alert('login dulu');
            document.location.href = 'login.php';
          </script>";
    exit;
}

$title = 'Kirim Email';

include 'layout/header.php';

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-envelope"></i> Kirim Email
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Kirim Email</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <form action="email-proses.php" method="post">
                <div class="mb-3">
                    <label for="email_penerima" class="form-label">Email Penerima</label>
                    <input type="email" class="form-control" id="email_penerima" name="email_penerima"
                        placeholder="Email penerima..." required>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject..."
                        required>
                </div>

                <div class="mb-3">
                    <label for="pesan" class="form-label">Pesan</label>
                    <textarea name="pesan" id="pesan" cols="30" rows="10" class="form-control" required></textarea>
                </div>

                <button type="submit" name="kirim" class="btn btn-primary" style="float: right;">Kirim</button>
            </form>
        </div>
    </section>
</div>

<?php include 'layout/footer.php'; ?>