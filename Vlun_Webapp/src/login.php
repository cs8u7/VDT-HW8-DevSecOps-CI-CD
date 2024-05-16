<?php
include("utilities.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = htmlspecialchars($_POST['uname']);
  $password = htmlspecialchars($_POST['psw']);

  if (getenv('APP_ENV') === 'dev') {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  }

  $conn = createConnection();

  $hash_psw = md5($password);

  $query = "SELECT username, user_password FROM users WHERE username = ? AND user_password = ?";
  $prepare_query = $conn->prepare($query);
  $prepare_query->bind_param("ss", $username, $hash_psw);
  $prepare_query->execute();
  $result = $prepare_query->get_result();
  $row = $result->fetch_assoc();
    
  if ($row) {
    $cookieName = "auth";
    $cookieValue = base64_encode($username .":". $hash_psw);
    $cookieExpire = time() + (86400);
    setcookie($cookieName, $cookieValue, $cookieExpire, "/");
    $prepare_query->close();
    $conn->close();
    header("Location: /index.php");
    exit;
  } else {
    $prepare_query->close();
    $conn->close();
    header("Location: login.php?error=invalidcredentials");
    exit;
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Jeager Garage's Login</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">
  <link rel="shortcut icon" href="logo.ico" type="image/x-icon"/>
  <style>
    body,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-family: "Karma", sans-serif
    }

    .w3-bar-block .w3-bar-item {
      padding: 20px
    }
  </style>
</head>

<body>

  <div class="w3-top">
    <div class="w3-white w3-xlarge" style="max-width:1200px;margin:auto">
      <div class="w3-center w3-padding-16"><b>LOGIN</b></div>
    </div>
  </div>

  <div style="display: flex;justify-content: center;align-items: center;height: 100vh;">
    <div>
      <form action="login.php" method="post">
        <div class="imgcontainer">
          <img src="/upload/logo.png" alt="Avatar" class="avatar" style="height:400px; width:auto;">
        </div><br>

        <div>
          <label for="uname"><b>Username</b></label><br>
          <input type="text" placeholder="Enter Username" name="uname" required><br><br>

          <label for="psw"><b>Password</b></label><br>
          <input type="password" placeholder="Enter Password" name="psw" required><br><br>
          <a href="./logon.php">Create Account</a><br><br>
          <button type="submit">Login</button><br><br>

          <?php if (isset($_GET['status']) && $_GET['status'] == 'account_created'): ?>
            <span style="color:green; margin-left: 10px;">Account is created successfully!</span>
          <?php endif; ?>

          <?php if (isset($_GET['status']) && $_GET['status'] == 'account_exist'): ?>
            <span style="color:green; margin-left: 10px;">Account exist!</span>
          <?php endif; ?>

          <?php if (isset($_GET['error']) && $_GET['error'] == 'invalidcredentials'): ?>
            <span style="color:red; margin-left: 10px;">Invalid username or password!</span>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

</body>

</html>