<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2012	   Juanjo Menent        <jmenent@2byte.es>
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
 *      \file       htdocs/projet/element.php
 *      \ingroup    projet facture
 *		\brief      Page of project referrers
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/policy.class.php';

if (! empty($conf->propal->enabled))      require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
if (! empty($conf->facture->enabled))     require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
if (! empty($conf->facture->enabled))     require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture-rec.class.php';
if (! empty($conf->commande->enabled))    require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
if (! empty($conf->salesorder->enabled))  require_once DOL_DOCUMENT_ROOT.'/salesorder/class/salesorder.class.php';
if (! empty($conf->fournisseur->enabled)) require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
if (! empty($conf->fournisseur->enabled)) require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
if (! empty($conf->contrat->enabled))     require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
if (! empty($conf->ficheinter->enabled))  require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
if (! empty($conf->deplacement->enabled)) require_once DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';
if (! empty($conf->agenda->enabled))      require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';

$langs->load("projects");
$langs->load("companies");
$langs->load("suppliers");
if (! empty($conf->facture->enabled))  	$langs->load("bills");
if (! empty($conf->commande->enabled)) 	$langs->load("orders");
if (! empty($conf->propal->enabled))   	$langs->load("propal");
if (! empty($conf->ficheinter->enabled))	$langs->load("interventions");



$projectid=GETPOST('id');
$ref=GETPOST('ref');
if ($projectid == '' && $ref == '')
{
	dol_print_error('','Bad parameter');
	exit;
}


/************************************************************************
 show o hide form de consolidation
*/
print '<script>
		function abrirForm(){
			if($("#conf_consolidation").css("display")=="none"){
				$("#conf_consolidation").css("display", "block");
				$("#conf_consolidation").css("margin-top","15px");
			}else{
					$("#conf_consolidation").css("display", "none");
					$("#conf_consolidation").css("margin-top","15px");	
				}
$valor_moneda_conversion= $(".seleccion_de_divisa").val();

			$( ".modeda_seleccionada_origen" ).each(function() {
				if($( this ).text() == $valor_moneda_conversion){
					var li=$(this).parent().parent();
					li.find("input").each(function() {
							$( this ).attr("readonly", true);
							$( this ).val(1);
						});
					}
			}); 
		}

		function moneda_seleccionada(){
			$valor_moneda_conversion= $(".seleccion_de_divisa").val();
			$(".moneda_seleccionada_para_conversion").html($valor_moneda_conversion);
			$(".moneda_seleccionada_para_conversion").data("moneda",$valor_moneda_conversion);
			 $(".id_monedas").removeAttr("readonly");
			$(".input_divisa_original").removeAttr("readonly");


			$( ".modeda_seleccionada_origen" ).each(function() {
				if($( this ).text() == $valor_moneda_conversion){
					var li=$(this).parent().parent();
					li.find("input").each(function() {
							$( this ).attr("readonly", true);
							$( this ).val(1);
						});
					}
			}); 
			
			
		}	

		

</script>';
/*************************************************************************/


/*************************************************************************************************************
	insert if consolidation is true
	when send post for save consolidation currencies of projet the insert this into consolidation scheme
*/

if(!empty($_POST['consolidation'])){
	
    $moneda_consolidacion=$_POST['seleccion_de_divisa'];
	$sql ="INSERT INTO ".MAIN_DB_PREFIX."consolidation (fk_projet, fk_currency)";
	$sql.= " VALUES (".$projectid.",'".$moneda_consolidacion."')";
    $sql.= " ON DUPLICATE KEY UPDATE fk_currency= '".$moneda_consolidacion."';";
	$resql = $db->query($sql);

	if(!empty($_POST['divisas'])){
		$arrDivisas=$_POST['divisas'];
		foreach ($arrDivisas as $divisa => $valores_conversion) {
				$sql='';
				$sql.="INSERT INTO ".MAIN_DB_PREFIX."consolidation_detail (fk_projet, fk_currency, value,CURRENCY_CONVERTION_VALUE )";
				$sql.= " VALUES (".$projectid.",'". $divisa."',".$valores_conversion['val_original'].",".$valores_conversion['val_conversion'].")";
				$sql.= " ON DUPLICATE KEY UPDATE VALUE=".$valores_conversion['val_original'].",CURRENCY_CONVERTION_VALUE=".$valores_conversion['val_conversion'].";";
				$resql = $db->query($sql);			
		}
 	}
	 

}

