<?php
	require_once 'login.php';
	require_once 'antivirus.html';
	$virus_table = "viruses";
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	
	if($_FILES){
		$handler = fopen($_FILES['filename']['tmp_name'], "r");		
		$contents = fread($handler, filesize($_FILES['filename']['tmp_name']));
		$contents = str_replace(array("\r", "\n"), '', $contents);

		print("<div style ='font:20px Monospace;text-decoration:underline;'>");
		
		$status = $_POST['infected'];
		switch($status){
			case 'possible':
				echo "Possibly infected<br>";
				scanForVirus($conn, $_FILES['filename']['tmp_name']);
				break;
			case 'virus':
				echo "Verify Login...<br>".validate_admin();
				break;
			default:
				echo "Something bad happened.";
		}

	}

	function addVirus($conn, $virus){
		$virus = fix_string($conn, $virus);
		echo $virus."<br>";
		global $virus_table;
		$query = "INSERT INTO $virus_table VALUES('$virus')";
		$result = $conn->query($query);
		if (!$result) return ("Database access failed: " . $conn->error);
		$result->close();
		return "";
	}

	function getVirusSignatures($conn){
		global $virus_table;
		$query = "SELECT * FROM $virus_table";
		$result = $conn->query($query);
		if (!$result) return ("Database access failed: " . $conn->error);
		$rows = $result->num_rows;
		$signatures = array();
		for($i = 0; $i < $rows; $i++){
			$result->data_seek($i);
			$signature = ($result->fetch_array(MYSQLI_NUM))[0];
			array_push($signatures, $signature);
		}
		$result->close();
		return $signatures;
	}

	function fix_string($str){
        if(get_magic_quotes_gpc())
            $str = stripcslashes($str);
        return htmlentities($str);
	}

	function scanForVirus($conn, $file){
		$virus_signatures = getVirusSignatures($conn);
		foreach($virus_signatures as $virus){
			for($i = 0; $i < filesize($_FILES['filename']['tmp_name']) - 19; $i++){
				$content20 = file_get_contents($file, NULL, NULL, $i, 20);
				if($content20 == $virus){
					echo "Virus --> ";
					echo "'".$virus."' == ";
					echo "'".$content20."'<br>";
				}
			}
		}
	}

	function validate_admin(){
		echo "in validate admin";

		$username = $password = "";
		if(isset($_POST['username']))
			$username = fix_string($_POST["username"]);
		if(isset($_POST['password']))
			$password = fix_string($_POST["password"]);
		echo "<br>Username : '$username'";
		echo "<br>Password : '$password'<br>";
		
		// if(validate_password($password) && validate_username($username)){
		// 	return true;
		// } else {
		// 	return "Invalid username/password";
		// }
	}
?>
