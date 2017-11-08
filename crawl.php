<!DOCTYPE html>
<html>
<head>
    <title>Crawling</title>
    <link href="assets/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/materialize/css/materialize.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/index.css">
</head>
<body>
<div class="container">
<div class="row">
<div class="col s12 m12">
<table class="table bordered striped responsive-table">
<thead>
  <tr>
  <th>no.</th>
  <th>Link</th>
  <th>Title</th>
  <th>Description</th>
  <th>Keywords</th>
</thead>
<tbody>
<?php
$noResult=1;
// THIS PROJECT IS FOR WEB MINING J COMPONENT.
// THIS CODE IS USED TO CRAWL THE WEB PAGE AND EXTRACT INFORMATION.
// ITS A TROPICAL CRAWLER.
// IT FOLLOWS BREADTH FIRST ALGORITHM.

//$start = "https://www.w3schools.com/";
//$start = "https://www.tutorialspoint.com/";
$start=$_GET["link"];
echo $start;
if(!$start)
	$start = "https://www.csstutorial.net/";

//DATABSE INITIALISATION...
$dbhost="localhost";
$dbuser="root";
$dbpass="secret";
$dbname="webminingcrawl";
$connection=mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
if(mysqli_connect_errno()){
	die("Database connection failed:"
		);
}
 //$query="INSERT INTO frontier(title,description,keywords,url) values('mak','mak','mak','mak.com');";

$already_crawled = array();
$crawling = array();


// THIS FUNCTION EXTRACTS THE DETAIL OF THE URL GIVEN TO IT....
function get_details($url) {

	global $connection;
	$options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: MAKBOT/1.0\n"));
	$context = stream_context_create($options);
	$doc = new DOMDocument();
	@$doc->loadHTML(@file_get_contents($url, false, $context));

	//EXTRACTING TITLE, DESCRIPTION AND KEYWORDS...
	$title = $doc->getElementsByTagName("title");
	$title = $title->item(0)->nodeValue;
	$description = "";
	$keywords = "";
	$metas = $doc->getElementsByTagName("meta");
	for ($i = 0; $i < $metas->length; $i++) {
		$meta = $metas->item($i);
		if (strtolower($meta->getAttribute("name")) == "description")
			$description = $meta->getAttribute("content");
		if (strtolower($meta->getAttribute("name")) == "keywords")
			$keywords = $meta->getAttribute("content");
	}
	
	//$title     = mysql_real_escape_string($title);
	//$description     = mysql_real_escape_string($description);
	//$keywords     = mysql_real_escape_string($keywords);
	//$url     = mysql_real_escape_string($url);

	//FOR DEBUGGING...
	global $noResult;
	echo "<tr>";
	echo "<td>$noResult</td>";
	echo "<td><a href='$url'>$url</a></td>";
	echo "<td>$title</td>";
	echo "<td>$description</td>";
	echo "<td>$keywords</td>";
	echo "</tr>";
	$noResult++;
	//DATABASE ACCESSING AND INSERTION...
	$query="INSERT INTO frontier(title,description,keywords,url,hubpoints) values('{$title}','{$description}','{$keywords}','{$url}',0);";
	$result=mysqli_query($connection,$query);
	if($result)
		$contact="success";
	else
		$contact="failure";
	echo "$contact.<p></p>";

	//RETURNING THE TITLE, DESCRIPTION AND KEYWORDS...
	//return '{ "Title": "'.str_replace("\n", "", $title).'", "Description": "'.str_replace("\n", "", $description).'", "Keywords": "'.str_replace("\n", "", $keywords).'", "URL": "'.$url.'"},';
}

// THIS FUNCTION EXTRACTS ALL THE LINKS FROM THE URL PROVIDED TO IT.
function follow_links($url) {
	global $already_crawled;
	global $crawling;
	$options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: MAKBOT/1.0\n"));
	$context = stream_context_create($options);
	$doc = new DOMDocument();
	@$doc->loadHTML(@file_get_contents($url, false, $context));
	$linklist = $doc->getElementsByTagName("a");
	foreach ($linklist as $link) {
		
		// SAVING THE LINK OR URL IN $L VARIABLE...
		$l =  $link->getAttribute("href");
		
		// EDITING THE LINK FOR RUNING IT IN THE BROWSER....
		if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
		} else if (substr($l, 0, 2) == "//") {
			$l = parse_url($url)["scheme"].":".$l;
		} else if (substr($l, 0, 2) == "./") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
		} else if (substr($l, 0, 1) == "#") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
		} else if (substr($l, 0, 3) == "../") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
		} else if (substr($l, 0, 11) == "javascript:") {
			continue;
		} else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
		}
		
		// CHECKING FOR ALREADY CRAWLED. NOTE: A DATABASE WILL BE CREATED ON THE LOCALHOST TO STORE ALL THE URLS WHICH ARE ALREADY CRAWLED, OR IT WILL CHECK FROM THE RANKING TABLE.
		if (!in_array($l, $already_crawled)) {
			$already_crawled[] = $l;
			$crawling[] = $l;

			//echo get_details($l)."<p></p>";
		}
		else{
			// increase Hub Score by one
			$qu="SELECT hubscore from frontier WHERE url=$l;";
			$res=mysqli_query($connection,$qu);
			$roww=mysqli_fetch_assoc($res);
			$hubs=$roww['hubscore'];
			$hubs++;
			$quu="UPDATE frontier SET hubscore=$hubs WHERE url=$l;";
		}
	}
	array_shift($crawling);
	foreach ($crawling as $site) {
		follow_links($site);
	}
}
follow_links($start);
?>
</tbody>
</div>
</div>
</div>
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript" src="assets/materialize/js/materialize.min.js"></script>
</body>
</html>