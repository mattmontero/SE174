<?php
	require_once 'login.php';
	$virus_table = "viruses";
	$conn = new mysqli($hn, $un, $pw, $db);
	if($conn->connect_error) die($conn->connect_error);

	
	echo <<< _END
		<form action="antivirus.php" method='post' 
		action='assignment4.php' enctype='multipart/form-data'>
			<input type="radio" name="infected" value="possible" onClick="r=true;enableSubmit();"> Possibly Infected<br>
			<input type="radio" name="infected" value="virus" onClick="r=true;enableSubmit();"> Surely Infected<br><br>
			Select File: <input type='file' name='filename' onchange="f=true;enableSubmit();" size='10'>
			<input type='submit' id="sb" disabled="disabled" value='Upload'>
		</form>
		<script type="text/javascript">
		r=false;
		f=false;
		function enableSubmit(){
			if(r && f){
				(document.getElementById("sb")).disabled="";
			}
		}
		</script>
_END;
	
	if($_FILES){
		$handler = fopen($_FILES['filename']['tmp_name'], "r");		
		$contents = fread($handler, filesize($_FILES['filename']['tmp_name']));
		//$signature = md5_file($_FILES['filename']['tmp_name']);	
		$contents = str_replace(array("\r", "\n"), '', $contents);
		
		addVirus($conn,"01234567890123456789");
		$virus_signatures = getVirusSignatures($conn);
		print_r($virus_signatures);
		//print("*".$contents."*<br>");
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
			

_END
	}

	//TODO: Sanitize
	function addVirus($conn, $virus){
		$virus = mysql_fix_string($conn, $virus);
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

	function mysql_fix_string($conn, $str){
		if(get_magic_quotes_gpc())
			$str = stripslashes($str);
		return $conn->real_escape_string($str);
	}
?>
