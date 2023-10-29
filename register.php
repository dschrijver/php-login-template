<?php
	$passwords_different = false;
	$password_too_weak = false;
	$username_too_short = false;
	$username_too_long = false;
	$user_exists = false;
	$email_not_valid = false;
	$email_exists = false;
	$verification_sent = false;

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		include("config.php");

		$username = $_POST["username"];
		$password = $_POST["password"];
		$confirm_password = $_POST["confirm_password"];
		$email = $_POST["email"];

		if ($password != $confirm_password) {
			$passwords_different = true;
		}

		if (strlen($password) < 6) {
			$password_too_weak = true;
		}

		if (strlen($username) < 3) {
			$username_too_short = true;
		}

		if (strlen($username) > 20) {
			$username_too_long = true;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$email_not_valid = true;
		}

		$sql = "SELECT * FROM `users` WHERE `username` = ?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows == 1) {
			$user_exists = true;
		}
		$stmt->close();

		$sql = "SELECT * FROM `users` WHERE `email` = ?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows == 1) {
			$email_exists = true;
		}
		$stmt->close();

		if (!($user_exists || $email_exists || $passwords_different || $password_too_weak || $username_too_long || $username_too_short || $email_not_valid)) {
			$password_hash = password_hash($password, PASSWORD_DEFAULT);

			$activation_code = bin2hex(random_bytes(16));
			$activation_hex = password_hash($activation_code, PASSWORD_DEFAULT);


			// Uncomment this block and comment out "$email_confirmed = 1;" to turn on email verification.

			// $activation_link = $_SERVER['SERVER_NAME'] . "/activate.php?email=" . $email . "&activation_code=" . $activation_code;
			// $message = "Dear ".$username.",\n\n Activate your account using the link below:\n\n".$activation_link."\n\nKind regards";
			// $sender_email = "verification@".$_SERVER['SERVER_NAME'];
			// $header = "From:" . $sender_email;
			// if(!mail($email, "Your activation link", $message, $header)) {
			// 	die("Failed to send verification mail.");
			// } 
			// $email_confirmed = 0;
			$email_confirmed = 1;
			$verification_sent = true;

			$sql = "INSERT INTO `users` (`username`, `password`, `email`, `email_confirmed`, `activation_code`) VALUES (?, ?, ?, ?, ?)";
			$stmt = $con->prepare($sql);
			$stmt->bind_param("sssis", $username, $password_hash, $email, $email_confirmed, $activation_hex);
			if (!$stmt->execute()) {
				$stmt->close();
				die("Failed to register: " . $con->error);
			}
			$stmt->close();
		}
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
	<title>Register</title>
</head>
<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card">
					<h2 class="card-title text-center">Register</h2>
					<div class="card-body py-md-4">
					<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST" >
						<div class="form-group">
							<span style = "color: red">
								<?php
									if ($user_exists) {
										echo "Username already exists.";
									}
									if ($username_too_long) {
										echo "Username can't be longer than 20 characters.";
									}
									if ($username_too_short) {
										echo "Username can't be shorter than 3 characters.";
									}
								?>
							</span>
						 	<input type="text" class="form-control" id="username" placeholder="Username" name = "username" required>
						</div>
						<div class="form-group">
							<span style = "color: red">
								<?php
									if ($email_exists) {
										echo "Email is already in use.";
									} 
									if ($email_not_valid) {
										echo "Email address is not valid.";
									}
								?>
							</span>
						 	<input type="email" class="form-control" id="email" placeholder="Email" name = "email" required>
						 </div>   
						<div class="form-group">
							<span style = "color: red">
								<?php
									if ($password_too_weak) {
										echo "Password should be at least 6 characters long.";
									}
								?>
							</span>
							<input type="password" class="form-control" id="password" placeholder="Password" name = "password" required>
						</div>
						<div class="form-group">
							<span style = "color: red">
								<?php
									if ($passwords_different) {
										echo "Passwords are not the same.";
									}
								?>
							</span>
							<input type="password" class="form-control" id="confirm-password" placeholder="Confirm-password" name = "confirm_password" required>
						</div>
						<div class="form-group">
							<span style = "color: green">
								<?php
									if ($verification_sent) {
										echo "Verification email is sent.";
									}
								?>
							</span>
						</div>
						<div class="d-flex flex-row align-items-center justify-content-between">
							<a href="login.php">Login</a>
							<button class="btn btn-primary" type = "submit">Create Account</button>
						</div>
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>