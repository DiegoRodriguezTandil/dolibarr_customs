<?php

require '../main2.inc.php';

$id = $_GET['id'];
$pricelevel = $_GET['prxx'];
$price = $_GET['price'];


$db->begin();   // Start transaction

$db->query("UPDATE llx_product_price SET price_level ='$pricelevel', price='$price' WHERE fk_product='$id'");

$db->commit();


print '
				<html>
				<head>
				<script>
				function loaded()
				{
					window.setTimeout(CloseMe,1000);
				}

				function CloseMe() 
				{
					window.close();
				}
				</script>
				</head>
				<body onLoad="loaded()">
				Producto Ingresado
				</body>';



?>