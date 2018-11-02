<?php
class Database{
	protected function _init()
	{
		require("./settings.php");
		$mysqli = new mysqli($db_hostname, $db_username, $db_password, $db_database);
		if($mysqli->connect_error)
		{
			exit('Error connecting to database'); //Should be a message a typical user could understand in production
		}
		return $mysqli;
	}
}