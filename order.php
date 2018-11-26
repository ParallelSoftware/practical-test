<?php
require("settings.php");
session_start();

if(!isset($_SESSION["user"]))
{
	header("Location: login.php");
}

$user = $_SESSION["user"];
echo 'Welcome ' . $user->firstname;

// $adminUserColumnHeader = '';
// if($user->admin)
// {
// 	$adminUserColumnHeader .= "<th>User</th>";
// }

// $adminUserColumnCell = '';
// if($user->admin)
// {
// 	$adminUserColumnCell .= "<td>" . $user->firstname . "</td>";
// }

$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_database);
$query = "SELECT * FROM product";
$resultProducts = $mysqli->query($query);

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

	echo "<table id='order_table' border='1'>
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
		echo "<tr>";
		echo "<td>" . $row['firstname'] . "</td>";
		echo "<td>" . $row['id'] . "</td>";
		echo "<td>" . $row['Value'] . "</td>";
		echo "<td>" . $row['order_date'] . "</td>";
		echo "<td>" . $row['order_update'] . "</td>";
		echo "<td>" . $row['Status'] . "</td>";
		// echo "<td>" . "<button type='submit' name='edit' value=''>Edit</button>" . "</td>";
		echo "</tr>";
	}
		echo "</table><a target='_blank' href='export.php'>Export</a>"; 
		// echo "<?php echo '?v'.$version
	$mysqli->close();

	$version = time();
?>	

<!DOCTYPE html>
<html>
<head>
	</script>
		<script src="jquery-3.3.1.min.js"></script>
		<script src="order.js"></script>

		<script type="text/javascript">
			// function PlaceNewOrderToggle()
			// {
			// 	$("#new_order_form").toggle();
			// 	$("#place_order_button").toggle();
			// 	$("#place_new_order_button").toggle();
			// }
			// function PlaceOrder()
			// {
				// <?php
				// 	$newOrderRef = '';

				// 	$query = "SELECT order_ref FROM order_product";
				// 	$result = $mysqli->query($query);
				// 	$allOrders = mysqli_fetch_array($result);


				// 	$$query ="SELECT MAX(order_ref) FROM order_product";
				// 	$highestOrderRef = $mysqli->query($query);

				// 	for ($i = '1'; $i <= $highestOrderRef; $i++)
				// 	{
				// 		foreach ($allOrders as $order)
				// 		{
				// 			if (in_array($i ,$allOrders))
				// 			{

				// 			}
				// 		}
						
				// 	}

				// 	while( $row = mysqli_fetch_array($allOrders) ){
				// 		if ($row['order_product'] ) {}
				// 	}

				// $mysqli->close();
				// ?>

			// 	$('#new_order_form input:text').each(function(){
			// 		var amount = $(this);
			// 		//alert(amount.attr('id'));
			// 		//alert(amount.val());
			// 		//amount.val("");
			// 		// alert("<?php echo $user->id; ?>");
			// 		// <?php
			// 		// 	$query = sprintf("INSERT INTO order_product (user_order_ref, order_ref, product_ref, quantity) VALUES ('%s', '%s', '%s', '%s')",
			// 		// 		mysqli_real_escape_string($mysqli, $user->id),
			// 		// 		mysqli_real_escape_string($mysqli, $hash),
			// 		// 		mysqli_real_escape_string($mysqli, $gender),
			// 		// 		mysqli_real_escape_string($mysqli, $color)
			// 		// 	);
			// 		// ?>

			// 		});


			// 	$("#new_order_form").toggle();
			// 	$("#place_order_button").toggle();
			// 	$("#place_new_order_button").toggle();
			// }
		</script>
		
</head>
<body>
	<?php
	
		echo "<form id='new_order_form' style='display:none'>";
		echo "<table border='1'>
		<tr>
		<th>Product</th>
		<th>Value</th>
		<th>Amount?</th>
		</tr>";
		while ($row = mysqli_fetch_array($resultProducts) ) {
			echo "<tr>";
			echo "<td>" . $row['description'] . "</td>";
			echo "<td>" . 'R' . $row['value']. '.00' . "</td>";
			echo "<td>" . "<input type='text' class='order_product' amount='{$row['value']}' id='{$row['id']}' name='inputAdd' style='width: 65px;' maxlength='2'></input>" . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</form>";
		// if (!$user->isAdmin) {
			//place new order group
			echo "<input type='button' onclick='PlaceNewOrderToggle()' id='place_new_order_button' value='Place new order'>
			<input type='button' onclick='PlaceOrder()' id='place_order_button' value='Place order' style='display:none'>
			<input type='button' onclick='CancelNewOrderToggle($row)' id='toggle_order_table' value='Cancel' style='display:none'>";
			// update order group
			echo "<input type='button' onclick='UpdateOrderToggle()' id='update_order_button' value='Update order'>
			<input type='button' onclick='PlaceNewOrderToggle($row)' id='toggle_order_table' value='Cancel' style='display:none'>
			<input type='button' onclick='UpdateOrder()' id='update_order' value='Update' style='display:none'>

			<input type='button' onclick='DeleteOrderToggle()' id='delete_order' value='Delete order'>";
		// }
	?>
	
	<div id="update_order_div" style="display:none">Specify order number to update -
		<input type="text" id="order_num_text_box" style='width: 65px;' maxlength='5'>
		<input type="button" onclick="SpecifyUpdateOrder()" value="Go">
		<input type="button" onclick="CancelSpecifyUpdateOrderToggle()" value="Cancel">
	</div>
	<div id="delete_order_div" style="display:none">Specify order number to delete -
		<input type="text" id="delete_order_num_text_box" style='width: 65px;' maxlength='5'>
		<input type="button" onclick="DeleteOrder()" value="Go">
		<input type="button" onclick="DeleteOrderToggle()" value="Cancel">
	</div>

</body>
</html>