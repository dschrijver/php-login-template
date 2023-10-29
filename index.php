<?php
	include("session.php");
	include("config.php");

	$sql = "SELECT `username` FROM `users` WHERE `id` = ?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param("i", $_SESSION["id"]);
	if (!$stmt->execute()) {
		die("Failed to retrieve user information: " . $con->error);
	}
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$username = $row["username"];

	$stmt->close();
	$con->close();
?>
<html>
	<head>
		<title>My website</title>
		<meta name = "viewport" content = "width=device-width, initial-scale=1"/>
		<style>
			body {
				font-family: sans-serif;
				background-color: #eeeeee;
				margin: 0;
				padding: 0;
				width: 100%;
			}

			#container {
				width: 100%;
				margin: 0;
				display: flex;
				flex-direction: column;
			}
			#header-container {
				width: 600px;
				margin: 0 auto;
				padding: 8px;
			}
			.logout-btn {
				width: 100%;
				margin: 0;
				color: #fff;
				background: gray;
				border: none;
				padding: 10px;
				border-radius: 4px;
				border-bottom: 4px solid black;
				transition: all .2s ease;
				outline: none;
				text-transform: uppercase;
				font-weight: 700;
			}

			.logout-btn:hover {
				background: black;
				color: #ffffff;
				transition: all .2s ease;
				cursor: pointer;
			}
		</style>
	</head>
	<body>
		<div id = "container">
			<div id = "header-container">
				<h1>Welcome <?=$username?></h1>
				<button class="logout-btn" type="button" onclick="location.href='logout.php'">Log out</button>
			</div>
		</div>
	</body>
</html>