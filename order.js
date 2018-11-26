$(document).ready(function() 
{
		refreshTable();
});

function PlaceNewOrderToggle()
{
	$("#update_order_button").toggle();
	$("#new_order_form").toggle();
	$("#place_order_button").toggle();
	$("#place_new_order_button").toggle();
	$("#toggle_order_table").toggle();
	$("#delete_order").toggle();
}
function CancelNewOrderToggle()
{
	$("#new_order_form").toggle();
	$("#place_order_button").toggle();
	$("#place_new_order_button").toggle();
	$("#toggle_order_table").toggle();
	$("#update_order_button").toggle();
	$("#delete_order").toggle();
}

function UpdateOrderToggle()
{
	$("#place_new_order_button").toggle();
	$("#update_order_button").toggle();
	$("#update_order_div").toggle();
	$("#delete_order").toggle();
}
function DeleteOrderToggle()
{

	$("#delete_order").toggle();
	$("#update_order_button").toggle();
	$("#place_new_order_button").toggle();
	$("#delete_order_div").toggle();
	$("#delete_order_num_text_box").val("");
}
function CancelSpecifyUpdateOrderToggle()
{
	$("#update_order_div").toggle();
	$("#place_new_order_button").toggle();
	$("#update_order_button").toggle();
	$("#order_num_text_box").val("");
	$("#delete_order").toggle();
}
function UpdateSpecifiedOrderToggle()
{
	$("#new_order_form").toggle();
	$("#update_order").toggle();
	$("#place_new_order_button").toggle();
	$("#delete_order").toggle();
	$("#update_order_button").toggle();
	// set page to default view
}

function SpecifyUpdateOrder(user)
{
	var orderNum = $('#order_num_text_box').val();
	$.ajax(
	{
		url : "get_order_details.php",
		data : {orderNum: orderNum}, 
		dataType : "json",
		type : "POST",
		success : function(result)
		{

			$("#update_order_div").toggle();
			$("#new_order_form").toggle();
			for (rowIndex = 0; rowIndex < result.length; ++rowIndex)
			{
				$('.order_product').each(function()
				{
					var productID = $(this).attr("id");
					if(productID == result[rowIndex].product_ref)
					{
						$(this).val(result[rowIndex].quantity);
						return;
					}
				});

			};
		},
		error : function(jqXHR, textStatus, errorThrown)
		{
			console.log(textStatus, errorThrown);
		}
	}),
	$("#update_order").toggle();
}

function UpdateOrder()
{
	var orderNum = $('#order_num_text_box').val();
	// var orderNum = 88;
	// duplicate code. Need to find out how to make function return value
	var product_quantities = [];
	$('.order_product').each(function()
	{
		if($(this).val() == "")
		{
			return;
		}
		product_quantities.push({product_id: $(this).attr("id"), product_quantity: $(this).val()});
		// product_quantities.push({order_number: orderNum, product_id: $(this).attr("id"), product_quantity: $(this).val()});
		// product_quantities.push({product_id: $(this).attr("id"), product_amount: $(this).attr("amount"), product_quantity: $(this).val()});
	$(this).val("");
	});

	$.ajax(
	{
		url : "update_order.php",
		data : {order_number: orderNum, products: product_quantities},
		dataType : "json",
		type : 'POST',
		success : function(result)
		{
			// add code here to update table row only, live.
			refreshTable();
			console.log(result);
		},
        error : function(jqXHR, textStatus, errorThrown)
        {
            console.log(textStatus, errorThrown);
        }
	});
	$('#order_num_text_box').val("");
	UpdateSpecifiedOrderToggle();
}

function DeleteOrder()
{

	var orderNum = $('#delete_order_num_text_box').val();

	$.ajax(
	{
		url : "delete_order.php",
		data : {order_number: orderNum},
		dataType : "json",
		type : 'POST',
		success : function(result)
		{
			// add code here to update table row only, live.
			refreshTable();
			console.log(result);
		},
        error : function(jqXHR, textStatus, errorThrown)
        {
            console.log(textStatus, errorThrown);
        }
	});
	DeleteOrderToggle();
}

function PlaceOrder()
{
	// duplicate code. Need to find out how to make function return value
	var product_quantities = [];
	$('.order_product').each(function()
	{
		if($(this).val() == "")
		{
			return;
		}
		product_quantities.push({product_id: $(this).attr("id"), product_amount: $(this).attr("amount"), product_quantity: $(this).val()});
	$(this).val("");
	});

	$.ajax(
	{
		url : "place_order.php",
		data : {products: product_quantities},
		dataType : "json",
		type : 'POST',
		success : function(result)
		{
			// addOrderToTable(result);
			refreshTable();
			
		},
        error : function(jqXHR, textStatus, errorThrown)
        {
            console.log(textStatus, errorThrown);
        }
	});

	$("#update_order_button").toggle();
	$("#new_order_form").toggle();
	$("#place_order_button").toggle();
	$("#place_new_order_button").toggle();
	$("#toggle_order_table").toggle();
	$("#delete_order").toggle();
}

function addOrderToTable(order)
{
	$("#order_table").append("<tr>" +
					"<td>user</td>" +
					"<td>" +  order.order_id + "</td>" +
					"<td>" + order.order_value + "</td>" +
					"<td>" + order.date + "</td>" +
					"<td>" + order.last_updated + "</td>" +
					"<td>" + order.status + "</td>" +
							"</tr>");
}

function refreshTable()
	{
		console.log('you have reached the function');

		$.ajax(
		{
			url : "view_table.php",
			dataType : "json",
			success : function(rows)
			{
				// Find and remove all rows greater then row one in order_table
				$("#order_table").find("tr:gt(0)").remove();
				// rebuild table
				for (index = 0; index < rows.length; ++index) {
					$("#order_table").append(
					"<tr>" +
						"<td>" + rows[index].firstname + "</td>" +
						"<td>" + rows[index].id + "</td>" +
						"<td>" + rows[index].Value + "</td>" +
						"<td>" + rows[index].order_date + "</td>" +
						"<td>" + rows[index].order_update + "</td>" +
						"<td>" + rows[index].Status + "</td>" +
					"</tr>");
				};
			}
		});
	}

	