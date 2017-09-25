<?php

function conectar_db() {
mysql_connect('localhost','admin','082244');
mysql_select_db('gestion') or die ('Error al seleccionar db');

}



?>