<?php 
  include_once('connection.php'); 

  /* we got this only after POST-Sign In, Else is return to Login page */
  if (isset($_SESSION['login_id'])) {
    header('location:admin.php?pg-url=home');
  }

  if (isset($_POST['signin'])) {
      $email = trim($_POST['email']);
      $password = $_POST['password'];

      // Fetch user by email only
      $sql_select = "SELECT * FROM admin_user WHERE email = ? LIMIT 1";

      $stmt = mysqli_prepare($conn, $sql_select);

      mysqli_stmt_bind_param($stmt, "s", $email);

      mysqli_stmt_execute($stmt);

      $data = mysqli_stmt_get_result($stmt);

      $row = mysqli_fetch_assoc($data);

      if ($row) {

          // Verify entered password with stored hash
          if (password_verify($password, $row['password'])) {

              session_regenerate_id(true);

              $_SESSION['login_id'] = $row['id'];

              header('Location: admin.php?pg-url=home');
              exit;

          } else {

              echo "Entered Password is incorrect... Please enter correct password... <br><br><br>";
          }

      } else {

          // Check whether any admin is registered
          $check = mysqli_query(
              $conn,
              "SELECT id FROM admin_user LIMIT 1"
          );

          if (mysqli_num_rows($check) == 0) {

              echo "There is no any data registered... Kindly register first... <br><br><br>";

          } else {

              echo "Entered Email is incorrect... Please enter correct email... <br><br><br>";
          }
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NachiyaarTraders | Log in</title>

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

      <form action="index.php" method="post">
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
            <button type="submit" class="btn btn-primary btn-block" name="signin">Sign In</button>
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