<?php
session_start();
session_destroy();
header("Location: ../frontend/loginPage.php");
exit();
?>
