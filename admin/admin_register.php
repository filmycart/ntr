<?php 
    include_once('connection.php'); 

    /* we got this only after POST-Sign In, Else is return to Login page */
    if (isset($_SESSION['login_id'])) {
      header('location:admin.php?pg-url=home');
    }

    /* Login process run if above session is not active*/
    if (isset($_POST['register'])) {

      $name = trim($_POST['name']);
      $email = trim($_POST['email']);
      $password = $_POST['password'];

      // Secure password hashing
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // Check existing email
      $stmt = mysqli_prepare(
          $conn,
          "SELECT id FROM admin_user WHERE email = ? LIMIT 1"
      );

      mysqli_stmt_bind_param($stmt, "s", $email);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);

      if (mysqli_stmt_num_rows($stmt) > 0) {
          echo "Email already registered.";
      } else {

          $stmtInsert = mysqli_prepare(
              $conn,
              "INSERT INTO admin_user (name, email, password)
               VALUES (?, ?, ?)"
          );

          mysqli_stmt_bind_param(
              $stmtInsert,
              "sss",
              $name,
              $email,
              $hashedPassword
          );

          if (mysqli_stmt_execute($stmtInsert)) {
              echo "Registration successful.";
          } else {
              echo "Registration failed.";
          }
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NachiyaarTraders | Register</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="index.php"><b>Nachiyaar</b>TRADERS</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="admin_register.php" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" name="email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <button type="submit" class="btn btn-primary btn-block" name="register">Register</button>
          </div>
          <div class="col-6">
            <div class="icheck-primary">
              <label for="remember">
                <a href="forgot.php" style="color: black; font-size: 14px;">Forgot Password ?</a>
              </label>
            </div>
          </div>
          <!-- /.col -->
          
          <!-- /.col -->
        </div>
      </form>

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<?php include_once 'scripts.php'; ?>