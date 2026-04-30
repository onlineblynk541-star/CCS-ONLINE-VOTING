<?php
// logout.php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect back to login page
header("location: login.php");
exit;
?>