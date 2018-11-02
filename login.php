<?php
$error = "";

if(isset($_POST["username"]) && isset($_POST["password"]))
{

	require("src/Users.php");

	$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, "password");
	
	if(isset($_POST["firstname"]) && isset($_POST["surname"])) // create a user
	{
		$firstname = filter_input(INPUT_POST, "firstname", FILTER_SANITIZE_STRING);
		$surname = filter_input(INPUT_POST, "surname", FILTER_SANITIZE_STRING);
		if(strlen($password) < 5)
		{
			$error = "Password must be more than 5 characters";
		}

		if(strlen($username) < 3)
		{
			$error = "Username must be more than 3 characters";
		}

		if(!$error)
		{
			$users = new Users();
			if($users->_CreateUser($firstname,$surname,$username,$password))
			{
				header("Location: order.php");
				die;
			}
			else
			{
				$error = "Username already exists";
			}
		}
	}
	else // logging in
	{
		$users = new Users();

		if($users->_Login($username, $password))
		{
			header('location: order.php');
			die;
		}
		else
		{
			$error = "Login or Password was incorrect!";
		}
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="styles.css">
		<script src="jquery-3.3.1.min.js"></script>
	</head>
	<body>
		<div class="login-page">
			<div class="form">
				<form method="POST" class="register-form">
					<input type="text" name="firstname" placeholder="firstname" required/>
					<input type="text" name="surname" placeholder="surname" required/>
					<input type="text" name="username" placeholder="username" required/>
					<input type="password" name="password" placeholder="password" required/>
					<button>create</button>
					<p class="message">Already registered? <a href="">Sign In</a></p>
				</form>
				<form method="POST" class="login-form">
					<input type="text" name="username" placeholder="username" required/>
					<input type="password" name="password" placeholder="password" required/>
					<button>login</button>
					<?php if($error) echo "<span class='err'>$error</span>"; ?>
					<p class="message">Not registered? <a href="#">Create an account</a></p>
				</form>
			</div>
		</div>
		<script>
			$('.message a').click(function()
			{
				$('form').animate({height: "toggle", opacity: "toggle"}, "slow");
			});
		</script>
	</body>
<html>

	