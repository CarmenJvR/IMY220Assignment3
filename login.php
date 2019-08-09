<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// Your database details might be different
	$mysqli = mysqli_connect("localhost", "root", "", "dbUser");

	if(isset($_POST["loginEmail"]) && isset($_POST["loginPassw"]))
	{
		$email = $_POST["loginEmail"] ;
		$pass = $_POST["loginPassw"] ;	
		$attempt = false;
	}
	else
	{
		$email = false ;
		$pass = false ;	
		$attempt = false;

		if (isset($_POST["currUserPass"]))
		{
			$attempt = true ;
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
	<meta name="author" content="Carmen Janse van Rensburg">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if ($attempt)
			{
				$target_dir = "gallery/";
				$uploadFile = $_FILES["picToUpload"];
				
            if(( $uploadFile["type"] == "image/jpeg" || $uploadFile["type"] == "image/jpg"  && $uploadFile["size"] < 1073741824)){
            	if($uploadFile["error"] > 0){
            		echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							Error: '.$uploadFile["error"].' 
	  						 </div>';
					echo "Error: " . $uploadFile["error"] . "<br/>";
					} else { //upload
						$target_file = $target_dir . basename($uploadFile["name"]);

							if(move_uploaded_file($uploadFile["tmp_name"], $target_file))
							{
								$id = $_POST['currUserID'];
								$f =  $uploadFile["name"] ;
								$query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$id' , '$f');";

								$res = mysqli_query($mysqli, $query) == TRUE;

								if($res)
								{
									echo 	'<div class="alert alert-info mt-3" role="alert">
			  							The file '. basename($uploadFile["name"]) .' has been uploaded 
			  						 </div>';
								}
								else
								{
									echo 	'<div class="alert alert-danger mt-3" role="alert">
	  									There was an error uploading your file to the database
	  									</div>';
								}
								
							} else {
								
								echo 	'<div class="alert alert-danger mt-3" role="alert">
	  									There was an error uploading your file
	  									</div>';
							}


					}
            }
            else
            {
            	echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							Invalid file!
	  						</div>';
            }



			}
			else if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$userID = $row['user_id'];
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
				
					echo 	"<form enctype='multipart/form-data' action='#' method='POST'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='hidden' name='currUserPass' value='". $pass  ."'/>
									<input type='hidden' name='currUserEmail' value='". $email ."'/>
									<input type='hidden' name='currUserID' value='". $userID ."'/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";

					echo "<h1>Image Gallery</h1>
							<div class='row imageGallery'>";

					$Q = "SELECT * FROM tbgallery WHERE user_id = '$userID' ";
					$result = $mysqli->query($Q);
						if (mysqli_num_rows($result) > 0) {
							// output data of each row

							while($row = mysqli_fetch_assoc($result)) {

							   echo "<div class='col-3' style='background-image: url(gallery/". $row['filename'] . ")'> </div>" ;
								
							}
					}


					echo	"</div>";
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