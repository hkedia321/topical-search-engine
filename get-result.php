<?php

$conn = mysqli_connect("localhost","root","secret","webminingcrawl");

if (!$conn)
{
  die('Could not connect: ' . mysqli_error());
  echo "not connected";
}
//    echo "Great Job";


$a = $_GET['query'];


$query2 = "SELECT * FROM frontier WHERE keywords LIKE '%$a%' or title like '%$a%' ORDER BY hubscore DESC;";
//echo $query2;
$run  = mysqli_query($conn,$query2);

if (!$run)
{
	echo "This project is doomed";
}

$i=0;
$j=0;

while($result = mysqli_fetch_assoc($run))
{
  $title[] = $result['title'];
  $description[] = $result['description'];
  $keywords[] = $result['keywords'];
  $url[] = $result['url'];
  $i++;
}

//for debugging and not for normal use
//  print_r($title);
//  echo "\n";
//  print_r($description);
//  echo "\n";
//  print_r($keywords);
//  echo "\n";
//  print_r($i);
//  print_r($url);
//  echo "\n";

mysqli_close($conn);


$json1=array();
$urls=[];
for($j=0; $j<$i; $j++)
{
  if(!in_array($url[$j],$urls)){
    $obj = (object) [
    'title' => $title[$j],
    'description' => $description[$j],
    'keywords' => $keywords[$j],
    'url' => urldecode($url[$j]),
  ];
    $urls[]=$url[$j];
    $json1[]=$obj;
  }
  else{
    //echo "FALSE";
  }
  //print_r($urls);
}

header('Content-Type: application/json');
echo json_encode($json1);
