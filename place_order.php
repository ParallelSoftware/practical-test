<?php

session_start();

if(!isset($_SESSION["user"]))
{
	header("Location: login.php");
}

require("settings.php");
$user = $_SESSION["user"];

$product_quantities = $_POST["products"];

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
$query = sprintf("INSERT INTO user_order(user_ref, status_ref) VALUES(%s, 1)",
		$user->id
		);
$result = $connection->query($query);

if($result)
{
	//"insert_id" = get last inserted row's primary key
	$order_id = $connection->insert_id;
}
// $date = date("d-m-Y");
$date = date('Y-m-d H:i:s');

$response = (object)array("order_id" => $order_id, "order_value" => 0, "date" => $date, "last_updated" => "0000-00-00 00:00:00", "status" => "Placed");

if($statement = $connection->prepare("INSERT INTO order_product VALUES(?, ?, ?)"))
{
	$statement->bind_param("iii", $order_id, $product_id, $product_quantity);
	foreach ($product_quantities as $product)
	{
		$product = (object)$product;
		$product_id = $product->product_id;
		$product_quantity = $product->product_quantity;
		$statement->execute();
		$response->order_value += $product->product_quantity * $product->product_amount;
	}
}
die(json_encode($response));

// Order	Value	Date	Last Updated	Status	Action