/**************************************************************************************************************/
/*************************************************************************************************************
INSERT INTO llx_consolidation_salesorder

 */

if(!empty($_POST['salesorder_id'])){
    $fecha_ingreso	= DateTime::createFromFormat('d/m/Y', $_POST['fecha_inicio_dolar'])->format('Y-m-d');
    $divisa_origen 			= $_POST['divisas_peso'];
    $valor_divisa_origen	= $_POST['divisas_peso_a_dolar'];
    $valor_divisa_destino	= $_POST['divisas_peso_a_dolar'];
    $salesorder_id			= $_POST['divisas_peso_a_dolar'];

	$sql ="INSERT INTO ".MAIN_DB_PREFIX."consolidation_salesorder (salesorder_id,fecha_ingreso,divisa_origen,valor_divisa_origen,valor_divisa_destino)";
	$sql.= " VALUES (".$salesorder_id.",'".$fecha_ingreso."','".$divisa_origen."',".$valor_divisa_origen.",'".$divisas_peso_a_dolar.");";
	$resql = $db->query($sql);

}

/**************************************************************************************************************/

$mine = $_REQUEST['mode']=='mine' ? 1 : 0;
//if (! $user->rights->projet->all->lire) $mine=1;	// Special for projects

$project = new Project($db);
if ($ref)
{
    $project->fetch(0,$ref);
    $projectid=$project->id;
}

// Security check
$socid=0;
if ($user->societe_id > 0) $socid=$user->societe_id;
$result = restrictedArea($user, 'projet', $projectid);


/*
 *	View
 */

$help_url="EN:Module_Projects|FR:Module_Projets|ES:M&oacute;dulo_Proyectos";
llxHeader("",$langs->trans("Referers"),$help_url);

$form = new Form($db);

$userstatic=new User($db);

$project = new Project($db);
$project->fetch($_GET["id"],$_GET["ref"]);
$project->societe->fetch($project->societe->id);

// To verify role of users
$userAccess = $project->restrictedProjectArea($user);

$head=project_prepare_head($project);
dol_fiche_head($head, 'resultado', $langs->trans("Project"),0,($project->public?'projectpub':'project'));


print '<table class="border" width="100%">';

$linkback = '<a href="'.DOL_URL_ROOT.'/projet/liste.php">'.$langs->trans("BackToList").'</a>';

print '<tr><td width="30%">'.$langs->trans("Ref").'</td><td>';
// Define a complementary filter for search of next/prev ref.
if (! $user->rights->projet->all->lire)
{
    $projectsListId = $project->getProjectsAuthorizedForUser($user,$mine,0);
    $project->next_prev_filter=" rowid in (".(count($projectsListId)?join(',',array_keys($projectsListId)):'0').")";
}
print $form->showrefnav($project, 'ref', $linkback, 1, 'ref', 'ref');
print '</td></tr>';

print '<tr><td>'.$langs->trans("Label").'</td><td>'.$project->title.'</td></tr>';

print '<tr><td>'.$langs->trans("Company").'</td><td>';
if (! empty($project->societe->id)) print $project->societe->getNomUrl(1);
else print '&nbsp;';
print '</td></tr>';

// Visibility
print '<tr><td>'.$langs->trans("Visibility").'</td><td>';
if ($project->public) print $langs->trans('SharedProject');
else print $langs->trans('PrivateProject');
print '</td></tr>';

// Statut
print '<tr><td>'.$langs->trans("Status").'</td><td>'.$project->getLibStatut(4).'</td></tr>';

print '</table>';

print '</div>';





/*
	Franco
	Add button consolidation 
	add form for select currency consolodation 
	and set currencies from proyect for currency consolodation 
*/

