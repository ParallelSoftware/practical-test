<?php

session_start();

if(!isset($_SESSION["user"]))
{
	header("Location: login.php");
}

require("settings.php");
$user = $_SESSION["user"];
$userID = $user->id;

$order_id = $_POST["orderNum"];

// $order_id = 88;

$rows = array();

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
$query = "SELECT product_ref, quantity
			FROM order_product
			WHERE order_ref = {$order_id}
			GROUP BY product_ref";
$result = $connection->query($query);
while ($row = mysqli_fetch_array($result)) {
	array_push($rows, $row);
}
die(json_encode($rows));
?>


<!-- "SELECT op.product_ref, SUM(p.value * op.quantity) AS amount
			FROM order_product op
			LEFT JOIN  product p ON op.product_ref = p.id
			WHERE order_ref = {$order_id}
			GROUP BY op.product_ref"; -->