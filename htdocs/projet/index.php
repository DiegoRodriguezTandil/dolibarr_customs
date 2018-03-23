<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *       \file       htdocs/projet/index.php
 *       \ingroup    projet
 *       \brief      Main project home page
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';


$langs->load("projects");
$langs->load("companies");

$mine = $_REQUEST['mode']=='mine' ? 1 : 0;

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
if (!$user->rights->projet->lire) accessforbidden();

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');


/*
 * View
 */

$socstatic=new Societe($db);
$projectstatic=new Project($db);

$projectsListId = $projectstatic->getProjectsAuthorizedForUser($user,($mine?$mine:($user->rights->projet->all->lire?2:0)),1,0,10);
//var_dump($projectsListId);


llxHeader("",$langs->trans("Projects"),"EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos");

$text=$langs->trans("Projects");
if ($mine) $text=$langs->trans("MyProjects");

print_fiche_titre($text);

// Show description of content
if ($mine) print $langs->trans("MyProjectsDesc").'<br><br>';
else
{
	if ($user->rights->projet->all->lire && ! $socid) print $langs->trans("ProjectsDesc").'<br><br>';
	else print $langs->trans("ProjectsPublicDesc").'<br><br>';
}

print '<table border="0" width="100%" class="notopnoleftnoright">';
print '<tr><td width="30%" valign="top" class="notopnoleft">';

print_projecttasks_array($db,$socid,$projectsListId);


/*************************************************************************************************************
insert if consolidation is true
when send post for save consolidation currencies of projet the insert this into consolidation scheme
 */

if(!empty($_POST['divisas_hidden'])){


    $fecha_inicio_dolar 	= DateTime::createFromFormat('d/m/Y', $_POST['fecha_inicio_dolar'])->format('Y-m-d');
    $divisas_peso 			= $_POST['divisas_peso'];
    $divisas_peso_a_dolar	= $_POST['divisas_peso_a_dolar'];
    $fecha_inicio_euro 		=DateTime::createFromFormat('d/m/Y', $_POST['fecha_inicio_euro'])->format('Y-m-d');
    $divisas_euro 			= $_POST['divisas_euro'];
    $divisas_euro_a_dolar	= $_POST['divisas_euro_a_dolar'];
	if(!empty($divisas_peso) and !empty($divisas_peso_a_dolar) ){
		$sql ="INSERT INTO ".MAIN_DB_PREFIX."consolidation_day (fecha_ingreso,divisa_origen,valor_divisa_origen,valor_divisa_destino)";
		$sql.= " VALUES ('".$fecha_inicio_dolar."','ARS','".$divisas_peso."','".$divisas_peso_a_dolar."');";
		$resql = $db->query($sql);

	}
    if(!empty($divisas_euro) and !empty($divisas_euro_a_dolar) ){
        $sql ="INSERT INTO ".MAIN_DB_PREFIX."consolidation_day (fecha_ingreso,divisa_origen,valor_divisa_origen,valor_divisa_destino)";
        $sql.= " VALUES ('".$fecha_inicio_euro."','EUR','".$divisas_euro."','".$divisas_euro_a_dolar."')";
        $resql = $db->query($sql);
    }
}

/**************************************************************************************************************/


echo'
<style>

	.div_convention{
				width:550px;
				heigth:65px;
				padding-top: 55px;
				margin-left:15px;
				margin-top:25px;
				color: "-internal-quirk-inherit";
				font-family: arial,tahoma,verdana,helvetica;
				background: rgb(222, 231, 236);	
		}
		.convention_form{
				width:545px;
				margin-left:5px;
			}

		.tabla_conversion{
			margin-top:5px;
			padding-bottom: 55px;
			width:540px;

		}	

		.seleccion_de_divisa{
			width:250px
		}
		.btn_submit{
			float: right;
		}
		.id_monedas{
				margin-left:5px;
				float: left;
			}
		.euro{
			padding-right:17px;
			
		}
		.euro2{
			padding-right:13px;
			
		}	
		.peso{
			padding-right:17px;
			
		}			

		.labels{
			margin-right: 5px;			
		}	
 
			
</style>

';



$form = new Form($db);// Date start

