<?php
	session_start();
	require("settings.php");

	// check if session exists
	if(empty($_SESSION['user'])) header("Location: index.php");

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
	if(!$status) die;

	$query = "SELECT `user_order`.`id` as `orderid`,`surname`,`firstname`, `order_date` as `order_placed`, `order_update`, `label` as `order_status`, SUM(`quantity` * `value`) as `order_total`
				FROM `user`, `user_order`, `order_product`, `product`, `status`
				WHERE `user`.`id` = `user_order`.`user_ref` AND `user_order`.`id` = `order_ref` AND `order_product`.`product_ref` = `product`.`id` AND `status_ref` = `status`.`id` AND `status_ref` <> '4'
				GROUP BY `user_order`.`id`
				ORDER BY `order_update` DESC";

	$result = $mysqli->query($query);
	// $f = fopen("tmp.csv", "w");
	// while($obj = $result->fetch_array(MYSQLI_ASSOC)){
	// 	fputcsv($f, $obj);
		
	// }

	$output = "";
	$i = 0;
	$header = "Order #,Surname,Firstname,Order Date,Order Update,Order Status,Order Total\n";
	while($obj = $result->fetch_array(MYSQLI_ASSOC)){
		if($i != 0) $output .= "\n";
		$output .= $obj['orderid'].",".$obj['surname'].",".$obj['surname'].",".$obj['order_placed'].",".$obj['order_update'].",".$obj['order_status'].",".$obj['order_total'];
		$i++;
	}

	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="orders.csv"');
	echo $header.$output;
	exit();
