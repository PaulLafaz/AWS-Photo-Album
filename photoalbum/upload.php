<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="Assignment 2 Cloud Architecture" />
	<meta name="keywords" content="assingment" />
	<meta name="author" content="Apostolos Lafazanis" />
	<title>Uploader Page</title>
</head>
<body>
	<h1>Photo Uploader</h1>
	<h3>Student ID: 101360815</h3>
	<h3>Name: Apostolos Lafazanis</h3>
	<form action = "uploadProcess.php" method = "post" enctype="multipart/form-data">
		<fieldset>
			<label>Photo title:</label>
			<input type ="text" name = "title" maxlength = "20" /><br>
			
			<label>Select a photo:(Less than 2MB and with no spaces in the files name)</label><br>
			<input type ="file" name = "image" /><br>
			
			<label>Description:</label>
			<input type ="text" name = "descr" maxlength = "40" /><br>
			
			<label>Date:</label>
			<input type ="date" name = "uploadDate" /><br>
			
			<label>Keyword(separated by a semicolonm, e.g. keyword1;keyword2; etc.):</label><br>
			<input type ="text" name = "keywords" maxlength = "40" /><br>
			
			<input type="submit" value="Upload"/>
		</fieldset>
	</form>
	<a href="getphotos.php">Search Photos</a><br>
	<a href="showAllPhotos.php">Display all Photos</a><br>
</body>
</html>