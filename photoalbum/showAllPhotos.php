 <!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Assignment 1b Cloud Architecture" />
	<meta name="keywords" content="assignment cloud" />
	<meta name="author" content="Apostolos Lafazanis" />
	<!--<link rel="stylesheet" 	 href="style.css" />			  Linking CSS stylesheet-->
	<title>Photo Album</title>
</head>
<body>
	<h1>Photo Album</h1>
	<h3>Here are all the photos in the database:</h3>
	<?php
	$host = "assignemetn2-db.ce0yewdqjjmg.us-east-1.rds.amazonaws.com";
	$user = "admin"; 
	$pswd = "04071996"; 									//Connetion details
	$dbnm = "assignment2";
	
	$conn = @mysqli_connect($host, $user, $pswd, $dbnm)
				or die('Unable to connect to the database');
				
	
	$photosQuery = "SELECT * FROM photos";
	
	$photosResult = @mysqli_query($conn, $photosQuery)		//Getting the details from the photos stored in the database
					or die('Couldnt get the photots');
					
	while ($row = mysqli_fetch_row($photosResult))
	{ 
		$photoID = $row[0];
		$title = $row[1];
		$description = $row[2];
		$date = $row[3];
		$link = $row[4];
		
		echo nl2br("Title: " . $title . "\n");
		echo nl2br("Description: " . $description . "\n");	//Display each of the photos details
		echo nl2br("Date uploaded: " . $date . "\n");
		echo nl2br("Associated kewords: ");
		
		
		$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $row[0] ."";
						
		$keywordsResult = @mysqli_query($conn, $keywordsQuery)			//Getting the keyword of the photos in the database
							or die('Couldnt get the keywords ');
					
		$assocKeywordArr = array();
				
		while ($assocKeyword = mysqli_fetch_row($keywordsResult))
		{
			$assocKeywordArr[] = $assocKeyword[2]; 						// Here I store the keywords of in an array so I can display them later
		}
		
		for($x = 0; $x < count($assocKeywordArr); $x++)
		{
			if($x != count($assocKeywordArr) - 1)						//Checking to see if it's the last keyword of the photo. If it is
			{															// print a dot instead of a comma between the keywords
				echo"" . $assocKeywordArr[$x]. ", ";
			}	
			else
			{
				echo"" . $assocKeywordArr[$x]. ".</li>";
			}	
		}
		echo nl2br("\n");	
		echo "<img id=\"image" . $photoID . "\" src=\"" . $link . "\" alt=\"" . $title . "\" />";	// Display the image 
		echo nl2br("\n\n");		
	}
	?>
	<a href="upload.php">Upload Photo Page</a><br>
	<a href="getphotos.php">Search Page</a>
</body>
</html>