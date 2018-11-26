<?php

require("settings.php");
session_start();

if(!isset($_SESSION["user"]))
{
	header("Location: login.php");
}

$user = $_SESSION["user"];

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

$table = "<table id='order_table' border='1'>
	<tr>
		<th>Name</th>
		<th>Order</th>
		<th>Value</th>
		<th>Date</th>
		<th>Last Updated</th>
		<th>Status</th>
	</tr>";
while($row = mysqli_fetch_array($result))
{
	$table .= "<tr>";
	$table .= "<td>" . $row['firstname'] . "</td>";
	$table .= "<td>" . $row['id'] . "</td>";
	$table .= "<td>" . $row['Value'] . "</td>";
	$table .= "<td>" . $row['order_date'] . "</td>";
	$table .= "<td>" . $row['order_update'] . "</td>";
	$table .= "<td>" . $row['Status'] . "</td>";
	$table .= "</tr>";
}
$table .= "</table>"; 
$mysqli->close();


$file="Orders.xlsx";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
echo $table;
?>