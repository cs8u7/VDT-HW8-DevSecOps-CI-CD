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
      $fileTempName = $_FILES['file']['tmp_name'];
      $fileName = $_FILES['file']['name'];
      $fileSize = $_FILES['file']['size'];
      $fileType = $_FILES['file']['type'];

      $maxsize = 100 * 1024 * 1024;
      if ($fileSize > $maxsize) {
         header("Location: /edit.php?id=$id&error=invalid_file_size");
         exit;
      }

      $uploadPath = './upload/' . $fileName;

      if (move_uploaded_file($fileTempName, $uploadPath)) {
         $conn = createConnection();

         $update_query = "UPDATE jaegers SET image_path = ? WHERE id = ?";
         $prepare_update = $conn->prepare($update_query);
         $new_image_path = $uploadPath;
         $prepare_update->bind_param("si", $new_image_path, $id);
         $prepare_update->execute();

         header("Location: /edit.php?id=$id&upload=done");
         exit;
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

         $jaeger = "SELECT * FROM jaegers WHERE id = ?";
         $jaeger_query = $conn->prepare($jaeger);
         $jaeger_query->bind_param("s", $_GET['id']);
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