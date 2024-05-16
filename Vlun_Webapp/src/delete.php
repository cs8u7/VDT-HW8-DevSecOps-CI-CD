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

   if ($row && $_GET['id']) {

      $jaeger = "DELETE FROM jaegers WHERE id = ? and user_id = ?";
      $jaeger_query = $conn->prepare($jaeger);
      $jaeger_query->bind_param("ss", $_GET['id'], $row['id']);

      if ($jaeger_query->execute()) {
         if ($jaeger_query->affected_rows > 0) {
            $jaeger_query->close();
            $conn->close();
            header("Location: /profile.php");
            exit;
         } else {
            $jaeger_query->close();
            $conn->close();
            header("Location: /profile.php?error=delete");
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



