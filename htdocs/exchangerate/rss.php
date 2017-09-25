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
$description = substr($description, 20); //sacar la primera parte de la descripcion 
$description = preg_replace('/[^0-9,]/s', '', $description); // de lo restante sacar todo lo que no sea numerico excepto la coma
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');
$value= str_replace(",",".", $description);

echo $description;




require '../main.inc.php';

//echo gmdate('d-m-Y H:i:s', time());
//$Date = now($str_server_now);
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');
$value= str_replace(",",".", $description);


$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','USD','ARS','$value')");
$db->commit();


//--------------------------------------------------------------------------------------------------------


//COTIZACION DOLARES POR PESO


$usdtoars = 1 / $value;

//$usdtoars = substr($usdtoars, 0, -9);


$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','ARS','USD','$usdtoars')");
$db->commit();


//--------------------------------------------------------------------------------------------------------

//COTIZACION DOLAR A EURO

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

$limit = 25;

for($x=24;$x<$limit;$x++) {
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
$description = substr($description, 20); //sacar la primera parte de la descripcion 
$description = preg_replace('/[^0-9,]/s', '', $description); // de lo restante sacar todo lo que no sea numerico excepto la coma
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');
echo $description;
$value= str_replace(",",".", $description);

$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','USD','EUR','$value')");
$db->commit();

//--------------------------------------------------------------------------------------------------------




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





//limpiar la variable para sacar la descripcion y dejar unicamente el nro de valor del euro
$description = substr($description, 8); 
$description = preg_replace('/[^0-9,]/s', '', $description); // de lo restante sacar todo lo que no sea numerico excepto la coma
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');
$value= str_replace(",",".", $description);
echo $description;
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');

$value= str_replace(",",".", $description);


$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','EUR','ARS','$value')");
$db->commit();







//

//--------------------------------------------------------------------------------------------------------


//COTIZACION EUROS POR PESO


$eurtoars = 1 / $value;

//$eurtoars = substr($eurtoars, 0, -9);


$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','ARS','EUR','$eurtoars')");
$db->commit();

//echo "eurtoars" . $eurtoars;




//---------------------------------------------------------------------------------------------------------






//---------------------------------------------------------------------------------------------------------

//COTIZACION EURO A DOLAR




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

$limit = 85;

for($x=84;$x<$limit;$x++) {
	//$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
	$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
	$link = $feed[$x]['link'];
	$description = $feed[$x]['desc'];
	$date = date('l F d, Y', strtotime($feed[$x]['date']));
	echo '<p><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong><br />';
	echo '<small><em>Posted on '.$date.'</em></small></p>';
	echo '<p>'.$description.'</p>';
}





//limpiar la variable para sacar la descripcion y dejar unicamente el nro de valor del euro
$description = substr($description, 8);
$description = preg_replace('/[^0-9,]/s', '', $description); // de lo restante sacar todo lo que no sea numerico excepto la coma
date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');
$value= str_replace(",",".", $description);

echo $description;

date_default_timezone_set("America/Buenos_Aires");
$Date = date('Y-m-d H:i:s');


$value= str_replace(",",".", $description);



$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','EUR','USD','$value')");
$db->commit();







//






?>