<?php 
$databaseConnection = mysqli_connect("localhost", "root", "root", "socialmedia");

$errors = [];
session_start();

if (mysqli_connect_error()) {
    exit("Database connection failed!");
}

unset($_SESSION['userId']);
unset($_SESSION['nickname']);
header("Location: login.php");
exit();
?>