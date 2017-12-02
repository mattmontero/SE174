<?php
	require_once 'login.php';
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
		$signature = md5_file($_FILES['filename']['tmp_name']);	
		$contents = str_replace(array("\r", "\n"), '', $contents);
		
		
		print("*".$contents."*<br>");
		print("<div style ='font:20px Monospace;text-decoration:underline;'>".file_get_contents($_FILES['filename']['tmp_name'], NULL, NULL, 0, 20)."<br>");
		
		$status = $_POST['infected'];
		switch($status){
			case 'possible':
				echo "Possibly infected<br>";
				break;
			case 'virus':
				echo "Surely a virus<br>";
				break;
			default:
				echo "Something bad happened.";
		}
		
		for($i = 0; $i < strlen($contents) - 15; $i++){
			$content20 = file_get_contents($_FILES['filename']['tmp_name'], NULL, NULL, $i, 20);
			echo "'".$content20."'<br>";
		}
		
	}
?>
