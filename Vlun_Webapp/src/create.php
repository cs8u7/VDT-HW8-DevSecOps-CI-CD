<?php
include ("./utilities.php");

if (isset($_COOKIE['auth'])) {
   $cookieValue = base64_decode($_COOKIE['auth']);
   list($username, $password) = explode(':', $cookieValue);

   $conn = createConnection();

   if ($conn->connect_error) {
      die('Connect Error: ' . $conn->connect_error);
   }

   $query = "SELECT id, username, user_password FROM users WHERE username = ? AND user_password = ?";
   $prepare_query = $conn->prepare($query);
   $prepare_query->bind_param("ss", $username, $password);
   $prepare_query->execute();
   $result = $prepare_query->get_result();
   $row = $result->fetch_assoc();

   $prepare_query->close();

   if ($row) {

      $jaeger = "INSERT INTO jaegers (user_id, jeager_name, image_path, model, jeager_status) VALUES (?, ?, ?, ?, ?)";
      $jaeger_query = $conn->prepare($jaeger);

      $userId = $row['id'];
      $randomNumber = rand();
      $jeagerName = "Jaeger $randomNumber";
      $imagePath = './upload/logo.png';
      $model = "Model Jaeger $randomNumber need to be updated";
      $jeagerStatus = "Status Jaeger $randomNumber need to be updated";

      $jaeger_query->bind_param('issss', $userId, $jeagerName, $imagePath, $model, $jeagerStatus);

      if ($jaeger_query->execute()) {
         if ($jaeger_query->affected_rows > 0) {
            $jaeger_query->close();
            $conn->close();
            header("Location: /profile.php");
            exit;
         } else {
            $jaeger_query->close();
            $conn->close();
            header("Location: /profile.php?error=insert");
            exit;
         }
      } else {
         die('Execute error: ' . $jaeger_query->error);
      }

   } else {
      $conn->close();
      header("Location: /logout.php");
      exit;
   }
} else {
   header("Location: /logout.php");
   exit;
}



