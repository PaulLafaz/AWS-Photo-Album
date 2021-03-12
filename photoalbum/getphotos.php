<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Assignment 1b Cloud Architecture" />
	<meta name="keywords" content="getphotos" />
	<meta name="author" content="Apostolos Lafazanis" />
	<title>Search Page</title>
</head>
<body>
	<h1>Photo Search Page</h1>
	<p>Welcome to my Photo Search Page! Here you can search for any photos you are interested in based on a title or date or keywords that are related to the photos.<br>You can leave the fields that you are not interested in blank.</p>
	<p>Please type in your searching critiria down below:</p>
	
	<form action= "getPhotosProcess.php" method="post">
		<label>Title:</label>
		<input type="text" name="title" placeholder="Type in a photo title..." /><br>
		<label>Date uploaded:</label>
		<input type="date" name="photoDate" /><br>
		<label>Time Frame(Specify whether you want to search for photos uploaded after or before your provided date):</label><br>
		<input type="radio" id="" name="timeFrame" value="Before" />
		<label>Before</label>
		<input type="radio" id="" name="timeFrame" value="After" />
		<label>After</label>
		<input type="radio" id="" name="timeFrame" value="On that date" />
		<label>On that date</label><br>
		<label>Keyword(separated by a semicolonm, e.g. keyword1;keyword2; etc.):</label><br>
		<input type ="text" name = "keywords"  /><br>
		<input type="submit" value="Search"/>
	</form>
	<a href="upload.php">Upload Photo Page</a><br>
	<a href="showAllPhotos.php">Display all photos</a>
</body>
</html>