echo '


	<div  class=" tabBar div_convention"  id="conf_consolidation">
		<h3>Ingreso de Divisas</h3>
		<span style=" font-style: italic;">Ingreso diario de divisas y su equivalente en U$S</span>
		<form action="index.php?mainmenu=project&leftmenu" class="convention_form" id="form_moneda"  method="post">
		<input type="hidden" name="consolidation" value="1">
			<table class=\'border tabla_conversion\'>
				<thead>
					<tr>
						<th>Fecha</th>
						<th>Valor Divisa Origen</th>
						<th>Valor Divisa de Coversión</th>
					</tr>
				</thead>
				 <tbody>	
					 <tr>
						<td>
						 	<div class=\'modeda_seleccionada_origen id_monedas labels\'  >Fecha
							</div>
							<div class="fecha">';
								print $form->select_date(($date_start?$date_start:''),'fecha_inicio_dolar');
        echo '           	</div>
						</td>
						<td> <div class=\'modeda_seleccionada_origen id_monedas labels peso\'  >$</div>
							<input type=\'NUMBER\' class=\'input_divisa_original\' step=\'any\' id="divisas_peso"  name=\'divisas_peso\' value= required>
						</td>
						<td> <div class=\'modeda_seleccionada_origen id_monedas labels\'  >U$S</div>
							<input type=\'NUMBER\' class=\'input_divisa_original\' step=\'any\' id="divisas_peso_a_dolar" name=\'divisas_peso_a_dolar\' value= required>
						</td>
					 </tr>
					 <tr>
						<td>
						 	<div class="modeda_seleccionada_origen id_monedas labels" >
  									Fecha
							</div>';
							print $form->select_date(($date_start?$date_start:''),'fecha_inicio_euro');
			echo '		</td>
						<td> <div  class=\'modeda_seleccionada_origen id_monedas euro labels\'  > € </div>
							<input type=\'NUMBER\' class=\'input_divisa_original\' step=\'any\' id="divisas_euro" name=\'divisas_euro\' value= required>
						</td>
						<td>
							<div >
								<div class=\'moneda_seleccionada_para_conversion id_monedas \'>U$S
								</div>
								<input class=\'id_monedas\' type=\'NUMBER\' step=\'any\'data-moneda=\'\' id="divisas_euro_a_dolar" name=\'divisas_euro_a_dolar\' value= required>	
							</div>
						</td>
					 </tr>
			     </tbody>
			</table>
			<input type="hidden" id="divisas_hidden" name="divisas_hidden" value=1>
			<button type="submit"  class="btn_submit" value="Submit">Confirmar</button>
				<div id="error_form">				
				</div>
				</br></br>
		</form>
</div> 
';


echo '
	<script>
	/*	$("#form_moneda").submit(function() {
		 var fid  = $("#fecha_inicio_dolar").val();
		 var dvp  = $("#divisas_peso").val();
		 var dpd  = $("#divisas_peso_a_dolar").val();
		 var fie  = $("#fecha_inicio_euro").val();
		 var dve  = $("#divisas_euro").val();
		 var dvea = $("#divisas_euro_a_dolar").val()
			if( fid=="" || dvp=="" || dpd=="" || fie=="" || dve=="" || dvea==""){
				$("#error_form").html("<h5>Error, todos los campos de la divisa $ deben de ser ingresados.</h5>");
			}
			return false; // return false to cancel form action
		});	
	*/		 
	</script>

';




print '</td><td width="70%" valign="top" class="notopnoleftnoright">';

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("ThirdParties"),"index.php","s.nom","","","",$sortfield,$sortorder);
print_liste_field_titre($langs->trans("NbOfProjects"),"","","","",'align="right"',$sortfield,$sortorder);
print "</tr>\n";

$sql = "SELECT count(p.rowid) as nb";
$sql.= ", s.nom, s.rowid as socid";
$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s on p.fk_soc = s.rowid";
$sql.= " WHERE p.entity = ".$conf->entity;
if ($mine || ! $user->rights->projet->all->lire) $sql.= " AND p.rowid IN (".$projectsListId.")";
if ($socid)	$sql.= "  AND (p.fk_soc IS NULL OR p.fk_soc = 0 OR p.fk_soc = ".$socid.")";
$sql.= " GROUP BY s.nom, s.rowid";

$var=true;
$resql = $db->query($sql);
if ( $resql )
{
	$num = $db->num_rows($resql);
	$i = 0;

	while ($i < $num)
	{
		$obj = $db->fetch_object($resql);
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td nowrap="nowrap">';
		if ($obj->socid)
		{
			$socstatic->id=$obj->socid;
			$socstatic->nom=$obj->nom;
			print $socstatic->getNomUrl(1);
		}
		else
		{
			print $langs->trans("OthersNotLinkedToThirdParty");
		}
		print '</td>';
		print '<td align="right"><a href="'.DOL_URL_ROOT.'/projet/liste.php?socid='.$obj->socid.'">'.$obj->nb.'</a></td>';
		print "</tr>\n";

		$i++;
	}

	$db->free($resql);
}
else
{
	dol_print_error($db);
}
print "</table>";

print '</td></tr></table>';


llxFooter();

$db->close();
?>
