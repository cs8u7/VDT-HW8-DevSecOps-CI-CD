<?php
function checkAuth()
{
   if (isset($_COOKIE['auth'])) {
      $cookieValue = base64_decode($_COOKIE['auth']);
      list($username, $password) = explode(':', $cookieValue);

      $conn = createConnection();

      if ($conn->connect_error) {
         die('Connect Error: ' . $conn->connect_error);
      }

      $query = "SELECT username, user_password FROM users WHERE username = ? AND user_password = ?";
      $prepare_query = $conn->prepare($query);
      $prepare_query->bind_param("ss", $username, $password);
      $prepare_query->execute();
      $result = $prepare_query->get_result();
      $row = $result->fetch_assoc();

      $prepare_query->close();
      $conn->close();

      if ($row) {
         return true;
      } else {
         return false;
      }
   } else {
      return false;
   }
}

function createConnection()
{
   $conn = new mysqli(getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'), getenv('MYSQL_DB'));
   return $conn;
}
