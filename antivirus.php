<?php
	session_start();
	require_once 'login.php';
	require_once 'antivirus.html';
	$virus_table = "viruses";
	$admins_table = "users";

	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);
	
	if($_FILES){		
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
					echo addVirus($conn, getSignature($_FILES['filename']['tmp_name']), $_POST['vname']);
				} else {
					echo "Invalid Username/Password";
				}
				break;
		}
	}
	session_destroy();

	/**
	 * Attemps to add a virus signature to the virus database.
	 * @return string Insert failure, or Insert confirmation
	 */
	function addVirus($conn, $virus, $virus_name){
		$virus = fix_string($virus);
		$virus_name = fix_string($virus_name);
		global $virus_table;
		$query = "INSERT INTO $virus_table VALUES('$virus', '$virus_name')";
		$result = $conn->query($query);
		if (!$result) return ("Insert failed: " . $conn->error);
		return "Virus added to database";
	}

	/**
	 * @return string first 20 bytes of file
	 */
	function getSignature($file){
		return file_get_contents($file, NULL, NULL, 0, 20);
	}

	/**
	 * @return array{string} all viruses in db.
	 */
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

	/**
	 * sanitize strings
	 */
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

	/**
	 * Validates admin un/pw
	 */
	function validate_admin($conn){
		global $admins_table;

		$username = $password = "";
		if(isset($_POST['username']))
			$username = fix_string($_POST["username"]);
		if(isset($_POST['password']))
			$password = fix_string($_POST["password"]);
		$salt_1 = "@&0!*";
		$salt_2 = "mp40%";
		$token = hash("ripemd128","$salt_1$password$salt_2");
		
		$query = "SELECT * FROM $admins_table WHERE username='$username'";
		$result = $conn->query($query);
		if (!$result) return ("Database access failed: " . $conn->error);

		$result->data_seek(0);
		$verified = false;
		if ($result->num_rows){
			$row = $result->fetch_array(MYSQLI_NUM);
			$result->close();

			if($token == $row[1]){
				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;
				$verified = true;
			}
		}
		return $verified;
	}
?>
