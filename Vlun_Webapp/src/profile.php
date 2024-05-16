<?php
include ("./utilities.php");
if (!checkAuth()) {
  header("Location: /logout.php");
  exit;
}

if (isset($_GET['error']) && $_GET['error'] == 'delete') {
  $errorMessage = 'Post delete failed!';
  echo "<script>alert('$errorMessage');</script>";
}

if (isset($_GET['error']) && $_GET['error'] == 'insert') {
  $errorMessage = 'Post create failed!';
  echo "<script>alert('$errorMessage');</script>";
}

?>

<!DOCTYPE html>
<html>

<head>
  <title>Jeager Garage</title>
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
      <div class="header-container">
        <div class="w3-center w3-padding-16"><a href="../index.php"><b>JEAGER GARAGE</b></a></div>

        <div class="w3-right w3-padding-16" style="margin-left: 500px;"><button onclick="location.href='./logout.php'"
            style="text-decoration: none; color: inherit;">Logout</button></div>
        <div class="w3-right w3-padding-16" style="margin-left: 10px;"><button onclick="location.href='./create.php'"
            style="margin-left:0px;">Create Jeager</button>
        </div>
      </div>

      <div class="search-container w3-padding-16">
        <form action="search.php" method="get" style="display: flex; align-items: center;">
          <input type="text" name="query" placeholder="Search..." style="flex-grow: 1;">
          <button type="submit" style="margin-left: 10px;">Search</button>
        </form>
      </div>
    </div>
  </div>

  <br><br>

  <div class="w3-main w3-content w3-padding" style="max-width:1200px;margin-top:100px">

    <div class="w3-row-padding w3-padding-16 w3-center">
      <?php
      $conn = createConnection();

      $cookieValue = base64_decode($_COOKIE['auth']);
      list($username, $password) = explode(':', $cookieValue);

      $query = "SELECT id FROM users WHERE username = ? AND user_password = ?";
      $prepare_query = $conn->prepare($query);
      $prepare_query->bind_param("ss", $username, $password);
      $prepare_query->execute();
      $result = $prepare_query->get_result();
      $row = $result->fetch_assoc();

      $jaeger = "SELECT * FROM jaegers WHERE user_id = ?";
      $jaeger_query = $conn->prepare($jaeger);
      $jaeger_query->bind_param("s", $row['id']);
      $jaeger_query->execute();
      $jaeger_result = $jaeger_query->get_result();

      if ($jaeger_result->num_rows > 0) {
        while ($jaeger = $jaeger_result->fetch_assoc()) {
          ?>
          <div class="w3-quarter" style="max-width:400px;margin-left: 50px">
            <img src="<?= htmlspecialchars($jaeger['image_path']) ?>" alt="<?= htmlspecialchars($jaeger['jeager_name']) ?>"
              style="height:200px; width: 300px;">
            <h3>
              <?= htmlspecialchars($jaeger['jeager_name']) ?>
            </h3>
            <p>
              <?= htmlspecialchars($jaeger['model']) ?>,
              <?= htmlspecialchars($jaeger['jeager_status']) ?>
            </p>
            <button onclick="location.href='./delete.php?id=<?php echo $jaeger['id']; ?>'">Delete Post</button>
            <button onclick="location.href='./edit.php?id=<?php echo $jaeger['id']; ?>'">Edit Post</button>
          </div>
          <?php
        }
      } else {
        ?>
        <div class="w3-white w3-xlarge" style="max-width:1200px;margin:auto">
          <div class="w3-center w3-padding-16"><b>JAEGER STORE IS EMPTY!!!</b></div>
        </div>
        <?php
      }
      ?>

    </div>

  </div>
</body>

</html>