/*
	**************************************************************************************************++
	style of the table form	
*/

	print '<style>
			.div_convention{
					width:500px;
					heigth:25px;
					margin-left:15px;
					margin-top:15px;
					color: -internal-quirk-inherit;
					font-family: arial,tahoma,verdana,helvetica;
					background: rgb(222, 231, 236);	
					display:none;
			}
			.convention_form{
					width:435px;
					margin-left:35px;
				}

			.tabla_conversion{
				margin-top:5px;
				width:435px;

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



		</style>';
/*	***************************************************************************************************/

/*
	**************************************************************************************************
	button convention
*/

	print '<a class="butAction" onclick="abrirForm()"  id="cotization">Seleccionar moneda de consolidación</a><br>';

/*	**************************************************************************************************/

/*
	**************************************************************************************************++
	form convention
*/

print '
	<div  class="tabBar div_convention"  id="conf_consolidation">
		<form action="resultado.php?id='.$project->id.'" class="convention_form"  method="post">
		<input type="hidden" name="consolidation" value="1">
			<label> Divisa de Consolidación :</label>
			<select class="seleccion_de_divisa" onchange="moneda_seleccionada()"  name="seleccion_de_divisa" >';
			/***********************************************************************************
			values of select currencies
			*/
			$exist_convention=$db->query("
				SELECT c.fk_currency,cc.label
				FROM  ".MAIN_DB_PREFIX."consolidation c
				JOIN ".MAIN_DB_PREFIX."c_currencies cc ON(c.fk_currency=cc.code_iso)
				WHERE  c.fk_projet ={$projectid} 
			");	
			
			//if existe tuplas enotnces existe una consolidacion para el poyecto
			if($exist_convention && $db->num_rows($exist_convention)>0){	
				$objp = $db->fetch_object($exist_convention);
				$objp->fk_currency;
				$moneda_consolidada=$objp->fk_currency;
				$all_currencies=$db->query("
									SELECT code_iso,label 
									FROM 	".MAIN_DB_PREFIX."c_currencies
								");
				echo "<option value={$objp->fk_currency}>".$objp->fk_currency ."(".$objp->label.")"." </option>";
				$num = $db->num_rows($all_currencies);
				$i = 0;
				while ($i < $num)
				{
					$objp = $db->fetch_object($all_currencies);
					$objp->code_iso;
					echo "<option value={$objp->code_iso}>".$objp->code_iso ."(".$objp->label.")"." </option>";
					//$currencies[$objp->code_iso]=$objp->label;
					$i++;
				}
				$db->free($all_currencies);
			}else{	
				
				//get all currencies
				$result=$db->query("
					SELECT code_iso,label 
					FROM 	".MAIN_DB_PREFIX."c_currencies
				");		
				//is true then create te select values
				if ($result)
				{
					$num = $db->num_rows($result);
					$i = 0;
					while ($i < $num)
					{
						$objp = $db->fetch_object($result);
						$moneda_consolidacion=$objp->code_iso;
						echo "<option value={$moneda_consolidacion}>".$moneda_consolidacion ."(".$objp->label.")"." </option>";
						//$currencies[$objp->code_iso]=$objp->label;
						$i++;
					}
					$db->free($result);
				}else
				{
					dol_print_error($db);
				}
			}
			print '</select><br>';
			/*
			 	end of valores del select currencies
			 ****************************************************************
			*/
			/******************************************************************
			  currencies of projet
			*/
			//if existe una consolidacion quiere decir que existen divisas con su valor de conversion 
	
			if($exist_convention && $db->num_rows($exist_convention)>0){
				$result=$db->query("
					SELECT cd.FK_CURRENCY,VALUE,CURRENCY_CONVERTION_VALUE 
					FROM  llx_consolidation_detail cd join llx_consolidation c 
					WHERE   cd.FK_PROJET=c.FK_PROJET and c.FK_PROJET ={$project->id}
			");
			}else{
				//si no existe entonces se recupera la divisa y su valor esta en blanco 
				$result=$db->query("
					SELECT FK_CURRENCY 
					FROM  PROJET_CURRENCY
					WHERE FK_PROJET ={$project->id}
				");	
			}	
			print"
			<table class='border tabla_conversion'>
				<thead>
					<tr>
						<th>Divisa Original</th>
						<th>Divisa de Coversión</th>
					</tr>
				</thead>
				 <tbody>";
    
					if ($result)
					{
						$num = $db->num_rows($result);
						$i = 0;
						while ($i < $num)
						{
							$objp = $db->fetch_object($result);
							echo "	<tr>
										<td> <div class='modeda_seleccionada_origen id_monedas'  >".$objp->FK_CURRENCY."</div>
										<input type='NUMBER' class='input_divisa_original' step='any' name='divisas[{$objp->FK_CURRENCY}][val_original]' value="; 
												 if(!empty($objp->VALUE)){ echo number_format($objp->VALUE, 2, '.', '');}
											 echo" required>
										</td>
										<td>";
									 		echo
											  "<div >";
												if(!empty($moneda_consolidada)){
														echo  "<div class='moneda_seleccionada_para_conversion id_monedas'  >". $moneda_consolidada."</div>";
														echo "<input class='id_monedas' data-moneda='{$moneda_consolidada}' type='NUMBER' step='any' name='divisas[{$objp->FK_CURRENCY}][val_conversion]' value="; 
														if(!empty($objp->VALUE)){ echo number_format($objp->CURRENCY_CONVERTION_VALUE, 2, '.', '');}
														echo" required>";
													}
													else {
														echo 	"<div class='moneda_seleccionada_para_conversion id_monedas'>". $moneda_consolidada."</div>";
														echo "<input class='id_monedas' type='NUMBER' step='any'data-moneda='' name='divisas[{$objp->FK_CURRENCY}][val_conversion]' value="; 
														if(!empty($objp->VALUE)){ echo number_format($objp->CURRENCY_CONVERTION_VALUE, 2, '.', '');}
														echo" required>";
													}				
												echo"	
											   </div>
										</td>
								   </tr>";


							$i++;
						}
						$db->free($result);
					}else
					{
						dol_print_error($db);
					}
			/*
				end currencies of projet
				************************************************************************************
			*/		
        print
			'</tbody>
		</table>
			<button type="submit"  class="btn_submit" value="Submit">Confirmar</button></br>
		</form>
	</div>';

echo
	"<script>
		function conversionManual(  linea){

		    var id_conversion_general='#conversion_general-'+linea;
			var id_conversion_manual ='#conversion_manual-'+linea;
		    $(id_conversion_general).hide();
		    $(id_conversion_manual).show();
		    
		}
		function cancelarConversionManual(  linea){
			var id_conversion_general='#conversion_general-'+linea;
			var id_conversion_manual ='#conversion_manual-'+linea;
			var id_input_nueva_conversion='.input_nueva_conversion-'+linea;
			console.log(id_input_nueva_conversion);
		    $(id_conversion_general).show();
		    $(id_conversion_manual).hide();
		    $(id_input_nueva_conversion).val('');
		    
		}	
		
	</script>";


/*
 * Referers types
 */

$listofreferent=array(
'salesorder'=>array(
	'title'=>"Listado de Ordenes de Venta asociadas al proyecto",
	'class'=>'Salesorder',
	'test'=>$conf->salesorder->enabled,
	'operation'=>'+'),
'trip'=>array(
	'title'=>"ListTripAssociatedProject",
	'class'=>'Deplacement',
	'test'=>$conf->deplacement->enabled,
	'operation'=>'-'),
	
'policy'=>array(
	'title'=>"Listado de pólizas asociadas al proyecto",
	'class'=>'Policy',
	'test'=>$conf->deplacement->enabled,
	'operation'=>'-'),
/*'Resultado'=>array(
	'title'=>"Totales",
	'class'=>'Policy',
	'test'=>$conf->deplacement->enabled)	*/
);
$arrayCurrencys = array();
$importeTotales = array();
foreach ($listofreferent as $key => $value)
{
	$title=$value['title'];
	$operation=$value['operation'];
	$classname=$value['class'];
	$qualified=$value['test'];
	$importeTotales[$title]['operation']=$operation;
	$linea=0;
	$view_tcc=false;
	if ($qualified)
	{
		print '<br>';

		print_titre($langs->trans($title));
		print '<table class="noborder" width="100%">';

		print '<tr class="liste_titre">';
		print '<td width="100">'.$langs->trans("Ref").'</td>';
		print '<td width="100" align="center">'.$langs->trans("Date").'</td>';
		print '<td width="200">'.$langs->trans("ThirdParty").'</td>';
		//FEDE
		print '<td align="right" width="120">'.$langs->trans("Divisa").'</td>';
		
		if (empty($value['disableamount'])) print '<td align="right" width="120">'.$langs->trans("Importe").'</td>';

		//FIN FEDE

		if (empty($value['disableamount']))
			if($classname=='Salesorder')
				print '<td align="right" width="120">'.$langs->trans("Venta").'</td>';
			else
				print '<td align="right" width="120"></td>';
		
		if($classname=='Deplacement')
			print '<td align="right" width="120">'.$langs->trans("Gasto").'</td>';
		else
			print '<td align="center" width="120">'.$langs->trans("Costo").'</td>';

        print '<td class="" width="200">Cotización</td>';

        print '<td class="">Valor</td>';

		print '<td align="right" width="200">'.$langs->trans("Status").'</td>';
		print '<td class=""></td>';
		print '</tr>';
		
		$elementarray = $project->get_element_list($key);
		if (count($elementarray)>0 && is_array($elementarray))
		{
			$var=true;
			$total_ht = 0;
			$total_ttc = 0;
			$total_cost=0;
			$num=count($elementarray);
			for ($i = 0; $i < $num; $i++)
			{
				$element = new $classname($db);

				$element->fetch($elementarray[$i]);

				$element->fetch_thirdparty();
				//print $classname;

				$var=!$var;
				print "<tr $bc[$var]>";

				// Ref
				print '<td align="left" nowrap>';
				print $element->getNomUrl(1);
				print "</td>\n";

				// Date
				$date=$element->date;
				if (empty($date)) $date=$element->datep;
				if (empty($date)) $date=$element->date_contrat;
				if (empty($date)) $date=$element->datev; //Fiche inter
				if (empty($date)) $date=$element->expiration_date;
				print '<td align="center">'.dol_print_date($date,'day').'</td>';

				// Third party
                print '<td align="left">';
                if (is_object($element->client)) print $element->client->getNomUrl(1,'',48);
				print '</td>';

			
                print '<td align="right">'.$element->fk_currency.'</td>';

                // Amount
				if (empty($value['disableamount'])) print '<td align="right">'.(isset($element->total_ht)?price($element->total_ht):'&nbsp;').'</td>';


				
				//FEDE
				$rate=$element->getRate($elementarray[$i],$conf->currency);
				/*
				$resql=$db->query("SELECT rate FROM llx_currency_conversion a join llx_deplacement b on a.source=b.fk_currency and a.target='ARS' where rowid=".$elementarray[$i]." order by date desc");
				if($conv = $db->fetch_object($resql))
				{
					$rate=$conv->rate;
				}
				*/
				
				
				// Amount
				//if (empty($value['disableamount'])) print '<td align="right">'.(isset($element->total_ttc)?price($element->total_ttc*$rate,0,'',0,2,2):'&nbsp;').'</td>';

					// Amount
				if (empty($value['disableamount'])){
					 print '<td align="right">';
						if(isset($element->total_ttc)){
							$view_tcc=true;
							echo price($element->total_ttc*$rate,0,'',0,2,2);
							if(!array_key_exists($element->fk_currency,$arrayCurrencys)){
								$arrayCurrencys[$element->fk_currency]['tcc']=$element->total_ttc*$rate;
							}else{
								$arrayCurrencys[$element->fk_currency]['tcc']= $arrayCurrencys[$element->fk_currency]['tcc']+($element->total_ttc*$rate);
							}	

						}else{
							print '&nbsp;';
						}
					print'</td>';
					
					};

					print '<td align="right">'.(isset($element->cost)?price($element->cost*$rate,0,'',0,2,2):'&nbsp;').'</td>';

               // if($element->fk_currency!='USD') {
					$fecha_ingreso	= DateTime::createFromFormat('d/m/Y',dol_print_date($date,'day'))
									->format('Y-m-d');
                    $sqlQuery = " 
					 	SELECT  id,fecha_ingreso,divisa_origen,divisa_destino,valor_divisa_origen,valor_divisa_destino 
                        FROM " . MAIN_DB_PREFIX . "consolidation_day
                        WHERE fecha_ingreso='{$fecha_ingreso}' 
                        AND divisa_origen='{$element->fk_currency}'
                        ORDER BY fecha_ingreso DESC LIMIT 1";
                    $resqlFinal = $db->query($sqlQuery);
					if( $resqlFinal  && $db->num_rows($resqlFinal)>0){
                        $obj = $db->fetch_object($resqlFinal);
                        echo"<td  style='font-size:80%; padding-left:10px;' align='left' width='200px' >
							 <span>Cotización al dia <b>".dol_print_date($date, 'day')."</b><span><br>
							 <div id='conversion_general-{$linea}'>
								<span>Tipo: General</span><br>
								<span>
									<b>
										{$obj->divisa_origen}
									</b>
									{$obj->valor_divisa_origen} / 
									<b>
										{$obj->divisa_destino}
									</b> {$obj->valor_divisa_destino}<br>
								</span>
								<a id='boton_conversion-{$linea}' onclick='conversionManual({$linea})'><i>Modificar Cotización<i></a>
							 </div>";
                        echo "<div hidden id='conversion_manual-{$linea}'>
								<span>Tipo: Manual</span><br>
								<form method='POST' action='/projet/resultado.php?id={$projectid}'  >
									<b>
										{$obj->divisa_origen}
									</b> 
									<input name='valor_divisa_origen' class='input_nueva_conversion-{$linea}' type='text' style='width:40px;'> /
									<b>
										{$obj->divisa_destino}
									</b> 
											<input name='valor_divisa_destino' class='input_nueva_conversion-{$linea}'   type='text'  style='width:40px;'>
											<input name='divisa_origen' class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->fk_currency}' >
											<input name='salesorder_id'  class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->id}' >
									
									<br>
									<div style='margin-top: 5px; margin-left:1px;'>
										<input   onclick='cancelarConversionManual({$linea})' class=\"button\" value=\"Cancelar\" name=\"addline\" type=\"button\">
										<input class=\"button\" value=\"Aceptar\" name=\"addline\" type=\"submit\">
									</div> 
								</form>
							 </div> 							
							</td>";
					}else{
						echo "
							<td  style='font-size:80%; padding-left:10px;' align='left' width='200px' >
								<div id='conversion_general-{$linea}' >
									<a id='boton_conversion-{$linea}' onclick='conversionManual({$linea})'>
										<i>
											Cotización Manual
										<i>
									</a>
								</div>
								<div hidden id='conversion_manual-{$linea}' >
									<span>
										Cotización al dia
										<b>".dol_print_date($date, 'day')."</b>
									<span><br>
									<form method='POST' action='/projet/resultado.php?id={$projectid}'>
										<div >
												<b>
													{$element->fk_currency}
												</b>
													<input name='valor_divisa_origen' class='input_nueva_conversion-{$linea}'  type='text' style='width:40px;'>
												<b>
													USD1
												</b>
												<input name='valor_divisa_destino' class='input_nueva_conversion-{$linea}'   type='text'  style='width:40px;'>
												<input name='divisa_origen' class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->fk_currency}' >
												<input name='salesorder_id'  class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->id}' >
												<br>
											
										</div>
										<div style='margin-top: 5px; margin-left:1px;'>
										<input   onclick='cancelarConversionManual({$linea})' class='button' value='Cancelar' name='addline' type='button'>
											<input class='button' value='Aceptar' name='addline' type='submit'>
										</div> 
									</form>
								</div>  
							</td>";
					}

              //  }else{
              //      echo "<td  align='left' > - </td>";
				//}
               // $total_conversion=$element->total_ttc * $obj->valor_divisa_destino;
              //  $total_conversion=$total_conversion/$obj->valor_divisa_origen;
               // $total_conversion=price($total_conversion,0,'',0,2,2);
                echo "<td  align='left' width='120px'>
							{}
					  </td>";
				// Status
				print '<td align="right">'.$element->getLibStatut(5).'</td>';

				print '</tr>';
	
				/******************************************************************************************************************
				  Array currencys 
				*/
				if(!array_key_exists($element->fk_currency,$arrayCurrencys)){
					$arrayCurrencys[$element->fk_currency]['ht']=$element->total_ht;
					$arrayCurrencys[$element->fk_currency]['c']=$element->cost*$rate;
				}else{
					$arrayCurrencys[$element->fk_currency]['ht']= $arrayCurrencys[$element->fk_currency]['ht']+$element->total_ht;
					$arrayCurrencys[$element->fk_currency]['c']= $arrayCurrencys[$element->fk_currency]['c']+($element->cost*$rate);
				}	

				/**********************************************************************************************************************/
				$total_ht = $total_ht + $element->total_ht;
				$total_ttc = $total_ttc + $element->total_ttc*$rate;
				$total_cost= $total_cost + $element->cost*$rate;
                $linea++;
			}
            $linea;
		//	print_r($arrayCurrencys);
			$total[$value['class']]=$total_ttc;
			$costo[$value['class']]=$total_cost;
			$importeTotales[$title]['currencies']=$arrayCurrencys;
				/*
			************************************************************************************
			Subtotal Base imponible	y Importe total	por moneda
			*/ 
			foreach ($arrayCurrencys as $divisa => $arrayTotales)	{
				print '<tr class="liste_total">';
				print '<td>Total</td>';
				print '<td>&nbsp;</td>';
				print '<td>&nbsp;</td>';
				print '<td>&nbsp;</td>';
				if(isset($arrayTotales['ht'])){
					print '<td align="right" title="Importe"><b><I>'.$divisa.' '.price($arrayTotales['ht']).'</I></b></td>';
				}else{
					print '<td>&nbsp;</td>';
				}				
				if(isset($arrayTotales['tcc']) && 	$view_tcc ){
					print '<td align="right" title="venta"><b><I> '.$divisa.' '.price($arrayTotales['tcc']).'</I></b></td>';
				}else{
					print '<td>&nbsp;</td>';
				}
				if(isset($arrayTotales['c'])){											
					print '<td align="right" title="Gasto"><b><I> '.$divisa.' '.price($arrayTotales['c'],0,'',0,2,2).'</I></b></td>';
				}else{
					print '<td>&nbsp;</td>';
				}					
				print '<td>&nbsp;</td>';
			
				print '<td></td>';
                print '<td></td>';
                print '<td></td>';
	
				print '</tr>';
			}	$view_tcc=false;
			print '</tr>';
		
			$arrayCurrencys=array();
			/*************************************************************************************/
			/*
			if (empty($value['disableamount']))	print '<td align="right"  width="100">'.$langs->trans("Total").' : '.price($total_ht).'</td>';
					
			print '<td></td>';
			
			if (empty($value['disableamount']))
				if($value['class']=='Salesorder')
					print '<td align="right" width="100">'.$langs->trans("TotalTTC").' : '.price($total_ttc,0,'',0,2,2).'</td>';
				else
					print '<td align="right" width="100"></td>';
					
			print '<td align="right" width="100">'.$langs->trans("Total").' : '.price($total_cost,0,'',0,2,2).'</td>';
			print '<td>&nbsp;</td>';
			print '</tr>';
			*/
		}
		print "</table>";


		/*
		 * Barre d'action
		 */
		print '<div class="tabsAction">';

		if ($project->statut > 0)
		{
			if ($project->societe->prospect || $project->societe->client)
			{
				if ($key == 'propal' && ! empty($conf->propal->enabled) && $user->rights->propale->creer)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/comm/addpropal.php?socid='.$project->societe->id.'&amp;action=create&amp;origin='.$project->element.'&amp;originid='.$project->id.'">'.$langs->trans("AddProp").'</a>';
				}
				if ($key == 'order' && ! empty($conf->commande->enabled) && $user->rights->commande->creer)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/commande/fiche.php?socid='.$project->societe->id.'&amp;action=create&amp;origin='.$project->element.'&amp;originid='.$project->id.'">'.$langs->trans("AddCustomerOrder").'</a>';
				}
				if ($key == 'invoice' && ! empty($conf->facture->enabled) && $user->rights->facture->creer)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/compta/facture/list.php?socid='.$project->societe->id.'&amp;action=create&amp;origin='.$project->element.'&amp;originid='.$project->id.'">'.$langs->trans("AddCustomerInvoice").'</a>';
				}
			}
			if ($project->societe->fournisseur)
			{
				if ($key == 'order_supplier' && ! empty($conf->fournisseur->enabled) && $user->rights->fournisseur->commande->creer)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/fourn/facture/fiche.php?socid='.$project->societe->id.'&amp;action=create&amp;origin='.$project->element.'&amp;originid='.$project->id.'">'.$langs->trans("AddSupplierInvoice").'</a>';
				}
				if ($key == 'invoice_supplier' && ! empty($conf->fournisseur->enabled) && $user->rights->fournisseur->facture->creer)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/fourn/commande/fiche.php?socid='.$project->societe->id.'&amp;action=create&amp;origin='.$project->element.'&amp;originid='.$project->id.'">'.$langs->trans("AddSupplierOrder").'</a>';
				}
			}
		}

		print '</div>';

	}
}
/*
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td width="90%">Resultado</td>';
print '<td width="10%" align="center"></td>';
print '<td></td>';
print '</tr>';
print '<tr>';
print '<td width="90%">Resultado</td>';
//print '<td width="10%" align="right">'.price($total['Salesorder']-$costo['Salesorder']-$costo['Deplacement']-$costo['Policy'],0,'',0,2,2).'</td>';
print '<td></td>';
print '</tr>';
print "</table>";

*/
echo "<h4>Totales consolidadios en la divisa {$moneda_consolidada}</h4>";
print '<table class="noborder" width="100%">
<thead  >
	<tr class="liste_titre">
		<th >Titulo</th>
		<th align=center >Divisa Consolidada</th>
		<th  align=right>Importe</th>
		<th  align=right>Costo</th>
	</tr>
