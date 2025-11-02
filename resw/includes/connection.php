<?php
$con = mysqli_connect("localhost", "root", "", "realestate");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>