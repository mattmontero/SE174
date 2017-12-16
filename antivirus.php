<?php
	require_once 'login.php';
	require_once 'antivirus.html';
	$virus_table = "viruses";
	$admins_table = "users";

	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);

	echo "--Add virus test--<br>";
	echo addVirus($conn,"20202020202020202021");
	echo "<br>--Add virus test--<br>";
	
	if($_FILES){
		//$handler = fopen($_FILES['filename']['tmp_name'], "r");		
		//$contents = fread($handler, filesize($_FILES['filename']['tmp_name']));
		//$contents = str_replace(array("\r", "\n"), '', $contents);
		
		//print("<div style ='font:20px Monospace; text-decoration:underline;'>");
		
		$status = $_POST['infected'];
		switch($status){
			case 'possible':
				if(scanForVirus($conn, $_FILES['filename']['tmp_name'])){
					echo "<b>!!ATTENTION!!:</b> Virus Found!<br>";
				} else {
					echo "Super clean file<br>";
				}
				break;
			case 'virus':
				if(validate_admin($conn)){
					echo "<br>Logged in!<br>";
					echo addVirus($conn, getSignature($_FILES['filename']['tmp_name']))."<br>";
				} else {
					echo "Invalid Username/Password";
				}
				break;
			default:
				echo "Something bad happened.";
		}
	}

	function addVirus($conn, $virus){
		$virus = fix_string($virus);
		global $virus_table;
		$query = "INSERT INTO $virus_table VALUES('$virus', 'title')";
		$result = $conn->query($query);
		if (!$result) return ("Insert failed: " . $conn->error);
		return true;
	}

	function getSignature($file){
		return file_get_contents($file, NULL, NULL, 0, 20);
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
					return true;
				}
			}
		}
		return false;
	}

	function validate_admin($conn){
		global $admins_table;

		$username = $password = "";
		if(isset($_POST['username']))
			$username = fix_string($_POST["username"]);
		if(isset($_POST['password']))
			$password = fix_string($_POST["password"]);
		$password = md5($password);
		
		$query = "SELECT * FROM $admins_table WHERE username='$username'";
		$result = $conn->query($query);
		if (!$result) return ("Database access failed: " . $conn->error);

		$result->data_seek(0);
		$verified = false;
		if ($result->num_rows == 1){
			$cmpPassword = ($result->fetch_array(MYSQLI_NUM))[1];
			if($password == $cmpPassword)
				$verified = true;
		} else {
			echo "invalid un/pw";
		}
		$result->close();
		return $verified;
	}
?>