</thead>
<tbody>';


$arr_result=array();
foreach ($importeTotales as $title => $currencies) {
	print '<tr>';
	print '<td>';
	print_titre($langs->trans($title)); ;
	print '</td>';
	print '<td align=center>';
	print  $moneda_consolidada ;
	print '</td>';	
	$sum=0;
	$sum_c=0;
	$sum_c_haber=0;
	$sum_haber=0;
	$result_sum=0;
	$resul_c=0;
	
        if( isset($currencies) && array_key_exists('currencies', $currencies)) {
            foreach($currencies['currencies'] as $currency=>$arrayValues){

            /***********************************************************************************
                    the  currencies values of project
            */
                    $sql="	SELECT VALUE,CURRENCY_CONVERTION_VALUE 
                                    FROM  llx_consolidation_detail
                                    WHERE FK_PROJET ={$project->id} ";
                    $sql.=" AND FK_CURRENCY ='".$currency."'";
                    $value_currency_projet=$db->query($sql);	
                    $value_currency_projet=$db->fetch_object($value_currency_projet);
                    if(empty($value_currency_projet->VALUE)){
                            $valor_divsa_original=1;
                    }else{
                            $valor_divsa_original=$value_currency_projet->VALUE;
                    }

                            $sum_c+=$arrayValues['c']*$value_currency_projet->CURRENCY_CONVERTION_VALUE / $valor_divsa_original;
                            $sum+=$arrayValues['ht']*$value_currency_projet->CURRENCY_CONVERTION_VALUE /  $valor_divsa_original;

            }            
        }


	print '<td  align=right>';
	print price($sum,0,'',0,2,2);
	print '</td>';
	print '<td   align=right>';
	print price($sum_c,0,'',0,2,2);
	print '</td>';


					
	$arr_result[$currencies['operation']]['c']+=$sum_c;
	$arr_result[$currencies['operation']]['ht']+=$sum;
	
}
	print '</tr>';

