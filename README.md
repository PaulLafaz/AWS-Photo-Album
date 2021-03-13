# AWS Photo Album

This repository contains code from my photo album website I created whilst studying 
Cloud Achitecture at Swinburne University.

The user could access the website to upload photos as well as their details and then either 
display the entire collection of photos in the database or perform a search to fiter out the 
photos of their choice.

Few important things worth noting are:

* The website was hosted on an **AWS EC2** instance using Apache HTTP Server (which was installed in the insatnce).
* The photos the user uploaded were stored on an **AWS S3** Bucket.
* The photo details were stored in a **AWS RDS** Database.
* The data table population was achieved using **PHP MyAdmin**.
* There was no CSS or Javascripet implemented in the website.

Here are some photos for you to look:


## Upload Page
<img src="https://github.com/PaulLafaz/AWS-Photo-Album/blob/main/images/Uploading_Page.PNG">

## Search Page
<img src="https://github.com/PaulLafaz/AWS-Photo-Album/blob/main/images/Search_Page.PNG">

## Search Result Page
<img src="https://github.com/PaulLafaz/AWS-Photo-Album/blob/main/images/Search_Results.PNG">
