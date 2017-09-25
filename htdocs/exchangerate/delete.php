<?php

require '../main.inc.php';

//$date= GETPOST('date');
$date= GETPOST('id');

$db->begin();   // Start transaction
$db->query("DELETE FROM llx_currency_conversion WHERE date = '$date'");
$db->commit();

header("location:../exchangerate/exchangeratelist.php");
?>