<?php
$conn = mysqli_connect("localhost","root","secret","webminingcrawl");

if (!$conn)
{
  die('Could not connect: ' . mysqli_error());
  echo "not connected";
}

$query="CREATE TABLE frontier(title text,description text, keywords text, url varchar(256), hubscore int, PRIMARY KEY (url));";
echo $query;
$run  = mysqli_query($conn,$query);

if (!$run)
{
	echo "The Create Table failed";
}
else{
	echo "Table created Sucessfully";
}
