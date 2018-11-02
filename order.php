<?php
	require("src/Orders.php");

	$orders = new Orders();
	if(!$orders->_LoggedIn())
	{
		header("Location: index.php");
		die;
	}
?><!DOCTYPE html>
<html>
	<head>
		<title>Ordering</title>
		<link rel="stylesheet" href="styles.css">
		<script type="text/javascript" src="jquery-3.3.1.min.js"></script>
		<script type="text/javascript">
		<?php
			if($_SESSION['user']->admin){
		?>
			$(document).ready(function(){
				adminViewOrder();
			});

			function adminViewOrder()
			{
				$.ajax({
					url: 'process.php',
		            type: 'POST',
		            data: { type: "adminViewOrders" },
		            dataType: "json",
		            success: function (json) {
		            	console.log(json);
		            	$('.orderTable').html(
		            		'<tr>'+
		            			'<td>Surname</td>'+
		            			'<td>First Name</td>'+
								'<td>Order #</td>'+
								'<td>Date</td>'+
								'<td>Status</td>'+
								'<td>Total</td>'+
							'</tr>'
		            	);

		            	$.each(json,function(key,obj){
		            		$('.orderTable').append(
		            			'<tr>'+
									'<td>'+obj.surname+'</td>'+
									'<td>'+obj.firstname+'</td>'+
									'<td>'+obj.id+'</td>'+
									'<td>'+obj.order_update+'</td>'+
									'<td>'+obj.label+'</td>'+
									'<td>'+formatter.format(obj.total)+'</td>'+
								'</tr>'
			            	);
		            	});
		            },
		            error: function(data){
		            	alert('Error View Orders');
		            }
				});
			}
		<?php
			} else {
		?>
			$(document).ready(function(){
				// update an order
				$('form#placeOrderForm button#updateBtn').click(function(e){
					e.preventDefault();
					var formData = $('form#placeOrderForm');
					
					$.ajax({
						url: 'process.php',
			            type: 'POST',
			            data: formData.serialize(),
			            dataType: "json",
			            success: function (data) {
			            	console.log(data);

			            	viewOrders();
			            	$.each($('input[name^="quantity"]'),function(key,obj){
			            		obj.value = 0;
							});
							editOrder(false);
			            },
			            error: function(data){
			            	alert('Error: '+data.responseText);
			            }
					});

				});

				// place an order
				$('form#placeOrderForm button#orderBtn').click(function(e){
					e.preventDefault();
					var formData = $('form#placeOrderForm');
					
					$.ajax({
						url: 'process.php',
			            type: 'POST',
			            data: formData.serialize(),
			            dataType: "json",
			            success: function (data) {
			            	viewOrders();
			            	$.each($('input[name^="quantity"]'),function(key,obj){
			            		obj.value = 0;
			            	});
			            },
			            error: function(data){
			            	alert('Error: '+data.responseText);
			            }
					});
				});

				$('#cancelBtn').click(function(){
					editOrder(false);
				});

				viewProducts();
				viewOrders();
			});

			function cancelOrder(id)
			{
				if(confirm('Do you want to cancel this order?')){
					
					$.ajax({
						url: 'process.php',
			            type: 'POST',
			            data: { type: "cancelOrder", orderId: id },
			            success: function (data) {
			            	console.log(data);
			            	viewOrders();
			            }
					});
				}
			}

			function viewOrder(id)
			{
				$('#orderId').val(id);
				$.ajax({
					url: 'process.php',
		            type: 'POST',
		            data: { type: "viewOrder", orderId: id },
		            dataType: "json",
		            success: function (json) {
		            	console.log(json);
		            	$.each($('input[name^="quantity"]'),function(key,obj){
		            		obj.value = 0;
		            	});
		            	$.each(json,function(key,obj){
		            		console.log(obj);
		            		$('#'+obj.id).val(obj.quantity)
		            	});
		            	editOrder(true);
		            },
		            error: function(data){
		            	console.log(data);
		            	alert('Error View Order');
		            }
				});
			}

			function viewOrders()
			{
				$.ajax({
					url: 'process.php',
		            type: 'POST',
		            data: { type: "viewOrders" },
		            dataType: "json",
		            success: function (json) {
		            	console.log(json);
		            	$('.orderTable').html(
		            		'<tr>'+
								'<td>Order #</td>'+
								'<td>Date</td>'+
								'<td>Status</td>'+
								'<td>Total</td>'+
								'<td>Update</td>'+
								'<td>Cancel</td>'+
							'</tr>'
		            	);

		            	$.each(json,function(key,obj){
		            		$('.orderTable').append(
		            			'<tr>'+
									'<td>'+obj.id+'</td>'+
									'<td>'+obj.order_update+'</td>'+
									'<td>'+obj.label+'</td>'+
									'<td>'+formatter.format(obj.total)+'</td>'+
									'<td><span class="updateOrder" onclick="viewOrder('+obj.id+');">[edit]</span></td>'+
									'<td><span class="cancelOrder" onclick="cancelOrder('+obj.id+');">[cancel]</span></td>'+
								'</tr>'
			            	);
		            	});
		            },
		            error: function(data){
		            	console.log(data);
		            	alert('Error View Orders');
		            }
				});
			}

			function viewProducts()
			{
				$.ajax({
					url: 'process.php',
		            type: 'POST',
		            data: { type: "viewProducts" },
		            dataType: "json",
		            success: function (json) {
		            	var product = $('.products');

		            	$.each(json, function(key,obj){
		            		$('.products').append(
		            			'<div class="product">'+
									'<div class="productDescription">'+obj.description+'</div>'+
									'<div class="productValue">'+formatter.format(obj.value)+'</div>'+
									'<input type="hidden" name="id[]" value="'+obj.id+'">'+
									'<input type="number" id="'+obj.id+'" name="quantity[]" value="0" min="0" max="10" style="width:80px;">'+
								'</div>'
		            		);
		            	});
		            },
		            error: function(data)
		            {
		            	alert('Error View Products');
		            }
				});
			}

			function editOrder(isEdit)
			{
				if(isEdit)
				{
					$('#orderBtn').hide();
					$('#updateBtn').show();
					$('#cancelBtn').show();
					$('#type').val('updateOrder');
				}
				else
				{
					$('#orderBtn').show();
					$('#updateBtn').hide();
					$('#cancelBtn').hide();
					$('#type').val('placeOrder');
					$.each($('input[name^="quantity"]'),function(key,obj){
	            		obj.value = 0;
	            	});
	            	$('#orderId').val('');
				}
			}
		<?php
			}
		?>
			const formatter = new Intl.NumberFormat('en-ZA', {
				style: 'currency',
				currency: 'ZAR',
				minimumFractionDigits: 2
			});
		</script>
	</head>
	<body>
		<a href="logout.php">Logout</a>
	
	<?php
		if($_SESSION['user']->admin){
	?>
		<a href="csv.php" target="_blank">Export</a>
		<table class="orderTable">
			<tr>
				<td>Order #</td>
				<td>Date</td>
				<td>Status</td>
				<td>Total</td>
				<td>Update</td>
				<td>Cancel</td>
			</tr>
		</table>

	<?php
		}
		else
		{
	?>

			<div class="form">
				<form id="placeOrderForm" method="POST" class="login-form">


					<div class="products">
						
					</div>
					<input type="hidden" id="orderId" name="orderId" value=""/>
					<input type="hidden" id="type" name="type" value="placeOrder"/>
					<button type="button" id="orderBtn">Place Order</button>
					<button type="button" style="display:none;" id="updateBtn">Update Order</button>
					<button type="button" style="display:none;" id="cancelBtn">Cancel</button>
				</form>

				
				<table class="orderTable">
					<tr>
						<td>Order #</td>
						<td>Date</td>
						<td>Status</td>
						<td>Total</td>
						<td>Update</td>
						<td>Cancel</td>
					</tr>
				</table>
				<!-- <button id="viewOrders">Refresh</button> -->
			</div>
	<?php
		}
	?>
	</body>
</html>