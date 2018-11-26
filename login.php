<?php
ini_set("display_errors", false);
error_reporting(E_ALL);
require("settings.php");

if(isset($_POST["username"]) && isset($_POST["password"]))
{
	$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, "password");
	$error = "";
	if(isset($_POST["firstname"]) && isset($_POST["surname"]))
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
			$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_database);
			$query = "INSERT INTO user(username, password, firstname, surname) VALUES(?, PASSWORD(?), ?, ?)";
			$statement = $mysqli->prepare($query);
			$statement->bind_param("ssss", $username, $password, $firstname, $surname);
			$status = $statement->execute();
			if($status)
			{
				$id = $statement->insert_id;
				$statement->close();
				$mysqli->close();
				session_start();
				$user = (object)array("id" => $id, "firstname" => $firstname, "surname" => $surname, "admin" => false);
				$_SESSION["user"] = $user;
				header("Location: order.php");
				die;
			}
			$error = "Username already exists";
			$statement->close();
			$mysqli->close();
		}

	} else
	{
		$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_database);
		$query = "SELECT firstname, surname, admin, id FROM user WHERE username = ? AND password = PASSWORD(?) LIMIT 1";
		$statement = $mysqli->prepare($query);
		$statement->bind_param("ss", $username, $password);
		$status = $statement->execute();
		if($status)
		{
			$statement->bind_result($firstname, $surname, $admin, $id);
			$statement->fetch();
			$statement->close();
			$mysqli->close();
			if(isset($firstname))
			{
				session_start();
				$user = (object)array("id" => $id, "firstname" => $firstname, "surname" => $surname, "admin" => $admin ? true : false);
				$_SESSION["user"] = $user;
				header("Location: order.php");
				die;
			}
		}
		$error = "Invalid Login";
		$statement->close();
		$mysqli->close();
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

	