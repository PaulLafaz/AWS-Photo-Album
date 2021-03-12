<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Assignment 1b Cloud Architecture" />
	<meta name="keywords" content="assignment cloud" />
	<meta name="author" content="Apostolos Lafazanis" />
	<title>Result Page</title>
</head>
<body>
<h1>Photo Searching Result Pag</h3>
<?php
/*
	At first glace this code seems to be enormously big but for the most part a lot of the code is repeated.
	This is because I didnt create my own php function so I had to reuse the same chucks of code instead of putting it in a function
	and calling that function when I needed to. Sorry for any inconvenience :/
*/

	$host = "assignemetn2-db.ce0yewdqjjmg.us-east-1.rds.amazonaws.com";
	$user = "admin"; 
	$pswd = "04071996"; 					//Connetion details
	$dbnm = "assignment2";
	
	$conn = @mysqli_connect($host, $user, $pswd, $dbnm)
				or die('Unable to connect to the database');
	
	echo"<p>Your searching critiria were:</p>";
	echo"<ul>";
	
	$userTitle = $_POST["title"];
	$userDate = $_POST["photoDate"];
	$userKeywords = $_POST["keywords"];
	$userTimeFrame = $_POST["timeFrame"];
	$keyword = explode(";", $userKeywords);
	$titleExists = false;
	$dateExists = false;
	$frameExists = false;
	$keywordsExists = false;
	
	
	if(!empty($_POST["title"]))
	{
		echo"<li>Title = " . $_POST["title"] . "</li>";
		$titleExists = true;
	}		
		
	if(!empty($_POST["photoDate"]))									// Checking the user's input and displaying the ones that they inserted
	{
		echo"<li>Date = " . $_POST["photoDate"] . "</li>";
		$dateExists = true;
		
		if(!empty($_POST["timeFrame"]))
		{
			echo"<li>Time Frame = " . $_POST["timeFrame"] . " (" . $_POST["photoDate"] . ")</li>";
		}
	}	

	if(!empty($_POST["keywords"]))
	{
		echo"<li>Keywords = ";
		$keywordsExists = true;
		
		for($i = 0; $i < count($keyword); $i++)
		{	
			if($i != count($keyword) - 1)
			{
				echo"" . $keyword[$i]. ", ";
			}
			else
			{
				echo"" . $keyword[$i]. ".</li>";
			}
		}			
	}	
	echo"</ul>";
	echo"<h3>Here are your search results:</h3>";
	
	
	if($titleExists == true)
	{
		if($dateExists == true)
		{
			if($keywordsExists == true)
			{
				if($keywordsExists == true)			//If all 3 of the field where filled out then
				{
					$keyword = explode(";", strtolower($userKeywords));
					$keywordPhotoIds = array();
					$datePhotoIds = array();
		
					$keywordsQuery = "SELECT * FROM keywords";
						
					$keywordsResult = @mysqli_query($conn, $keywordsQuery)
										or die('Couldnt get the keywords A');
							
					while ($row = mysqli_fetch_row($keywordsResult))		
					{
						for($i = 0; $i < count($keyword); $i++)
						{
							if(strtolower($row[2]) == $keyword[$i])					//Store all the keywords from the database that are related
							{											// to the ones the user inserted 
								$keywordPhotoIds[] = $row[1];
							}
						}	
					}

					$uniquePhotoIds = array_unique($keywordPhotoIds);
					$matchArr = array();
			
			
					if(!empty($uniquePhotoIds)) // If the array is not empty that means some of the keywords matched
					{
						foreach($uniquePhotoIds as $unique)
						{
							$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $unique . "\"";
				
							$photosResult = @mysqli_query($conn, $photosQuery)
											or die('Couldnt get the keywords B');
			
							while ($photosRecord = mysqli_fetch_row($photosResult))
							{
								if(!empty($userTimeFrame))
								{
									if($userTimeFrame == "Before")						// Now that we have all the keyword matches we look at them to see if they also match with  
									{																			// the title and the date
										if(($photosRecord[3] < $userDate) && ($photosRecord[1] == strtolower($userTitle))) 
										{												//if the title matches and the user choose to see date that were smaller than the one they put
											$matchArr[] =  $photosRecord[0];
										}
									}
									else if($userTimeFrame == "After" && ($photosRecord[1] == strtolower($userTitle))) //the same but bigger
									{
										if($photosRecord[3] > $userDate)
										{
											$matchArr[] =  $photosRecord[0];
										}
									}
									else
									{
										if($photosRecord[3] == $userDate && ($photosRecord[1] == strtolower($userTitle))) //the same but just on that day they provided
										{
											$matchArr[] =  $photosRecord[0];
										}
									}	
								}
								else
								{
									if(photosRecord[3] == $userDate && ($photosRecord[1] == strtolower($userTitle))) //If the user inserts a date but doesnt pick a time frame 
									{																				// the code will assume that is wants to display just the photos 
										$matchArr[] =  $photosRecord[0];												// uploaded on that date
									}
								}
							}
						}
				
						if(!empty($matchArr)) 				// this is the arrray with all the final matches. All we need to do now is to display them. This code exist in every main if 
						{
							echo "<p>Number of result: " . count($matchArr) . "</p>";
							
							for($y = 0; $y < count($matchArr); $y++)
							{
								$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $matchArr[$y] . "\"";
		
								$photosResult = @mysqli_query($conn, $photosQuery)
												or die('Couldnt get the photots');
							
								while ($match = mysqli_fetch_row($photosResult))
								{ 
									echo nl2br("Title: " . $match[1] . "\n");
									echo nl2br("Description: " . $match[2] . "\n");
									echo nl2br("Date uploaded: " . $match[3] . "\n");
									echo nl2br("Associated kewords: ");
							
									$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $matchArr[$y] ."";
						
									$keywordsResult = @mysqli_query($conn, $keywordsQuery)
														or die('Couldnt get the keywords ');
					
									$assocKeywordArr = array();
				
									while ($assocKeyword = mysqli_fetch_row($keywordsResult))
									{
										$assocKeywordArr[] = $assocKeyword[2];
									}
							
									for($x = 0; $x < count($assocKeywordArr); $x++)
									{
										if($x != count($assocKeywordArr) - 1)
										{
											echo"" . $assocKeywordArr[$x]. ", ";
										}	
										else
										{
											echo"" . $assocKeywordArr[$x]. ".</li>";
										}	
									}
									echo nl2br("\n");
									echo "<img id=\"image" . $match[0] . "\" src=\"" . $match[4] . "\" alt=\"" . $match[1] . "\" />";	
									echo nl2br("\n\n");
								}
							}
						}
						else
						{
							echo "<p>I'm sorry but there were no results with those 3 details you provided: Please try again.</p>"; // if the it finds no kewords matched it will display this message
						}
					}
					else
					{
						echo "<p>I'm sorry but there were no results with those 3 details you provided: Please try again.</p>"; //same here
					}			
				}	
			}
			else    			// This is only if the user inserted a title and a date a lot of the code is the same as the ones above
			{
				$photoIds = array();
				$matchArr = array();
				
				$titleQuery = "SELECT photoID FROM photos WHERE title = \"" . strtolower($userTitle) . "\"";
						
				$titleResult = @mysqli_query($conn, $titleQuery)
									or die('Couldnt get the keywords');
							
				while ($titleRecord = mysqli_fetch_row($titleResult))			// getting the titles
				{
					$photoIds[] = $titleRecord[0];
				}
				
				if(!empty($photoIds)) 
				{
					for($i = 0; $i < count($photoIds); $i++)
					{
						$photosQuery = "SELECT * FROM photos WHERE photoID = " . $photoIds[$i] . " ";
			
						$photosResult = @mysqli_query($conn, $photosQuery)
										or die('Couldnt get the photots');
							
						while ($match = mysqli_fetch_row($photosResult))
						{ 
							if(!empty($userTimeFrame))
							{
								if($userTimeFrame == "Before")
								{							
									if($match[3] < strtolower($userDate))			//checking to see if they check the users search requirements
									{													// if they do store the ids in an array
										$matchArr[] =  $match[0];
									}
									else
									{
										echo "<p> Not working</p>";
									}
								}
								else if($userTimeFrame == "After")
								{
									if($match[3] > strtolower($userDate))
									{
										$matchArr[] =  $match[0];
									}
								}
								else
								{
									if($match[3] == strtolower($userDate))
									{
										$matchArr[] =  $match[0];
									}
								}	
							}
							else
							{
								if(match[3] == strtolower($userDate))
								{
									$matchArr[] =  $match[0];
								}
							}	
						}
					}
					
					if(!empty($matchArr))						// display that array
					{
						echo "<p>Number of result: " . count($matchArr) . "</p>";
						
						for($y = 0; $y < count($matchArr); $y++)
						{
							$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $matchArr[$y] . "\"";
			
							$photosResult = @mysqli_query($conn, $photosQuery)
											or die('Couldnt get the photots');
							
							while ($match = mysqli_fetch_row($photosResult))
							{ 
								echo nl2br("Title: " . $match[1] . "\n");
								echo nl2br("Description: " . $match[2] . "\n");
								echo nl2br("Date uploaded: " . $match[3] . "\n");
								echo nl2br("Associated kewords: ");
							
								$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $matchArr[$y] ."";
						
								$keywordsResult = @mysqli_query($conn, $keywordsQuery)
													or die('Couldnt get the keywords ');
					
								$assocKeywordArr = array();
				
								while ($assocKeyword = mysqli_fetch_row($keywordsResult))
								{
									$assocKeywordArr[] = $assocKeyword[2];
								}
							
								for($x = 0; $x < count($assocKeywordArr); $x++)
								{
									if($x != count($assocKeywordArr) - 1)
									{
										echo"" . $assocKeywordArr[$x]. ", ";
									}	
									else
									{
										echo"" . $assocKeywordArr[$x]. ".</li>";
									}	
								}
								echo nl2br("\n");
								echo "<img id=\"image" . $match[0] . "\" src=\"" . $match[4] . "\" alt=\"" . $match[1] . "\" />";	
								echo nl2br("\n\n");
							}
						}
					}
					else
					{
						echo "<p>Sorry but there were no matches with those details. Please try again.</p>";
					}
				}
				else
				{
					echo "<p>Sorry but there were no matches with those details. Please try again.</p>";
				}	
			}
		}
		else
		{
			if($keywordsExists == true)  // if they keyword exist it means the user inserted a title and keywords
			{
				$keyword = explode(";", strtolower($userKeywords));
				$keywordPhotoIds = array();
				$datePhotoIds = array();
		
				$keywordsQuery = "SELECT * FROM keywords";
						
				$keywordsResult = @mysqli_query($conn, $keywordsQuery)
									or die('Couldnt get the keywords A');		//find the related keywords matches
								
				while ($row = mysqli_fetch_row($keywordsResult))
				{
					for($i = 0; $i < count($keyword); $i++)
					{
						if(strtolower($row[2]) == $keyword[$i])
						{
							$keywordPhotoIds[] = $row[1];
						}
					}	
				}

				$uniquePhotoIds = array_unique($keywordPhotoIds);			//store them in an array
				$matchArr = array();
			
			
				if(!empty($uniquePhotoIds)) 
				{
					foreach($uniquePhotoIds as $unique)
					{
						$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $unique . "\"";
				
						$photosResult = @mysqli_query($conn, $photosQuery)
									or die('Couldnt get the keywords B');
			
						while ($photosRecord = mysqli_fetch_row($photosResult))
						{
							if($photosRecord[1] == strtolower($userTitle))			//check to see if the title matches as well
							{
								$matchArr[] = $photosRecord[0];
							}
						}
					}
					
					if(!empty($matchArr))		// and if it does diplay the results otherwise print a no results found message
					{
						echo "<p>Number of result: " . count($matchArr) . "</p>";
						
						for($y = 0; $y < count($matchArr); $y++)
						{
							$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $matchArr[$y] . "\"";
			
							$photosResult = @mysqli_query($conn, $photosQuery)
											or die('Couldnt get the photots');
							
							while ($match = mysqli_fetch_row($photosResult))
							{ 
								echo nl2br("Title: " . $match[1] . "\n");
								echo nl2br("Description: " . $match[2] . "\n");
								echo nl2br("Date uploaded: " . $match[3] . "\n");
								echo nl2br("Associated kewords: ");
							
								$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $matchArr[$y] ."";
							
								$keywordsResult = @mysqli_query($conn, $keywordsQuery)
													or die('Couldnt get the keywords ');
					
								$assocKeywordArr = array();
				
								while ($assocKeyword = mysqli_fetch_row($keywordsResult))
								{
									$assocKeywordArr[] = $assocKeyword[2];
								}	
							
								for($x = 0; $x < count($assocKeywordArr); $x++)
								{
									if($x != count($assocKeywordArr) - 1)
									{
										echo"" . $assocKeywordArr[$x]. ", ";
									}	
									else
									{
										echo"" . $assocKeywordArr[$x]. ".</li>";
									}	
								}
								echo nl2br("\n");
								echo "<img id=\"image" . $match[0] . "\" src=\"" . $match[4] . "\" alt=\"" . $match[1] . "\" />";	
								echo nl2br("\n\n");
							}
						}
					}
					else
					{
						echo "<p>I'm sorry but there were no results with those 2 details you provided: Please try again.A</p>";
					}
				}
				else
				{
					echo "<p>Sorry but there were no matches with those details. Please try again.B</p>";
				}
			}
			else   // that means that the user only typed in a title
			{
				$photoIds = array();
				
				$titleQuery = "SELECT photoID FROM photos WHERE title = \"" . strtolower($userTitle) . "\"";
						
				$titleResult = @mysqli_query($conn, $titleQuery)
									or die('Couldnt get the keywords');				//find the photos with the same title
							
				while ($titleRecord = mysqli_fetch_row($titleResult))
				{
					$photoIds[] = $titleRecord[0];					//put them in an array
				}
				
				if(!empty($photoIds))     //if we find any matched display them to the screen otherwise display an message
				{
					echo "<p>Number of result: " . count($photoIds) . "</p>";
					
					for($i = 0; $i < count($photoIds); $i++)
					{
						$photosQuery = "SELECT * FROM photos WHERE photoID = " . $photoIds[$i] . " ";
			
						$photosResult = @mysqli_query($conn, $photosQuery)
										or die('Couldnt get the photots');
							
						while ($match = mysqli_fetch_row($photosResult))
						{ 
							echo nl2br("Title: " . $match[1] . "\n");
							echo nl2br("Description: " . $match[2] . "\n");
							echo nl2br("Date uploaded: " . $match[3] . "\n");
							echo nl2br("Associated kewords: ");
							
							$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $photoIds[$i] ."";
						
							$keywordsResult = @mysqli_query($conn, $keywordsQuery)
											or die('Couldnt get the keywords ');
					
							$assocKeywordArr = array();
				
							while ($assocKeyword = mysqli_fetch_row($keywordsResult))
							{
								$assocKeywordArr[] = $assocKeyword[2];
							}
							
							for($x = 0; $x < count($assocKeywordArr); $x++)
							{
								if($x != count($assocKeywordArr) - 1)
								{
									echo"" . $assocKeywordArr[$x]. ", ";
								}	
								else
								{
									echo"" . $assocKeywordArr[$x]. ".</li>";
								}	
							}
							echo "<img id=\"image" . $match[0] . "\" src=\"" . $match[4] . "\" alt=\"" . $match[1] . "\" />";	
							echo nl2br("\n\n");	
						}
					}	
				}
				else
				{
					echo "<p>Sorry but there were no matches with those details. Please try again.</p>";
				}	
			}
		}
	}
	else if($dateExists == true)
	{
		if($keywordsExists == true)  // if the user only filled in the date and the keywords then
		{
			$keyword = explode(";", strtolower($userKeywords));
			$keywordPhotoIds = array();
			$datePhotoIds = array();
		
			$keywordsQuery = "SELECT * FROM keywords";
						
			$keywordsResult = @mysqli_query($conn, $keywordsQuery)
								or die('Couldnt get the keywords A');
							
			while ($row = mysqli_fetch_row($keywordsResult))		//find the keyword matched and put them in an array
			{
				for($i = 0; $i < count($keyword); $i++)
				{
					echo "".$keyword[$i]."";
					
					if(strtolower($row[2]) == $keyword[$i])
					{
						$keywordPhotoIds[] = $row[1];
						
					}
				}	
			}

			$uniquePhotoIds = array_unique($keywordPhotoIds);
			$matchArr = array();
			
			
			if(!empty($uniquePhotoIds)) 
			{
				foreach($uniquePhotoIds as $unique)
				{
					$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $unique . "\"";   // go through all of them to see if they match the date search
				
					$photosResult = @mysqli_query($conn, $photosQuery)
									or die('Couldnt get the keywords B');
			
					while ($photosRecord = mysqli_fetch_row($photosResult))
					{
						if(!empty($userTimeFrame))
						{
							if($userTimeFrame == "Before")
							{							
								if($photosRecord[3] < strtolower($userDate))
								{
									$matchArr[] =  $photosRecord[0];
								}
								else
								{
									echo "<p> Not working</p>";
								}
							}
							else if($userTimeFrame == "After")
							{
								if($photosRecord[3] > strtolower($userDate))		//if they do store them in an array again
								{
									$matchArr[] =  $photosRecord[0];
								}
							}
							else
							{
								if($photosRecord[3] == strtolower($userDate))
								{
									$matchArr[] =  $photosRecord[0];
								}
							}	
						}
						else
						{
							if(photosRecord[3] == strtolower($userDate))
							{
								$matchArr[] =  $photosRecord[0];
							}
						}
						
					}
				}
				
				if(!empty($matchArr))			//and then print them to the screen
				{
					echo "<p>Number of result: " . count($matchArr) . "</p>";
					
					for($y = 0; $y < count($matchArr); $y++)
					{
						$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $matchArr[$y] . "\"";
			
						$photosResult = @mysqli_query($conn, $photosQuery)
										or die('Couldnt get the photots');
							
						while ($match = mysqli_fetch_row($photosResult))
						{ 
							echo nl2br("Title: " . $match[1] . "\n");
							echo nl2br("Description: " . $match[2] . "\n");
							echo nl2br("Date uploaded: " . $match[3] . "\n");
							echo nl2br("Associated kewords: ");
							
							$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $matchArr[$y] ."";
						
							$keywordsResult = @mysqli_query($conn, $keywordsQuery)
											or die('Couldnt get the keywords ');
					
							$assocKeywordArr = array();
				
							while ($assocKeyword = mysqli_fetch_row($keywordsResult))
							{
								$assocKeywordArr[] = $assocKeyword[2];
							}
							
							for($x = 0; $x < count($assocKeywordArr); $x++)
							{
								if($x != count($assocKeywordArr) - 1)
								{
									echo"" . $assocKeywordArr[$x]. ", ";
								}	
								else
								{
									echo"" . $assocKeywordArr[$x]. ".</li>";
								}	
							}
							echo nl2br("\n");
							echo "<img id=\"image" . $match[0] . "\" src=\"" . $match[4] . "\" alt=\"" . $match[1] . "\" />";	
							echo nl2br("\n\n");
						}
					}
				}
				else
				{
					echo "<p>I'm sorry but there were no results with those 2 details you provided: Please try again.</p>";
				}
			}
			else
			{
				echo "<p>I'm sorry but there were no results with those 2 details you provided: Please try again.</p>";
			}			
		}
		else // this means that the user only inserted the date
		{
			$photoIds = array();

			if(!empty($userTimeFrame))
			{
				if($userTimeFrame == "Before")
				{
					$photosQuery = "SELECT * FROM photos WHERE dateOfPhoto < \"" . strtolower($userDate) . "\" ";
				}
				else if($userTimeFrame == "After")
				{
					$photosQuery = "SELECT * FROM photos WHERE dateOfPhoto > \"" . strtolower($userDate) . "\" ";
				}
				else
				{
					$photosQuery = "SELECT * FROM photos WHERE dateOfPhoto = \"" . strtolower($userDate) . "\" ";		//depending on the time frame, change the query
				}	
			}
			else
			{
				$photosQuery = "SELECT * FROM photos WHERE dateOfPhoto = \"" . strtolower($userDate) . "\" ";
			}
			
			$photosResult = @mysqli_query($conn, $photosQuery)
									or die('Couldnt get the keywords');
							
			while ($photosRecord = mysqli_fetch_row($photosResult))
			{
				$photoIds[] = $photosRecord[0];				//find the date upload matches
			}
			
			if(!empty($photoIds)) 			// and print them to the screen
			{
				echo "<p>Number of result: " . count($photoIds) . "</p>";
				
				for($i = 0; $i < count($photoIds); $i++)
				{
					$photosArrayQuery = "SELECT * FROM photos WHERE photoID = " . $photoIds[$i] . " ";
			
					$photosArrayResult = @mysqli_query($conn, $photosArrayQuery)
									or die('Couldnt get the photots');
							
					while ($match = mysqli_fetch_row($photosArrayResult))
					{ 
						echo nl2br("Title: " . $match[1] . "\n");
						echo nl2br("Description: " . $match[2] . "\n");
						echo nl2br("Date uploaded: " . $match[3] . "\n");
						echo nl2br("Associated kewords: ");
				
						$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $photoIds[$i] ."";
						
						$keywordsResult = @mysqli_query($conn, $keywordsQuery)
											or die('Couldnt get the keywords');
					
						$assocKeywordArr = array();
				
						while ($assocKeyword = mysqli_fetch_row($keywordsResult))
						{
							$assocKeywordArr[] = $assocKeyword[2];
						}
				
						for($x = 0; $x < count($assocKeywordArr); $x++)
						{
							if($x != count($assocKeywordArr) - 1)
							{
								echo"" . $assocKeywordArr[$x]. ", ";
							}
							else
							{
								echo"" . $assocKeywordArr[$x]. ".</li>";
							}
						}
						echo nl2br("\n");
						echo "<img id=\"image" . $match[0] . "\" src=\"" . $match[4] . "\" alt=\"" . $match[1] . "\" />";	
						echo nl2br("\n\n");
					}
				}
			}
			else
			{
				echo "<p>Sorry but there were no matches with those details. Please try again.</p>";
			}
		}
	}
	else  //this means that the user only inserted keywords
	{
		$keyword = explode(";", strtolower($userKeywords)); 
		$photoIds = array();
	
		$keywordsQuery = "SELECT * FROM keywords";
						
		$keywordsResult = @mysqli_query($conn, $keywordsQuery)
							or die('Couldnt get the keywords');
							
		while ($row = mysqli_fetch_row($keywordsResult))			//find all the keywords in the database that are matching the user's
		{
			for($i = 0; $i < count($keyword); $i++)
			{
				//echo "".$row[2].", ";
				if(strtolower($row[2]) == $keyword[$i])
				{
					$photoIds[] = $row[1];
				}
			}	
		}
		
		$uniquePhotoIds = array_unique($photoIds);

		
		if(!empty($uniquePhotoIds)) 		//display them to the screen
		{
			echo "<p>Number of result: " . count($uniquePhotoIds) . "</p>";
			
			foreach($uniquePhotoIds as $unique)
			{
				$photosQuery = "SELECT * FROM photos WHERE photoID = \"" . $unique . "\"";
				
			
				$photosResult = @mysqli_query($conn, $photosQuery)
								or die('Couldnt get the photots');
							
				while ($match = mysqli_fetch_row($photosResult))
				{ 
					echo nl2br("Title: " . $match[1] . "\n");
					echo nl2br("Description: " . $match[2] . "\n");
					echo nl2br("Date uploaded: " . $match[3] . "\n");
					echo nl2br("Associated kewords: ");
				
					$keywordsQuery = "SELECT * FROM keywords WHERE photoID = " . $unique ."";
						
					$keywordsResult = @mysqli_query($conn, $keywordsQuery)
									or die('Couldnt get the keywords');
					
					$assocKeywordArr = array();
				
					while ($assocKeyword = mysqli_fetch_row($keywordsResult))
					{
						$assocKeywordArr[] = $assocKeyword[2];
					}
				
					for($x = 0; $x < count($assocKeywordArr); $x++)
					{
						if($x != count($assocKeywordArr) - 1)
						{
							echo"" . $assocKeywordArr[$x]. ", ";
						}
						else
						{
							echo"" . $assocKeywordArr[$x]. ".</li>";
						}
					}
					echo nl2br("\n");
					echo "<br><img id=\"image" . $match[0] . "\" src=\"" . $match[4] . "\" alt=\"" . $match[1] . "\" />";	
					echo nl2br("\n\n");	
				}
			}
		}
		else
		{
			echo "<p>Sorry but there were no matches with those details. Please try again.</p>";
		}
	}

	echo nl2br("\n\n");
?>
	<br><a href="getphotos.php">Go back to the Search Photo page</a>	
	<br><a href="upload.php">Go to the Photo Uploader page</a>		
</body>
</html>