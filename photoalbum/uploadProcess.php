<?php
	ini_set('display_errors', 1);
	require dirname(__FILE__).'/../aws/aws-autoloader.php';
	
	use Aws\S3\MultipartUploader;
	use Aws\Common\Exception\S3Exception;											//including the AwsSDK library files
	use Aws\S3\S3Client;
	
	$s3_client = new S3Client(['version' => 'latest', 'region' => 'us-east-1']); 	//creating an S3 Client
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Assignment 2 Cloud Architecture" />
	<meta name="keywords" content="assingment" />
	<meta name="author" content="Apostolos Lafazanis" />
	<title>Photo Upload Process Webpage</title>
</head>
<body>
	<h1>Photo Uploader Result Page</h1>
	<?php
					
		$target_dir = './';
		$target_file = $target_dir . basename($_FILES["image"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$photoTitle = $_POST["title"];
		$photoDescr = $_POST["descr"];
		$photoDate = $_POST["uploadDate"];
		$userKeywords = $_POST["keywords"];
		$keyword = explode(";", strtolower($userKeywords)); 
		
		$host = "assignemetn2-db.ce0yewdqjjmg.us-east-1.rds.amazonaws.com";
		$user = "admin"; 
		$pswd = "04071996"; 														//Connetion details
		$dbnm = "assignment2";
	
		$conn = @mysqli_connect($host, $user, $pswd, $dbnm)
				or die('Unable to connect to the database');

		
																					// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["image"]["tmp_name"]);
			if($check !== false) {
				echo nl2br("File is an image - " . $check["mime"] . ".\n");
				$uploadOk = 1;
			} else {
				echo nl2br("File is not an image.\n");
				$uploadOk = 0;
			}
		}
		
																					// Check file size
		if ($_FILES['image']['size'] > 2000000) {
			echo nl2br("Sorry, your file is too large.\n");
			echo filesize($target_file) . ' bytes';
			$uploadOk = 0;
		}
		
																					// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			echo nl2br("Sorry, only JPG, JPEG, PNG & GIF files are allowed.\n");
			$uploadOk = 0;
		}
		
																					// Make sure the user filled all the details
		if(empty($_POST["title"]) || empty($_POST["descr"]) || empty($_POST["uploadDate"]) && empty($_POST["keywords"]))
		{
			echo nl2br("Some of the details of the image were missing. Please fill in all the fields.\n");
			$uploadOk = 0;
		}
		
		
		
																					// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo nl2br("Sorry, your image was not uploaded due to the above error(s).\n");
																					// if everything is ok, try to upload file
		} else 
		{
																					//upload the file to your EC2 instance
			if (move_uploaded_file($_FILES["image"]["tmp_name"], dirname(__FILE__). '/uploads/' . basename($_FILES["image"]["name"]))) {
				
				echo nl2br("The file ". basename( $_FILES["image"]["name"]). " has been uploaded.\n");
				
				$bucket = 'assignmetn2-bucket';
				
				$uploader = new MultipartUploader($s3_client, dirname(__FILE__). '/uploads/' . basename($_FILES["image"]["name"]), [
				'bucket' => $bucket, 
				'key' => $target_file												//creating an uploader
				]);
				
				try {
					$result = $uploader->upload();									//uploading the image in the bucket
				} catch (S3Exception $e) {}
				
				$reference = "http://d34qipa2sduqlz.cloudfront.net/" . basename($_FILES["image"]["name"]);
							

					
				$insertPhotoQuery = "INSERT INTO photos(title, description, dateOfPhoto, reference) 
								VALUES(\"" . $photoTitle . "\", \"" . $photoDescr . "\", \"" . $photoDate . "\", \"" . $reference . "\")"; 
								
				
				$insertsResult = @mysqli_query($conn, $insertPhotoQuery)
								or die('Couldnt insert the photo details to the database');			//storing the meta data details of the photo into the database

				
				$getPhotoIdQuery = "SELECT photoID FROM photos WHERE title = \"" . $photoTitle . "\"";
				
				$getPhotoIdResult = @mysqli_query($conn, $getPhotoIdQuery)
								or die('Couldnt get the photos ID from the database');
								
				while ($photo = mysqli_fetch_row($getPhotoIdResult))
				{
					for($i = 0; $i < count($keyword); $i++)
					{
						$insertKeywordQuery = "INSERT INTO keywords(photoID, keyword) 
										VALUES(\"" . $photo[0] . "\", \"" . $keyword[$i] . "\")"; 	//storing the keywords of the photo into the database
						
						$insertKeywordResult = @mysqli_query($conn, $insertKeywordQuery)
								or die('Couldnt insert the keyword into the database');
					}	
				}
			}  
			else
			{
				echo nl2br("Sorry, there was an internal error uploading your file.\n");  			//Displaying an error message if an internal error occurs
			}	
		}
	?>
	<br><a href="upload.php">Go back to the upload page</a>
	<br><a href="getphotos.php">Go to the Search Photos page</a><br>
</body>
</html>