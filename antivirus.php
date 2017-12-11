<?php
	require_once 'login.php';
	require_once 'antivirus.html';
	$virus_table = "viruses";
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	
	if($_FILES){
		$handler = fopen($_FILES['filename']['tmp_name'], "r");		
		$contents = fread($handler, filesize($_FILES['filename']['tmp_name']));
		//$signature = md5_file($_FILES['filename']['tmp_name']);	
		$contents = str_replace(array("\r", "\n"), '', $contents);

		addVirus($conn,"01234567890123456789");
		$virus_signatures = getVirusSignatures($conn);
		print_r($virus_signatures);
		print("<div style ='font:20px Monospace;text-decoration:underline;'>".file_get_contents($_FILES['filename']['tmp_name'], NULL, NULL, 0, 20)."<br>");
		
		$status = $_POST['infected'];
		switch($status){
			case 'possible':
				echo "Possibly infected<br>";
				break;
			case 'virus':
				echo "Surely a virus<br>";
				$adminHTML = "";
				break;
			default:
				echo "Something bad happened.";
		}
		
		for($i = 0; $i < strlen($contents) - 15; $i++){
			$content20 = file_get_contents($_FILES['filename']['tmp_name'], NULL, NULL, $i, 20);
			echo "'".$content20."'<br>";
		}
		
	}

	//TODO: Sanitize
	function addVirus($conn, $virus){
		$virus = mysql_entities_fix_string($conn, $virus);
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

	function login(){
		$html = <<< _END
		<form action="antivirus.php">
			<div class = "container">
				<lable>Username</label>
				<input type="text" placeholder="Enter Username" name="un required>
				<lable>Password</label>
				<input type="password" placeholder="Enter Password" name="pw" required>
				<button type="submit">Sign in</button>
			</div>
_END;
	}

	function mysql_entities_fix_string($conn, $str){
		return htmlentities(mysql_fix_string($conn, $str));
	}

	function mysql_fix_string($conn, $str){
		if(get_magic_quotes_gpc())
			$str = stripslashes($str);
		return $conn->real_escape_string($str);
	}
?>
