<?php
	session_start();
	require("settings.php");

	// check if session exists
	if(empty($_SESSION['user'])) die();

	// checking if user exists in the db
	$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if($mysqli->connect_error) {
		exit('Error connecting to database'); //Should be a message a typical user could understand in production
	}
	$query = "SELECT firstname, surname, admin, id FROM user WHERE id = ? LIMIT 1";
	$statement = $mysqli->prepare($query);
	$statement->bind_param("s", $_SESSION['user']->id);
	$status = $statement->execute();
	$statement->close();
	if(!$status) die();

	if($_POST['type'] == "viewProducts"){
		$query = "SELECT id, description, `value` FROM product ORDER BY `value` DESC";
		$result = $mysqli->query($query);
		$array = array();
		while($obj = $result->fetch_array(MYSQLI_ASSOC)){
			$array[] = $obj;
		}
		echo json_encode($array);
	}
	else if($_POST['type'] == "viewOrders"){
		$query = "SELECT `user_order`.`id`, `order_date`, `order_update`, `label`, SUM(`quantity` * `value`) as `total`
					FROM `user_order`, `order_product`, `product`, `status`
					WHERE `user_order`.`id` = `order_ref` AND `order_product`.`product_ref` = `product`.`id` AND `status_ref` = `status`.`id`
						AND `user_ref` = ? AND `status_ref` <> '4'
					GROUP BY `user_order`.`id`
					ORDER BY `order_update` DESC";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param('s', $_SESSION['user']->id);
		$stmt->execute();
		$result = $stmt->get_result();
		$array = array();
		while($obj = $result->fetch_array(MYSQLI_ASSOC)){
			$array[] = $obj;
		}
		echo json_encode($array);
	}
	else if($_POST['type'] == "viewOrder"){
		$orderId = $_POST['orderId'];

		$query = "SELECT `product`.`id`, `value`, `quantity`
					FROM `user_order`, `order_product`, `product`
					WHERE `user_order`.`id` = `order_ref` AND `order_product`.`product_ref` = `product`.`id`
						AND `user_ref` = ? AND `status_ref` <> '4' AND `order_ref` = ?
					ORDER BY `value` DESC";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param('ss', $_SESSION['user']->id, $orderId);
		$stmt->execute();
		$result = $stmt->get_result();
		$array = array();
		while($obj = $result->fetch_array(MYSQLI_ASSOC)){
			$array[] = $obj;
		}
		echo json_encode($array);
	}
	// place an order
	else if($_POST['type'] == "placeOrder"){
		$ids = $_POST['id'];
		$quantities = $_POST['quantity'];
		$flag = 0;
		foreach($quantities as $key=>$qty){
			if($qty == 0) {
				unset($quantities[$key]);
				unset($ids[$key]);
			}
		}
		
		if(count($quantities)){
			$status_ref = 1;
			$query = "INSERT INTO user_order(user_ref, order_update, status_ref) VALUES(?,NOW(),?)";
			$statement = $mysqli->prepare($query);
			$statement->bind_param("ss", $_SESSION['user']->id, $status_ref);
			
			$status = $statement->execute();
			
			if($status)
			{
				echo $order_ref = $statement->insert_id;
			
				foreach($ids as $key=>$productId){

					$quantity = $quantities[$key];
					$query = "INSERT INTO order_product(order_ref, product_ref, quantity) VALUES(?, ?, ?)";
					$statement = $mysqli->prepare($query);
					$statement->bind_param("sss", $order_ref, $productId, $quantity);
					$status = $statement->execute();
					// 
				}
			}
			else{
				echo '{"status":"Error","description","Something went wrong with the user order"}';
				die();
			}

			echo '{"status":"Success","description","Order Placed"}';
			die();
		}
		else{
			echo '{"status":"Error","description","No products"}';
			die();
		}
		
	}
	else if($_POST['type'] == 'cancelOrder')
	{
		if(empty($_POST['orderId'])) die;

		 echo $orderId = $_POST['orderId'];

		$query = "UPDATE `user_order` SET `status_ref` = '4' WHERE `id` = ? AND `user_ref` = ?";
		$statement = $mysqli->prepare($query);
		$statement->bind_param("ss", $orderId, $_SESSION['user']->id);
		$status = $statement->execute();

		if($status){
			echo '{"status":"Success","description","Order Cancelled"}';
			die();
		}
		echo '{"status":"Error","description","Something went wrong"}';
		die();
	}
	else if($_POST['type'] == "updateOrder"){
		$ids = $_POST['id'];
		$quantities = $_POST['quantity'];
		$orderId = $_POST['orderId'];
		$flag = 0;

		foreach($quantities as $key=>$qty){
			if($qty == 0) {
				unset($quantities[$key]);
				unset($ids[$key]);
			}
		}
		
		if(count($ids)){
			$status_ref = 1;

			$query = "DELETE FROM `order_product` WHERE `order_ref` = ?";
			$statement = $mysqli->prepare($query);
			$statement->bind_param("s", $orderId);
			$status = $statement->execute();

			foreach($ids as $key=>$productId){
				$quantity = $quantities[$key];

				$query = "INSERT INTO order_product(order_ref, product_ref, quantity) VALUES(?, ?, ?)";
				$statement = $mysqli->prepare($query);
				$statement->bind_param("sss", $orderId, $productId, $quantity);
				$status = $statement->execute();
			}
			

			echo '{"status":"Success","description","Order Placed"}';
			die();
		}
		else{
			echo '{"status":"Error","description","No products"}';
			die();
		}
	}
	// admin orders
	else if($_POST['type'] == "adminViewOrders" && $_SESSION['user']->admin){
		$query = "SELECT `surname`,`firstname`,`user_order`.`id`, `order_date`, `order_update`, `label`, SUM(`quantity` * `value`) as `total`
					FROM `user`, `user_order`, `order_product`, `product`, `status`
					WHERE `user`.`id` = `user_order`.`user_ref` AND `user_order`.`id` = `order_ref` AND `order_product`.`product_ref` = `product`.`id` AND `status_ref` = `status`.`id` AND `status_ref` <> '4'
					GROUP BY `user_order`.`id`
					ORDER BY `order_update` DESC";

		$result = $mysqli->query($query);
		$array = array();
		while($obj = $result->fetch_array(MYSQLI_ASSOC)){
			$array[] = $obj;
		}
		echo json_encode($array);
	}