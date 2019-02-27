<?php
$virus = "";
echo <<<_END
<html>
<head>
<title> FILE UPLOAD 
</title>
</head>
<body>
<form method = 'post' action= 'upload.php' enctype = 'multipart/form-data'>
Select file to add to database: <input type = 'file' name = 'filename' size = '10'>
<label>Virus Name  :</label><input type = "text" name = "virus" class = "box"/><br /><br />
<input type = 'submit' value = 'Upload'>
</form>
_END;
echo "<a href='check.php'>click here</a> to check your file.<br>";

if($_FILES && isset($_POST['virus'])){
	$name = $_FILES['filename']['name'];
	$name = fix_string($name);
	$virus = fix_string($_POST['virus']);
	move_uploaded_file($_FILES['filename']['tmp_name'],$name);
	if($_POST['virus'] == '' || $_FILES['filename']['type'] == ''){
		echo "Please submit a file and a name for your virus";
	}else{
		upload_to_db($name,$virus);
	}
}

//gets the first 20 values of the files signature and store it into the database
function upload_to_db($file,$virus){
	$bytes = 20;
	$conn = new mysqli("localhost","root","abc123");
	if ($conn->connect_error) die($conn->connect_error);
	mysqli_select_db($conn,"project");
	$query = "CREATE TABLE IF NOT EXISTS infected(name VARCHAR(32) NOT NULL, signature VARCHAR(32) NOT NULL UNIQUE)";
	$result = $conn->query($query);
	if (!$result) die($conn->error);
	$sign = get_signature($file,$bytes);
	$query = "INSERT INTO infected VALUES('$virus','$sign')";
	$result = $conn->query($query);
	echo "File successfully added to database<br>";
}

//reads the file and and convert ASCII values into hex to get the first stated($size) signature values
function get_signature($file,$size){
	$signature = "";
	if($handle = fopen($file,'r')){
		$contents = fread($handle,$size);
		for($i = 0; $i < $size;$i++){
			$dec = ord($contents[$i]);
			$hex = base_convert($dec,10,16);
			$signature .= $hex;
		}
		return $signature;
	}
}

function fix_string($string){
	if(get_magic_quotes_gpc()) $string = stripslashes($string);
	return htmlentities($string);
}
?>
