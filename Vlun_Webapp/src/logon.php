<?php
include("utilities.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = htmlspecialchars($_POST['uname']);
  $email = htmlspecialchars($_POST['email']);
  $password = htmlspecialchars($_POST['psw']);

  if (getenv('APP_ENV') === 'dev') {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  }

  $conn = createConnection();

  $hash_psw = md5($password);

  $query = "SELECT id FROM users WHERE username = ? AND user_password = ?";
  $prepare_query = $conn->prepare($query);
  $prepare_query->bind_param("ss", $username, $hash_psw);
  $prepare_query->execute();
  $result = $prepare_query->get_result();
  
  if ($row = $result->fetch_assoc()) {
    $prepare_query->close();
    header("Location: login.php?status=account_exist");
    exit;
  } else {
    $sql = "INSERT INTO users (username, email, user_password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hash_psw);

    if ($stmt->execute()) {
      $stmt->close();
      $conn->close();
      header("Location: login.php?status=account_created");
      exit;
    } else {
      $stmt->close();
      $conn->close();
      header("Location: logon.php?error=broken-logon");
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Jeager Garage's Logon</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">
  <link rel="shortcut icon" href="logo.ico" type="image/x-icon" />
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
      <div class="w3-center w3-padding-16"><b>LOGON</b></div>
    </div>
  </div>

  <div style="display: flex;justify-content: center;align-items: center;height: 100vh;">
    <div>
      <form action="logon.php" method="post">
        <div class="imgcontainer">
          <img src="/upload/logo.png" alt="Avatar" class="avatar" style="height:400px; width:auto;">
        </div><br>

        <div>
          <label for="uname"><b>Username</b></label><br>
          <input type="text" placeholder="Enter Username" name="uname" required><br><br>

          <label for="uname"><b>Email</b></label><br>
          <input type="text" placeholder="Enter Username" name="email" required><br><br>

          <label for="psw"><b>Password</b></label><br>
          <input type="password" placeholder="Enter Password" name="psw" required><br><br>

          <a href="./login.php">Already has an Account</a><br><br>

          <button type="submit">Create Account</button><br><br>

          <?php if (isset($_GET['error']) && $_GET['error'] == 'broken-logon'): ?>
            <span style="color:red; margin-left: 10px;">Create account failed!</span>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

</body>

</html>