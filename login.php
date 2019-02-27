<?php
	session_start();
	if($_SERVER["REQUEST_METHOD"] == "POST") {
      	$conn = new mysqli("localhost","root","abc123");
		if ($conn->connect_error) die($conn->connect_error);
		$username = mysql_fix_string($conn,$_POST['username']);
		$password = mysql_fix_string($conn,$_POST['password']); 
		mysqli_select_db($conn,"project");
		$salt1 = "p@!d";
		$salt2 = "j&**z";
		$token = hash('ripemd128',"$salt1$password$salt2");
		$query = "SELECT username FROM users WHERE username = '$username' AND password = '$token'";
		$result = mysqli_query($conn,$query);
		$count = mysqli_num_rows($result);
		//If query returned a result then login is successful and count should be 1 
		if(isset($_SESSION['username'])){
				$username = $_SESSION['username'];
				destroy_session();
		}
		if($count == 1) {
			if(isset($_SESSION['username'])){
				$username = $_SESSION['username'];
				destroy_session();
			}
			//if user is admin, go to upload page, else go to check page
			if($username == "admin"){	//admin account is (admin, password)
				header("location: upload.php");
			}else{
				header("location: check.php");
			}
		}else {
			echo "Invalid username or password<br>";
		}
		
   }
    echo "If you do not have an account, Please <a href='register.php'>click here</a> to sign up.";
	
    function mysql_fix_string($conn, $string) {
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return $conn->real_escape_string($string);
	}
	function destroy_session(){
		session_start();
		$_SESSION = array();
		setcookie(session_name(), '', time() - 2592000, '/');
		session_destroy();
	}
echo <<<_END
<html>
	<head>
		  <title>Login Page</title>
	   </head>
	   
			<form action = "" method = "post">
				<label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
				<label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
				<input type = "submit" value = " Submit "/><br />
			</form>
<html>	
_END;
?>