/*	if($arr_result['+']['c']> 0){
			$tt_c=$arr_result['+']['c']-$arr_result['-']['c'] ;
	}else{
		$tt_c=$arr_result['-']['c'];
	}
*/

	$tt_c=$arr_result['+']['c']-$arr_result['-']['c'] ;
	$tt_ht=$arr_result['+']['ht']-$arr_result['-']['ht'] ;
	print '<tr>';
	print '</tr>';
	print '<tr>';	
	print '<td>
			<I><b>
				Total
			</I></b>';
	print '</td>';
	print '<td  align=center>';
		print 	 '<I><b>';
			echo $moneda_consolidacion;	
		print 	 '<I><b>';			
	print '</td>';	
	print '<td  align=right>';
	print 	 '<I><b>';
			print price($tt_ht,0,'',0,2,2);
	print 	 '</I></b>';
	print '</td>';
	print '<td align=right>';
	print 	 '<I><b>';
			print price($tt_c,0,'',0,2,2);
	print 	 '</I></b>';
	print '</td>';
	print '</tr>';	

//print '<td width="10%" align="right">'.price($total['Salesorder']-$costo['Salesorder']-$costo['Deplacement']-$costo['Policy'],0,'',0,2,2).'</td>';

print '<tbody>';
print "</table>";

llxFooter();

$db->close();





?>


