<?php

/**
 * @file
 * This is the main index page for the site.
 */

require_once('common.php');
if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    $values = [
        'username' => $username,
        'password' => $password
    ];

    $userInfo = $database->login($values);

    if (!empty($userInfo['result'])) {
        $_SESSION['userInfo'] = $userInfo['result'];
        header("Location: profile.php");
    } else {
        $error = "The username or password was incorrect";
    }
}


if (isset($_SESSION['userInfo'])) {
	$userInfo = $_SESSION['userInfo'];
	if ($userInfo->privileges == 'admin') {
		$allOrderInfo = $database->showAllOrders($userInfo);
	} else if ($userInfo->privileges == 'Employee'){
		$assignedRequest = $database->getAssignedRequest($userInfo->userID);
	}
	else {
		$customerOrderInfo = $database->showCustomerActiveOrders($userInfo);
	}
}
if (isset($_POST['species']) && isset($_POST['birthdate']) && isset($_POST['breed'])) {
	$species = filter_var($_POST['species'], FILTER_SANITIZE_STRING);
	$birthdate = filter_var($_POST['birthdate'], FILTER_SANITIZE_STRING);
	$breed = filter_var($_POST['breed'], FILTER_SANITIZE_STRING);

	$pValues = [
		'species' => $species,
		'birthdate' => $birthdate,
		'breed' => $breed
	];

	$response = $database->createPet($pValues);
	$petInfo['result'] = $response[0]['result'];
}

$usererror = "";
$passerror = "";
$confirmPasserror = "";

$usernameErrorcount = 0;
$passwordErrorcount = 0;

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirmpass']) && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email'])) {
	$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	$confirmpass = filter_var($_POST['confirmpass'], FILTER_SANITIZE_STRING);
	$firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
	$lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

	if (preg_match('/[0-9]/', $username) == 0 || preg_match('/[0-9]/', $username) == 0) {
		$usererror = "Username must include at least two numbers";
		$usernameErrorcount++;
	}
	if (strlen($username) < 8) {
			$usererror = "Username must be at least 8 characters long";
			$usernameErrorcount++;
	}

	if (preg_match('/[0-9]/', $password) == 0 || preg_match('/[0-9]/', $password) == 0) {
		$error = "Password must include at least 2 numbers";
		$passwordErrorcount++;
	}
	if (preg_match('/[\'^£$%&*()}{@#~!?><>,|=_+¬-]/',$password) == 0){
		$passerror = "Must include 1 special character";
		$passwordErrorcount++;
	}
	if (strlen($password) < 8) {
		$passerror = "Password must be at least 8 characters long";
		$passwordErrorcount++;
	}
	if (strcmp($password,$confirmpass) != 0){
		$confirmPasserror = "Passwords must match";
		$passwordErrorcount++;
	}

	if ($usernameErrorcount < 1 && $passwordErrorcount < 1){
		$values = [
			'username' => $username,
			'password' => $password,
			'firstName' => $firstname,
			'lastName' => $lastname,
			'email' => $email
		];
		$response = $database->createUser($values);
		if (!empty($response['error'])){
			$usererror = $response['error'];
		}else{
			$userInfo['result'] = $response[0]['result'];
		}
	}

	if (!empty($userInfo['result'])) {
		$_SESSION['userInfo'] = $userInfo['result'];
		header('Location: profile.php');
	}
}



?>





<!DOCTYPE HTML>
<HTML lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width = device-width, initial-scale = 1.0, user-scalable = 1;">
	<link rel="stylesheet" type="text/css" href="css/bestPetIndex.css">
	<link rel="stylesheet" type="text/css" href="css/sideBar.css">
	<link rel="stylesheet" type="text/css" href="css/indexCard.css">
	<link rel="stylesheet" type="text/css" href="css/tab.css">

	<title>Pet Best, LLC</title>
</head>

<body>
  <?php require_once("menu.php") ?>

    <div class="bgimg-5">

		</div>


 <script src="js/tab.js"></script>






<div class="card">
			<div class="tab">
				<button class="tablinks" onclick="openTab(event, 'Login')" id="defaultOpen">Login</button>
				<button class="tablinks" onclick="openTab(event, 'Register')">Register</button>
			</div>


			<div id="Login" class="tabcontent">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      	  <div class="form">
            <p>Please sign in below.</p>
           	 <hr>
									<table>
											<tr>
													<td>
															<label for="username"><b>Username</b></label>
													</td>
													<td>
															<input type="text" style="text-align:center;" placeholder="Enter Username" name="username" required><br>
													</td>
											</tr>
											<tr>
													<td>
															<label for="password"><b>Password</b></label>
													</td>
													<td>
															<input type="password" style="text-align:center;" placeholder="Enter Password" name="password" required><br>
													</td>
											</tr>
											<?php if (isset($error)) : ?>
													<p style="color:red;"><?php echo $error ?></p>
											<?php endif; ?>
									</table>
            	<hr>
            <input type="submit" class="button" value="Sign In"></input>
						</div>
    		</form>
			</div>







<div id="Register" class="tabcontent">
 	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<div class="form">
			<p>Please fill in this form to create an account.</p>
			<hr>
			<table>
				<tr>
					<td>
						<label for="firstname"><b>First Name</b></label>
					</td>
					<td>
						<input type="text" style="text-align:center;" placeholder="Enter First Name"
							name="firstname" required value=<?php if (isset($firstname)){echo $firstname;}?>><br>
					</td>
				</tr>
				<tr>
					<td>
						<label for="lastname"><b>Last Name</b></label>
					</td>
					<td>
						<input type="text" style="text-align:center;" placeholder="Enter Last Name"
							name="lastname" required value=<?php if (isset($lastname)){echo $lastname;}?>><br>
					</td>
				</tr>
				<tr><td><label for="email"><b>Email</b></label></td>
					<td><input type="email" style="text-align:center;" placeholder="Enter Email"
						name="email" required value=<?php if (isset($email)){echo $email;}?>><br></td>
				</tr>
				<tr><td><label for="username"><b>Username</b></label></td>
					<td><input type="text" style="text-align:center;" placeholder="Enter Username"
					name="username" required value=<?php if (isset($username)){echo $username;}?>>
					 <?php echo "<span style=color:red;font-size:.8em;>" . $usererror . "</span>" ?><br></td>
				</tr>
				<tr>
					<td><label for="password"><b>Password</b></label></td>
					<td><input type="password" style="text-align:center;" placeholder="Enter Password"
							name="password" required value=<?php if (isset($password)){echo $password;}?>>
							<?php echo "<span style=color:red;font-size:.8em;>" . $passerror . "</span>" ?><br></td>
				</tr>
				<tr>
					<td><label for="confirmpass"><b>Repeat Password</b></td>
					</label>
					<td><input type="password" style="text-align:center;" placeholder="Repeat Password"
						name="confirmpass" required><?php echo "<span style=color:red;font-size:.8em;>" . $confirmPasserror . "</span>" ?><br>
					</td>
				</tr>
			</table>
			<hr>
			<button type="submit" class="button">Register</button>
</div>
</div>









</body>

</HTML>
