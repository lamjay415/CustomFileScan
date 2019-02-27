<?php
$forename ="";
$surname = "";
$username = "";
$password = "";
$email = "";
if(isset($_POST['forename']))
	$forename = fix_string($_POST['forename']);
if(isset($_POST['surname']))
	$surname = fix_string($_POST['surname']);
if(isset($_POST['username']))
	$username = fix_string($_POST['username']);
if(isset($_POST['email']))
	$email = fix_string($_POST['email']);
if(isset($_POST['password']))
	$password = fix_string($_POST['password']);

$fail = validate_forename($forename);
$fail .= validate_surname($surname);
$fail .= validate_username($username);
$fail .= validate_email($email);
$fail .= validate_password($password);

echo "<!DOCTYPE html>\n<html><title>Register</title>";

if($fail == ""){
	header("location: login.php");
	$conn = new mysqli("localhost","root","abc123");
	if ($conn->connect_error) die($conn->connect_error);
	mysqli_select_db($conn,"project");
	$query = "CREATE TABLE IF NOT EXISTS users(forename VARCHAR(32) NOT NULL,surname VARCHAR(32) NOT NULL,
		username VARCHAR(32) NOT NULL UNIQUE,email VARCHAR(64) NOT NULL,password VARCHAR(32) NOT NULL)";
	$result = $conn->query($query);
	if (!$result) die($conn->error);
	$forename = mysql_fix_string($conn,$forename);
	$surname = mysql_fix_string($conn,$surname);
	$username = mysql_fix_string($conn,$username);
	$email = mysql_fix_string($conn,$email);
	$password = mysql_fix_string($conn,$password);
	$salt1 = "p@!d";
	$salt2 = "j&**z";
	$token = hash('ripemd128',"$salt1$password$salt2");
	add_user($conn,$forename,$surname,$username,$email,$token);
	exit();
}

echo <<<_END

<style>
.signup{
	border: 1px solid #999999;
	font: normal 14px helvetica; color:#444444;
}
</style>

<script>
	function validate(form){
		fail = validateForename(form.forename.value)
		fail += validateSurename(form.surname.value)
		fail += validateUsername(form.username.value)
		fail += validateEmail(form.email.value)
		fail += validatePassword(form.password.value)
		
		if(fail == "") return true
		else { alert(fail); return false}
	}
	
	function validateForename(field){
		return (field =="") ? "No Forename was entered. \n" : ""
	}
	function validateSurname(field){
		return (field =="") ? "No Surname was entered. \n" : ""
	}
	function validateUsername(field){
		if(field == "") return "No Username was entered. \n"
		else if(/[^a-zA-Z0-9_-]/.text(field)
			return "Invalid Username.\n"
		return ""
	}
	function validateEmail(field){
		if(field =="") return "No Email was entered. \n"
		else if(!((field.indexOf(".")>0) && (field.indexOf("@") > 0)) ||
			/[^a-zA-Z0-9.@_-]/.test(field))
			return "Invalid Email.\n"
		return ""
	}
	function validatePassword(field){
		if (field =="") return "No Password was entered. \n"
		else if(field.length <6) return "Password must be 6 characters or longer.\n"
		else if(!/[a-z]/.test(field) || ! /[A-Z]/.test(field) || !/[0-9]/.test(field)
			return "Password must have at least 1 uppercase, 1 lowercase letter, and 1 number"
		return ""
	}
	</script>
</head>
	<body>
	<table border = "0" cellpadding="2" cellspacing="5" bgcolor="#eeeeee
		<th colspan ="2" align="center">Signup Form</th>
		<tr><td colspan = "2">Sorry, the following errors were found<br>
		in your form: <p><font color=black size=1><i>$fail</i></font></p>
		</td></tr>
	<form method="post" action ="register.php" onSubmit="return validate(this)">
	<tr><td>Forename</td>
		<td><input type="text" maxlength="32" name="forename" value="$forename">
	</td></tr><tr><td>Surname</td>
		<td><input type="text" maxlength="32" name="surname" value="$surname">
	</td></tr><tr><td>Username</td>
		<td><input type="text" maxlength="16" name="username" value="$username">
	</td></tr><tr><td>email</td>
		<td><input type="text" maxlength="64" name="email" value="$email">
	</td></tr><tr><td>password</td>
		<td><input type="text" maxlength="16" name="password" value="$password">
	</td></tr><tr><td colspan="2" align="center"><input type ="submit"
		value="Signup"></td></tr>
	
	</form>
	</table>
	</body>
	</html>
_END;

function validate_forename($field){
	return ($field == "") ? "No Forename was entered<br>": "";
}
function validate_surname($field){
	return ($field == "") ? "No surname was entered<br>": "";
}
function validate_username($field){
	if($field == "") return "No username was entered<br>";
	else if(preg_match("/[^a-zA-Z0-9_-]/",$field)){
		return "Usernames can only contain letters, numbers, - and _<br>";
	}
	return "";
}

function validate_password($field){
	if($field == "") return "No Password was entered<br>";
	else if(strlen($field) < 6)
		return "Password must be 6 characters or longer";
	else if(!preg_match("/[a-zA-Z0-9]/",$field)){
		return "Password must contain at least 1 uppercase, 1 lowercase letter, and 1 number";
	}
	return "";
}

function validate_email($field){
	if($field == "") return "No Email was entered<br>";
	else if(!((strpos($field,"." )>0) && (strpos($field,"@")>0)) || 
		preg_match("/[^a-zA-Z0-9.@_-]/",$field))
		return "Invalid Email<br>";
	return "";
}
function fix_string($string){
	if(get_magic_quotes_gpc()) $string = stripslashes($string);
	return htmlentities($string);
}

function mysql_fix_string($conn, $string) {
	if (get_magic_quotes_gpc()) $string = stripslashes($string);
	return $conn->real_escape_string($string);
}
function add_user($connection, $fn, $sn, $un,$email, $pw){
	$query = "INSERT INTO users VALUES('$fn','$sn','$un','$email','$pw')";
	$result = $connection->query($query);
	if(!$result)die($connection->error);
} 
?>