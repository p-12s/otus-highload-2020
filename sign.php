<?php
require 'core/load.php';
require 'connect/DB.php';

if (isset($_POST['first-name']) && !empty($_POST['first-name'])) {
	$upFirst = $_POST['first-name'];
	$upLast = $_POST['last-name'];
	$upEmailMobile = $_POST['email-mobile'];
	$upPassword = $_POST['up-password'];
	$birthDay = $_POST['birth-day'];
	$birthMonth = $_POST['birth-month'];
	$birthYear = $_POST['birth-year'];
	if (!empty($_POST['gen'])) {
		$upGen = $_POST['gen'];
	}
	$birth = '' . $birthYear . '-' . $birthMonth . '-' . $birthDay . '';
	if (empty($upFirst) or empty($upLast) or empty($upEmailMobile) or empty($upGen)) {
		$error = 'All fields are required';
	} else {
		$first_name = $loadFromUser->checkInput($upFirst);
		$last_name = $loadFromUser->checkInput($upLast);
		$email_mobile = $loadFromUser->checkInput($upEmailMobile);
		$password = $loadFromUser->checkInput($upPassword);
		$screenName = '' . $first_name . '_' . $last_name . '';
		if (DB::query('SELECT screen_name FROM user WHERE screen_name = :screenName', array( ':screenName' => $screenName ))) {
			$screenRand = rand();
			$userLink = '' . $screenName . '' . $screenRand . '';
		} else {
			$userLink = $screenName;
		}
		if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email_mobile)) {
            if (DB::query('SELECT mobile FROM user WHERE mobile=:mobile', array( ':mobile' => $email_mobile ))) {
                $error = 'Mobile number is already in use.';
            } else {
                $user_id = $loadFromUser->create('user', array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'mobile' => $email_mobile,
                    'password' => password_hash($password, PASSWORD_BCRYPT) ,
                    'screen_name' => $screenName,
                    'user_link' => $userLink,
                    'birthday' => $birth,
                    'gender' => $upGen
                ));
                $loadFromUser->create('profile', array(
                    'user_id' => $user_id,
                    'birthday' => $birth,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'profile_pic' => 'assets/image/defaultProfile.png',
                    'cover_pic' => 'assets/image/defaultCover.png',
                    'gender' => $upGen
                ));
                $tstrong = true;
                $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                $loadFromUser->create('token', array(
                    'token' => sha1($token) ,
                    'user_id' => $user_id
                ));
                setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);
                header('Location: index.php');
            }
		} else {
			if (!filter_var($email_mobile)) {
				$error = "Invalid Email Format";
			} else {
				if ((filter_var($email_mobile, FILTER_VALIDATE_EMAIL)) && $loadFromUser->checkEmail($email_mobile)) {
					$error = "Email is already in use";
				} else {
					$user_id = $loadFromUser->create('user', array(
						'first_name' => $first_name,
						'last_name' => $last_name,
						'email' => $email_mobile,
						'password' => password_hash($password, PASSWORD_BCRYPT) ,
						'screen_name' => $screenName,
						'user_link' => $userLink,
						'birthday' => $birth,
						'gender' => $upGen
					));
					$loadFromUser->create('profile', array(
						'userId' => $user_id,
						'birthday' => $birth,
						'firstName' => $first_name,
						'lastName' => $last_name,
						'profilePic' => 'assets/image/defaultProfile.png',
						'coverPic' => 'assets/image/defaultCover.png',
						'gender' => $upGen
					));
					$tstrong = true;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
					$loadFromUser->create('token', array(
						'token' => sha1($token) ,
						'user_id' => $user_id
					));
					setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);
					header('Location: index.php');
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>facebook-clone</title>
		<link rel="stylesheet" href="assets/css/style.css" />
	</head>
	<body>
		<header class="header">
			<div class="header_container">
				<div class="logo_text">Clonebook</div>
				<div class="sign-in">
					<label class="sign-in__label" for="sign-in">
						<input class="sign-in__input" value="Войти в существующий аккаунт" type="submit" id="sign-in">
					</label>
				</div>
			</div>
		</header>
		<main class="main" style="width:100%;">
			<div class="left-side">
				<img src="assets/image/facebook_signin_image.png" alt="">
			</div>
			<div class="right-side">
				<div class="error">
					<?php if (!empty($error)) {
						echo $error;
					}; ?>
				</div>
				<h1 style="color:#212121;">Create an account</h1>
				<div style="color:#212121; font-size:20px">It's free and always will be</div>
					<form action="sign.php" method="post" name="user-sign-up">
						<div class="sign-up-form">
							<div class="sign-up-name">
								<input type="text" name="first-name" id="first-name" class="text-field" placeholder="First Name">
								<input type="text" name="last-name" id="last-name" placeholder="Last Name" class="text-field">
							</div>
							<div class="sign-wrap-mobile">
								<input type="text" name="email-mobile" id="up-email" placeholder="Mobile number or email address" class="text-input">
							</div>
							<div class="sign-up-password">
								<input type="password" name="up-password" id="up-password" class="text-input" placeholder="Password">
							</div>
							<div class="sign-up-birthday">
								<div class="bday">Birthday</div>
								<div class="form-birthday">
									<select name="birth-day" id="days" class="select-body"></select>
									<select name="birth-month" id="months" class="select-body"></select>
									<select name="birth-year" id="years" class="select-body"></select>
								</div>
							</div>
							<div class="gender-wrap">
								<input type="radio" name="gen" id="fem" value="female" class="m0">
								<label for="fem" class="gender">Female</label>
								<input type="radio" name="gen" id="male" value="male" class="m0">
								<label for="male" class="gender">Male</label>
							</div>
							<div class="term">
								<p>By clicking Sign Up, you agree to our terms, Data policy and Cookie policy. You may receive SMS notifications from us and can opt out at any time.</p>
							</div>
							<input type="submit" value="Sign Up" class="sign-up">
						</div>
					</form>
				</div>
			</div>
		</div>
		<script src="assets/js/jquery.js"></script>
		<script>
			for (var i = new Date().getFullYear(); i > 1980; i--) {
				$("#years").append($('<option/>').val(i).html(i));
			}
			for (var i = 1; i < 13; i++) {
				$('#months').append($('<option/>').val(i).html(i));
			}
			updateNumberOfDays();
			function updateNumberOfDays() {
				$('#days').html('');
				var month = $('#months').val();
                var year = $('#years').val();
                var days = daysInMonth(month, year);
				for (var i = 1; i < days + 1; i++) {
					$('#days').append($('<option/>').val(i).html(i));
				}
			}
			$('#years, #months').on('change', function() {
				updateNumberOfDays();
			});
			function daysInMonth(month, year) {
				return new Date(year, month, 0).getDate();
			}
		</script>
	</body>
</html>
