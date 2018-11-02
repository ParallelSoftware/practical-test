<?php
	require("src/Orders.php");

	$orders = new Orders();
	if(!$orders->_LoggedIn()) die;

	if($_POST['type'] == "viewProducts"){
		echo json_encode($orders->viewProducts());
	}
	else if($_POST['type'] == "viewOrders"){
		
		echo json_encode($orders->viewOrders());
	}
	else if($_POST['type'] == "viewOrder"){
		echo json_encode($orders->viewOrder($_POST['orderId']));
	}
	// place an order
	else if($_POST['type'] == "placeOrder"){
		$ids = $_POST['id'];
		$quantities = $_POST['quantity'];
		
		if($error = $orders->placeOrder($ids,$quantities))
		{
			echo $error;
		}
		else
		{
			echo 1;
		}
		die();
	}
	else if($_POST['type'] == 'cancelOrder')
	{
		if($orders->cancelOrder($_POST['orderId'])){
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
		
		if($error = $orders->updateOrder($orderId,$ids,$quantities))
		{
			echo $error;
		}
		else
		{
			echo 1;
		}
		die();
	}
	// admin orders
	else if($_POST['type'] == "adminViewOrders" && $_SESSION['user']->admin){
		
		echo json_encode($orders->adminViewOrders());
	}