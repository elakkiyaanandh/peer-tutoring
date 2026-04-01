<?php
// logout.php
// Destroy session to log the user out securely
session_start();
session_unset();
session_destroy();

// Redirect back to login page
header("Location: login.html");
exit();
?>
