<?php
if (isset($_COOKIE['auth'])) {
    unset($_COOKIE['auth']); 
    setcookie('auth', '', time() - 3600, '/'); 
}

header("Location: login.php");
exit;
