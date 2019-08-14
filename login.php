<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// Your database details might be different
	$mysqli = mysqli_connect("localhost", "root", "", "dbUser");

	$email = isset($_POST["loginName"]) ? $_POST["loginName"] : false;
	$pass = isset($_POST["loginPassw"]) ? $_POST["loginPassw"] : false;
	$uid = isset($_POST["loginUid"]) ? $_POST["loginUid"] : false;
	$uploadError = false;
	$uploadFile = false;

	if (isset($_FILES["picToUpload"])){
		$uploadFile = $_FILES["picToUpload"];
	}

	if ($uploadFile != false) {
		$target_dir = "gallery/";
		
		$target_file = $target_dir . basename($uploadFile["name"]);

		if (($uploadFile["type"] == "image/jpeg" 
		|| $uploadFile["type"] == "image/jpg") && $uploadFile["size"] < 1000000) {
			if ($uploadFile["error"] > 0){
				//fail
				$uploadError = $uploadFile["error"];
			} else {
				//success
				if(move_uploaded_file($uploadFile["tmp_name"], $target_file)){
					$nn = $uploadFile["name"];
					$query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$uid', '$nn');";
					$res = mysqli_query($mysqli, $query);
				} else {
					$uploadError = "Sorry, there was a problem uploading";
				}
			}
		} else {
			$uploadError = "Filetype or size is incorrect";
		}

	} 
	
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 3</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Luke Greenberg">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
		
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$uid = $row['user_id'];
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form enctype='multipart/form-data' action='login.php' method ='POST'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<button type='submit' class='btn btn-standard' value='Upload Image' name='submit'>Upload Image </button>
									<input type='email' id='loginEmail' class='invisible form-control' value='".$email."' name='loginName'>
									<input type='password' id='loginPass' class='invisible form-control' value='".$pass."' name='loginPassw'>
									<input type='text' id='loginUid' class='invisible form-control' value='".$uid."' name='loginUid'>
								</div>
							  </form>";
					if ($uploadError != false){
						echo "THERE WAS AN ERROR UPLOADING: " . $uploadError;
					}
					echo "<h1>Image Gallery</h1>";

					$query = "SELECT * FROM tbgallery WHERE user_id = '$uid'";
					$res = $mysqli->query($query);
					echo "<div class='row imageGallery'>";
					
					if ($res->num_rows > 0){
						while ($row = $res->fetch_assoc()){
							$nFileName = $row["filename"];

							echo "<div class='col-3' style='background-image: url(gallery/$nFileName)'></div>";

						}
					}
					echo "</div>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
						  </div>';
						  
			}
		?>
	</div>
</body>
</html>