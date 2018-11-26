<?php
require("settings.php");

session_start();

$user = $_SESSION['user'];

if(!isset($_SESSION["user"]))
{
	header("Location: login.php");
}

$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_database);

	if ($user->admin)
	{	
		$query = "SELECT u.firstname, uo.id, SUM(p.value * op.quantity) AS Value, uo.order_date, uo.order_update, s.label AS Status
			FROM user_order uo
			LEFT JOIN status s ON uo.status_ref = s.id
			LEFT JOIN user u ON u.id = uo.user_ref
			LEFT JOIN order_product op ON op.order_ref = uo.id
			LEFT JOIN product p ON p.id = op.product_ref
			GROUP BY uo.id";
	}else
	{
		$query = "SELECT u.firstname, uo.id, SUM(p.value * op.quantity) AS Value, uo.order_date, uo.order_update, s.label AS Status
			FROM user_order uo
			LEFT JOIN status s ON uo.status_ref = s.id
			LEFT JOIN user u ON u.id = uo.user_ref
			LEFT JOIN order_product op ON op.order_ref = uo.id
			LEFT JOIN product p ON p.id = op.product_ref
			WHERE u.id = {$user->id}
			GROUP BY uo.id";		
	}

	$result = $mysqli->query($query);
	$rows = array();

	while($row = mysqli_fetch_array($result))
		{
			array_push($rows, $row);
		}

	$mysqli->close();
	die(json_encode($rows));