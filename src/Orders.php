<?php
require("src/Users.php");
class Orders extends Users
{
	public function viewProducts()
	{
		try
		{
			$mysqli = $this->_init();
			$query = "SELECT id, description, `value` FROM product ORDER BY `value` DESC";
			$result = $mysqli->query($query);
			$array = array();
			while($obj = $result->fetch_array(MYSQLI_ASSOC))
			{
				$array[] = $obj;
			}
			$mysqli->close();
			return $array;
		}
		catch(Exception $e)
		{
			exit("Error: problem with viewing the probucts. ".$e->getMessage());
		}
		
	}

	public function viewOrders()
	{
		try
		{
			$mysqli = $this->_init();
			$query = "SELECT `user_order`.`id`, `order_date`, `order_update`, `label`, SUM(`quantity` * `value`) as `total`
					FROM `user_order`
					INNER JOIN `order_product` ON `user_order`.`id` = `order_ref`
					INNER JOIN `product` ON `order_product`.`product_ref` = `product`.`id`
					INNER JOIN `status` ON `status_ref` = `status`.`id`
					WHERE `user_ref` = ? AND `status_ref` <> '4'
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
			return $array;
		}
		catch(Exception $e)
		{
			exit("Error: problem with viewing the orders. ".$e->getMessage());
		}
	}

	public function viewOrder($orderId)
	{
		try
		{
			$mysqli = $this->_init();
			$query = "SELECT `product`.`id`, `value`, `quantity`
					FROM `user_order`
					INNER JOIN `order_product` ON `user_order`.`id` = `order_ref`
					INNER JOIN `product` ON `order_product`.`product_ref` = `product`.`id`
					WHERE `user_ref` = ? AND `status_ref` <> '4' AND `order_ref` = ?
					ORDER BY `value` DESC";
			$stmt = $mysqli->prepare($query);
			$stmt->bind_param('ss', $_SESSION['user']->id, $orderId);
			$stmt->execute();
			$result = $stmt->get_result();
			$array = array();
			while($obj = $result->fetch_array(MYSQLI_ASSOC)){
				$array[] = $obj;
			}
			return $array;
		}
		catch(Exception $e)
		{
			exit("Error: problem with viewing the orders. ".$e->getMessage());
		}
	}

	public function cancelOrder($orderId)
	{
		try
		{
			$mysqli = $this->_init();
			$query = "UPDATE `user_order` SET `status_ref` = '4' WHERE `id` = ? AND `user_ref` = ?";
			$statement = $mysqli->prepare($query);
			$statement->bind_param("ss", $orderId, $_SESSION['user']->id);
			$status = $statement->execute();
			if($status)
				return true;
		}
		catch(Exception $e)
		{
			exit("Error: problem with cancelling an order. ".$e->getMessage());
		}
	}

	public function placeOrder($ids,$quantities)
	{
		try
		{
			$error = 0;
			foreach($quantities as $key=>$qty)
			{
				if($qty == 0) {
					unset($quantities[$key]);
					unset($ids[$key]);
				}
			}
			$mysqli = $this->_init();
			if(count($quantities))
			{
				$status_ref = 1;
				$query = "INSERT INTO user_order(user_ref, order_update, status_ref) VALUES(?,NOW(),?)";
				$statement = $mysqli->prepare($query);
				$statement->bind_param("ss", $_SESSION['user']->id, $status_ref);
				$status = $statement->execute();
				
				if($status)
				{
					$order_ref = $statement->insert_id;
					foreach($ids as $key=>$productId)
					{
						$quantity = $quantities[$key];
						$query = "INSERT INTO order_product(order_ref, product_ref, quantity) VALUES(?, ?, ?)";
						$statement = $mysqli->prepare($query);
						$statement->bind_param("sss", $order_ref, $productId, $quantity);
						$status = $statement->execute();
					}
				}
				else
				{
					$error = "Something went wrong with the user order";
				}
				$statement->close();
				$mysqli->close();
				return $error;
			}
			else
			{
				return "No products added";
			}
		}
		catch(Exception $e)
		{
			exit("Something went wrong with the user order");
		}
	}

	public function updateOrder($orderId,$ids,$quantities)
	{
		try
		{
			$error = 0;
			foreach($quantities as $key=>$qty){
				if($qty == 0) {
					unset($quantities[$key]);
					unset($ids[$key]);
				}
			}
			
			if(count($ids)){
				$status_ref = 1;
				$mysqli = $this->_init();
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
				$statement->close();
				$mysqli->close();

				echo $error;
				die();
			}
			else{
				echo "No product updated";
				die();
			}
		}
		catch(Exception $e)
		{
			exit("Something went wrong with the user order");
		}
	}

	public function adminViewOrders()
	{
		try
		{
			if(!$_SESSION['user']->admin) die;
			$mysqli = $this->_init();
			$query = "SELECT `surname`,`firstname`,`user_order`.`id`, `order_date`, `order_update`, `label`, SUM(`quantity` * `value`) as `total`
						FROM `user`
						INNER JOIN `user_order` ON `user`.`id` = `user_order`.`user_ref`
						INNER JOIN `order_product` ON `user_order`.`id` = `order_ref`
						INNER JOIN `product` ON `order_product`.`product_ref` = `product`.`id`
						INNER JOIN `status` ON `status_ref` = `status`.`id`
						WHERE `status_ref` <> '4'
						GROUP BY `user_order`.`id`
						ORDER BY `order_update` DESC";

			$result = $mysqli->query($query);
			$array = array();
			while($obj = $result->fetch_array(MYSQLI_ASSOC))
			{
				$array[] = $obj;
			}
			return $array;
		}
		catch(Exception $e)
		{
			exit("Error: problem with viewing admin orders. ".$e->getMessage());
		}
	}
}