<?php

session_start();
include 'config/app.php';

// check apakah tombol Login ditekan
if (isset($_POST['login'])) {
    // ambil input username dan password
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // secret key
    $secret_key = "6LeDmF0tAAAAAFC5yaf74flEtfIu4hvYlzbkbB8A";

    $verifikasi = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);

    $response = json_decode($verifikasi);

    if ($response->success) {
        // check username
        $result = mysqli_query($db, "SELECT * FROM akun WHERE username = '$username'");

        // jika ada usernya
        if (mysqli_num_rows($result) == 1) {
            // check passwordnya
            $hasil = mysqli_fetch_assoc($result);

            if (password_verify($password, $hasil['password'])) {
                // set session
                $_SESSION['login']    = true;
                $_SESSION['id_akun']  = $hasil['id_akun'];
                $_SESSION['nama']     = $hasil['nama'];
                $_SESSION['username'] = $hasil['username'];
                $_SESSION['email']    = $hasil['email'];
                $_SESSION['level']    = $hasil['level'];

                // jika login benar arahkan ke file index.php
                header("Location: index.php");
                exit;
            } else {
                // jika password salah
                $error = true;
            }
        } else {
            // jika username tidak ditemukan
            $error = true;
        }
    } else {
        // jika recaptcha tidak valid
        $errorRecaptcha = true;
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Admin Login</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Font Poppins (sama seperti portofolio) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Favicons -->
    <link rel="icon" href="/docs/5.0/assets/img/favicons/favicon.ico">
    <meta name="theme-color" content="#0f172a">

    <style>
    * {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background-color: #0f172a;
        color: #f8fafc;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

    .form-signin {
        width: 100%;
        max-width: 400px;
        padding: 40px 35px;
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        text-align: center;
    }

    .form-signin img {
        margin-bottom: 20px;
        filter: invert(1) brightness(2);
    }

    .form-signin h1 {
        color: #ffffff;
        font-weight: 700;
        font-size: 1.6rem;
        margin-bottom: 25px;
    }

    .badge-login {
        display: inline-block;
        background: rgba(56, 189, 248, 0.1);
        color: #38bdf8;
        border: 1px solid rgba(56, 189, 248, 0.3);
        padding: 5px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 15px;
    }

    .form-floating {
        margin-bottom: 16px;
    }

    .form-floating .form-control {
        background-color: #0f172a;
        border: 1px solid #334155;
        color: #f8fafc;
        border-radius: 8px;
    }

    .form-floating .form-control:focus {
        background-color: #0f172a;
        border-color: #38bdf8;
        color: #f8fafc;
        box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.15);
    }

    .form-floating .form-control::placeholder {
        color: #64748b;
    }

    .form-floating>label {
        color: #64748b;
    }

    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label {
        color: #38bdf8;
    }

    .g-recaptcha {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .btn-login-submit {
        background-color: #38bdf8;
        color: #0f172a;
        border: none;
        font-weight: 600;
        padding: 12px;
        border-radius: 8px;
        transition: 0.3s;
        width: 100%;
    }

    .btn-login-submit:hover {
        background-color: #0284c7;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .alert-custom {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.4);
        color: #f87171;
        border-radius: 8px;
        font-size: 0.9rem;
        padding: 10px 15px;
        margin-bottom: 15px;
    }

    .footer-login {
        margin-top: 25px;
        color: #64748b;
        font-size: 0.8rem;
    }

    .back-link {
        position: fixed;
        top: 25px;
        left: 30px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.3s;
        z-index: 10;
    }

    .back-link:hover {
        color: #38bdf8;
    }

    @media (max-width: 480px) {
        body {
            display: block;
            padding: 20px 15px;
        }

        .back-link {
            position: static;
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .form-signin {
            max-width: 100%;
            margin: 0 auto;
        }
    }
    </style>
</head>

<body>

    <a href="../index.html" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Portofolio</a>

    <main class="form-signin">
        <img src="assets/img/logo_ibnu.png" alt="" width="70" height="57">

        <h1>Admin Login</h1>

        <?php if (isset($error)) : ?>
        <div class="alert-custom text-center">
            <b>Username/Password SALAH</b>
        </div>
        <?php endif; ?>

        <?php if (isset($errorRecaptcha)) : ?>
        <div class="alert-custom text-center">
            <b>Recaptcha Tidak Valid</b>
        </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-floating">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username..."
                    required>
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" name="password" class="form-control" id="floatingPassword"
                    placeholder="Password..." required>
                <label for="floatingPassword">Password</label>
            </div>

            <div class="g-recaptcha" data-sitekey="6LeDmF0tAAAAABzRIKw4iOcFnN9miPm4dT5UN2XM"></div>

            <button class="btn-login-submit" type="submit" name="login">Log In</button>
        </form>

        <p class="footer-login">&copy; 13juli-13nov 2026</p>
    </main>

    <script src="https://www.google.com/recaptcha/api.js"></script>

</body>

</html>