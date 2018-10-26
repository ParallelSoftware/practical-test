<?php
	session_start();
	require("settings.php");



$orderId = 35;
$i = 2;
	// checking if user exists in the db
	$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_database);
	if($mysqli->connect_error) {
	  exit('Error connecting to database'); //Should be a message a typical user could understand in production
	}
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