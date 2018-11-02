<?php
require("src/Database.php");
class Users extends Database{

	public function _Login($username, $password)
	{
		$isLoggedin = false;
		try{
			$mysqli = $this->_init();
			$query = "SELECT firstname, surname, admin, id FROM user WHERE username = ? AND password = PASSWORD(?) LIMIT 1";
			$statement = $mysqli->prepare($query);
			$statement->bind_param("ss", $username, $password);
			$status = $statement->execute();
			
			if($status)
			{
				$statement->bind_result($firstname, $surname, $admin, $id);
				$statement->fetch();
				
				if(isset($firstname))
				{
					session_start();
					$user = (object)array("id" => $id, "firstname" => $firstname, "surname" => $surname, "admin" => $admin ? true : false);
					$_SESSION["user"] = $user;
					$isLoggedin = true;
				}
				$statement->close();
				$mysqli->close();
				return $isLoggedin;
			}
		} catch(Exception $e){
			exit("Error: problem with logging in!");
		}
	}

	public function _CreateUser($firstname,$surname,$username,$password)
	{
		$isLoggedin = false;
		try{
			$mysqli = $this->_init();
			$query = "INSERT INTO user(username, password, firstname, surname) VALUES(?, PASSWORD(?), ?, ?)";
			$statement = $mysqli->prepare($query);
			$statement->bind_param("ssss", $username, $password, $firstname, $surname);
			$status = $statement->execute();
			if($status)
			{
				$id = $statement->insert_id;
				session_start();
				$user = (object)array("id" => $id, "firstname" => $firstname, "surname" => $surname, "admin" => false);
				$_SESSION["user"] = $user;
				$isLoggedin = true;
			}
			$statement->close();
			$mysqli->close();
			return $isLoggedin;
		}
		catch(Exception $e)
		{
			exit("Error: problem with create user!");
		}
	}

	public function _LoggedIn()
	{
		try
		{
			session_start();
			if(empty($_SESSION['user'])) return false;

			$mysqli = $this->_init();
			$query = "SELECT firstname, surname, admin, id FROM user WHERE id = ? LIMIT 1";
			$statement = $mysqli->prepare($query);
			$statement->bind_param("s", $_SESSION['user']->id);
			$status = $statement->execute();
			$statement->close();
			if(!$status) return false;

			return true;
		}
		catch(Exception $e)
		{
			exit("Error: cannot check if you're logged in!");
		}
	}
}