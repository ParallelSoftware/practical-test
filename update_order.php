<?php
session_start();

if(!isset($_SESSION["user"]))
{
	header("Location: login.php");
}

require("settings.php");
$user = $_SESSION["user"];

$product_quantities = $_POST["products"];
$orderNum = $_POST['order_number'];

// $order = $product_quantities->order_number;

$connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);
$existingItems = array();
// $newItems = [];
// var_dump($product_quantities->order_number);
// $query = "SELECT product_ref FROM order_product WHERE order_ref = 88";
$query = "DELETE FROM order_product WHERE order_ref = '{$orderNum}'";
$result = $connection->query($query);

$query = "UPDATE user_order SET order_update = NOW() WHERE id = '{$orderNum}'";
$result = $connection->query($query);

// while ($row_product_ref = mysqli_fetch_array($result)) {
// 	array_push($existingItems, $row_product_ref);
// }

// $connection = new mysqli($db_hostname, $db_username, $db_password, $db_database);

// $statement = $connection->prepare("UPDATE order_product SET product_ref = ?, quantity = ? WHERE order_ref == {$product_quantities->order_number}");
// $statement->bind_param("ii", $product_id, $product_quantity);

// mysqli_error($connection);
// echo $connection->error;
// die(json_encode($connection->error));
// var_dump($items);
// foreach ($product_quantities as $product) {
// 	array_push($newItems, $products->product_id);
// }

// if ($statement = $connection->prepare("UPDATE order_product SET product_ref = ?, quantity = ? WHERE order_ref = ?")) {
// 	$statement->bind_param("iii", $product_id, $product_quantity, $order_num);

	if($statement = $connection->prepare("INSERT INTO order_product VALUES(?, ?, ?)"))
	{
		$statement->bind_param("iii", $orderNum, $product_id, $product_quantity);
		foreach ($product_quantities as $product)
		{
			$product = (object)$product;
			$product_id = $product->product_id;
			$product_quantity = $product->product_quantity;
			$statement->execute();
		}
	}

	// foreach ($product_quantities as $product) {
	// 	$product = (object)$product;
	// 	$element = 0;
	// 	if (in_array($product->product_id, $existingItems[$element])) {
			
	// 		$product_id = $product->product_id;
	// 		$product_quantity = $product->product_quantity;
	// 		$oder_num = $orderNum;
	
	// 		$statement->execute();
	// 	}
	// 	else
	// 	{
	// 		// $query = "DELETE FROM order_product WHERE product_ref = {$existingItems[$element]}";
	// 	}
	// 	$element++;

	// 	// $response->order_value += $product->product_quantity * $product->product_amount;
	// }
	// $element = 0;

// }

// $result = $connection->query($query);

// how to know what i am returning the the AJAX query? (can not return simple array)
die(json_encode("success"));