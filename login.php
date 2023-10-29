<?php
	$login_error = false;
	$email_not_confirmed = false;
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$login_error = true;
		include("config.php");

		$username = $_POST["username"];
		$password = $_POST["password"];

		$sql = "SELECT `id`,`password`,`email_confirmed` FROM `users` WHERE `username` = ?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			$email_confirmed = boolval($row["email_confirmed"]);

			$hash = $row["password"];
			if (password_verify($password, $hash)) {
				$login_error = false;
			}
			if (!$email_confirmed) {
				$email_not_confirmed = true;
			} 
			if (!($login_error || $email_not_confirmed)) {
				session_start();
				$_SESSION["id"] = $row["id"];
				header('Location: index.php');
				exit();
			}
		} 
		$stmt->close();
		$con->close();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="form.css">
	<title>Login</title>
</head>
<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card">
					<h2 class="card-title text-center">Login</h2>
					<div class="card-body py-md-4">
					<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST" >
						<div class="form-group">
							<span style = "color: red">
								<?php
									if ($login_error) {
										echo "Username or password is incorrect.";
									} elseif ($email_not_confirmed) {
										echo "Confirm your email adress.";
									}
								?>
							</span>
						 	<input type="text" class="form-control" id="username" placeholder="Username" name = "username" required autofocus>
						</div>
						<div class="form-group">
							<input type="password" class="form-control" id="password" placeholder="Password" name = "password" required>
						</div>
						<div class="d-flex flex-row align-items-center justify-content-between">
							<a href="register.php">Create an account</a>
							<button class="btn btn-primary" type = "submit">Login</button>
						</div>
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>