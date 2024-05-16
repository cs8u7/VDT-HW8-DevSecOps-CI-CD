<?php
include ("./utilities.php");
if (!checkAuth()) {
   header("Location: /logout.php");
   exit;
}

if (!isset($_GET["id"])) {
   header("Location: /logout.php");
   exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
   $id = intval($_POST['id']);
   if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
      $id = $_POST['id'];
      $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
      $fileTempName = $_FILES['file']['tmp_name'];
      $fileName = $_FILES['file']['name'];
      $fileSize = $_FILES['file']['size'];
      $fileType = $_FILES['file']['type'];

      if (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|gif)$/', $fileName)) {
         header("Location: /edit.php?id=$id&error=invalid_file");
         exit;
      }

      $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

      if (!array_key_exists($ext, $allowed)) {
         header("Location: /edit.php?id=$id&error=invalid_file");
         exit;
      }

      $maxsize = 100 * 1024 * 1024;
      if ($fileSize > $maxsize) {
         header("Location: /edit.php?id=$id&error=invalid_file_size");
         exit;
      }

      if (in_array($fileType, $allowed)) {
         $newFilename = uniqid() . '.' . $ext;
         $uploadPath = './upload/' . $newFilename;

         if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadPath)) {
            header("Location: /edit.php?id=$id&upload=done");
            exit;
         } else {
            header("Location: /edit.php?id=$id&error=upload_error");
            exit;
         }
      } else {
         header("Location: /edit.php?id=$id&error=upload_error");
         exit;
      }
   } else {
      header("Location: /edit.php?id=$id&error=file_error");
      exit;
   }
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
            <div class="w3-center w3-padding-16"><a href="./index.php"><b>JEAGER GARAGE</b></a></div>

            <div class="w3-right w3-padding-16" style="margin-left: 10px;"><button
                  onclick="location.href='./logout.php'" style="text-decoration: none; color: inherit;">Logout</button>
            </div>
            <div class="w3-right w3-padding-16" style="margin-left: 500px;"><button
                  onclick="location.href='./profile.php'"
                  style="text-decoration: none; color: inherit;">Profile</button></div>
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

         $jaeger = "SELECT * FROM jaegers WHERE id = ? AND user_id = ?";
         $jaeger_query = $conn->prepare($jaeger);
         $jaeger_query->bind_param("ss", $_GET['id'], $row['id']);
         $jaeger_query->execute();
         $jaeger_result = $jaeger_query->get_result();
         $jaeger = $jaeger_result->fetch_assoc();
         if ($jaeger) {
            ?>

            <div class="imgcontainer">
               <img src="<?= htmlspecialchars($jaeger['image_path']) ?>"
                  alt="<?= htmlspecialchars($jaeger['jeager_name']) ?>" class="avatar"
                  style="height:400px; width:auto;"><br><br>

               <form action="/edit.php?id=<?= htmlspecialchars($jaeger['id']) ?>" method="post"
                  enctype="multipart/form-data">
                  Choose file to upoad:
                  <input type="file" name="file" id="file">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($jaeger['id']) ?>">
                  <input type="submit" value="Post" name="submit">
               </form>
               <?php if (isset($_GET['error']) && $_GET['error'] == 'file_error'): ?>
                  <span style="color:red; margin-left: 10px;">Please select a image to upload!</span>
               <?php endif; ?>

               <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_file'): ?>
                  <span style="color:red; margin-left: 10px;">Please select a image to upload!</span>
               <?php endif; ?>

               <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_file_size'): ?>
                  <span style="color:red; margin-left: 10px;">Please select a smaller image to upload!</span>
               <?php endif; ?>

               <?php if (isset($_GET['error']) && $_GET['error'] == 'upload_error'): ?>
                  <span style="color:red; margin-left: 10px;">Please upload again, error!</span>
               <?php endif; ?>

               <?php if (isset($_GET['upload']) && $_GET['upload'] == 'done'): ?>
                  <span style="color:green; margin-left: 10px;">Upload Success!</span>
               <?php endif; ?>

            </div><br>

            <div>
               <label for="jaeger_name"><b>Jaeger Name</b></label><br>
               <input type="text" value="<?= htmlspecialchars($jaeger['jeager_name']) ?>" name="jaeger_name"
                  required><br><br>

               <label for="jaeger_model"><b>Model</b></label><br>
               <input type="text" value="<?= htmlspecialchars($jaeger['model']) ?>" name="jaeger_model" required><br><br>

               <label for="jaeger_status"><b>Jaeger Status</b></label><br>
               <input type="text" value="<?= htmlspecialchars($jaeger['jeager_status']) ?>" name="jaeger_status"
                  required><br><br>

               <button type="submit">Post Jaeger</button><br><br>

               <?php
         } else {
            ?>
               <div class="w3-white w3-xlarge" style="max-width:1200px;margin:auto">
                  <div class="w3-center w3-padding-16"><b>JAEGER POST INVALID!!!</b></div>
               </div>
               <?php
         }
         ?>

         </div>

      </div>

   </div>
</body>

</html>