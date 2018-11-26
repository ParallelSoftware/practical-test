<?php
session_start();

if(!isset($_SESSION["user"]))
{
	header("Location: login.php");
}

require("settings.php");

$orderNum = $_POST['order_number'];

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
$query = "UPDATE user_order SET status_ref = 4 WHERE id = '{$orderNum}'";
$result = $connection->query($query);

// how to know what i am returning the the AJAX query? (can not return simple array)
die(json_encode("success"));