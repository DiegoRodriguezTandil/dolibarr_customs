<?php


//COTIZACION DOLAR

$rss = new DOMDocument();
$rss->load('http://themoneyconverter.com/rss-feed/ES/USD/rss.xml');
$feed = array();

foreach ($rss->getElementsByTagName('item') as $node) {
	$item = array ( 
		'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
		'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
		'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
		'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
		
		
		);
	array_push($feed, $item);

}

$limit = 2;

for($x=1;$x<$limit;$x++) {
	//$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
	$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
	$link = $feed[$x]['link'];
	$description = $feed[$x]['desc'];
	$date = date('l F d, Y', strtotime($feed[$x]['date']));
	echo '<p><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong><br />';
	echo '<small><em>Posted on '.$date.'</em></small></p>';
	echo '<p>'.$description.'</p>';
}





//limpiar la variable para sacar la descripcion y dejar unicamente el nro de valor del dolar
$description = substr($description, 20, -15); // returns "de"
//echo "var desc";
//echo $description;




require 'main.inc.php';


//echo gmdate('d-m-Y H:i:s', time());
//$Date = now($str_server_now);
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');

//echo "Fecha" . " " . $Date;

//$value=(int)$description;

echo "<br/>";



$value= str_replace(",",".", $description);



//echo "Value" . $value;


$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','USD','ARS','$value')");
$db->commit();





//---------------------------------------------------------------------------------------------------------

//COTIZACION EURO




$rss = new DOMDocument();
$rss->load('http://themoneyconverter.com/rss-feed/ES/EUR/rss.xml');
$feed = array();

foreach ($rss->getElementsByTagName('item') as $node) {
	$item = array ( 
		'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
		'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
		'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
		'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
		
		
		);
	array_push($feed, $item);

}

$limit = 2;

for($x=1;$x<$limit;$x++) {
	//$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
	$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
	$link = $feed[$x]['link'];
	$description = $feed[$x]['desc'];
	$date = date('l F d, Y', strtotime($feed[$x]['date']));
	echo '<p><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong><br />';
	echo '<small><em>Posted on '.$date.'</em></small></p>';
	echo '<p>'.$description.'</p>';
}





//limpiar la variable para sacar la descripcion y dejar unicamente el nro de valor del dolar
$description = substr($description, 8, -15); // returns "de"
//echo "var desc";
//echo $description;

//require '../main.inc.php';


//echo gmdate('d-m-Y H:i:s', time());
//$Date = now($str_server_now);
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');

echo "<br/>";

//echo "Fecha" . " " . $Date;

//$value=(int)$description;



$value= str_replace(",",".", $description);

echo "<br/>";

//echo "Value" . $value;






$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','EUR','ARS','$value')");
$db->commit();



//










?>