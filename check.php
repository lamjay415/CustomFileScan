<?php
echo <<<_END
<html>
<head>
<title> FILE UPLOAD 
</title>
</head>
<body>
<form method = 'post' action= 'check.php' enctype = 'multipart/form-data'>
Select file to check: <input type = 'file' name = 'filename' size = '10'>
<input type = 'submit' value = 'Upload'>

</form>
_END;

if($_FILES){
	$name = $_FILES['filename']['name'];
	$name = fix_string($name);
	move_uploaded_file($_FILES['filename']['tmp_name'],$name);
	switch($_FILES['filename']['type']){
		case '': echo "Please submit a file<br>"; break;
		default: check_db($name); break;
	}
}

//loop through infected database for signature of input file
//if signature is found then file is infected and return malware name set by admin
function check_db($file){
	$conn = new mysqli("localhost","root","abc123");
	if ($conn->connect_error) die($conn->connect_error);
	mysqli_select_db($conn,"project");
	$sign = get_signature($file,filesize($file));
	$query = "SELECT * FROM infected";
	$result = $conn->query($query);
	while($row = mysqli_fetch_assoc($result)){
		$temp = $row["signature"];
        if(strpos($sign,$temp) !== false){
			echo "File is infected by $row[name]!";
			break;
		}else{
			echo "File does not contain any malware";
		}
    }
}

//reads the file and and convert ASCII values into hex to get the first desired signature values
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
//sanitizing input
function fix_string($string){
	if(get_magic_quotes_gpc()) $string = stripslashes($string);
	return htmlentities($string);
}
?>
