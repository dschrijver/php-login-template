<?php
	$db_credentials = parse_ini_file("db.ini");
	define('DB_SERVER', $db_credentials["server"]);
	define('DB_USERNAME', $db_credentials["username"]);
	define('DB_PASSWORD', $db_credentials["password"]);
	define('DB_DATABASE', $db_credentials["database"]);

	// Create connection
	$con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	// Check connection
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
?>