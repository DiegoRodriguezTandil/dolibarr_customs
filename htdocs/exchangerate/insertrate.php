<?php

require '../main.inc.php';

$Date = date('Y-m-d H:i:s');
$cotizacion = GETPOST('cotizacion');
$currency = GETPOST('currency');

if ($currency == 'USD')  
	{

		$db->begin();   // Start transaction
		$db->query("INSERT INTO llx_currency_conversion Values ('$Date','$currency','ARS','$cotizacion')");
		$db->commit();
	
	
	
	
	//COTIZACION DOLARES POR PESO


$usdtoars = 1 / $cotizacion;

$usdtoars = substr($usdtoars, 0, -9);


$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','ARS','USD','$usdtoars')");
$db->commit();
	
	
	
	
	}
	
	
	
	if ($currency == 'EUR')  
	{

		$db->begin();   // Start transaction
		$db->query("INSERT INTO llx_currency_conversion Values ('$Date','$currency','ARS','$cotizacion')");
		$db->commit();
	
	
	
	
	//COTIZACION EUROS POR PESO


$eurtoars = 1 / $cotizacion;

$eurtoars = substr($eurtoars, 0, -9);


$db->begin();   // Start transaction
$db->query("INSERT INTO llx_currency_conversion Values ('$Date','ARS','EUR','$eurtoars')");
$db->commit();
	
	
	
	
	}
	
	
	
	  
		
		
		
		    
		
 header("Location: addnewrate.php");
?>