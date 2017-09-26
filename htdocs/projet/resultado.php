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
		foreach ($arrDivisas as $divisa => $valor_consolidado) {
			$sql='';
			$sql.="INSERT INTO ".MAIN_DB_PREFIX."consolidation_detail (fk_projet, fk_currency, value)";
			$sql.= " VALUES (".$projectid.",'".$divisa."',".$valor_consolidado.")";
			$sql.= " ON DUPLICATE KEY UPDATE VALUE=". $valor_consolidado .";";
			$resql = $db->query($sql);			
		}
 	}

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
		</style>';
/*
	end style of the table form	
	**************************************************************************************************++
*/

/*
	**************************************************************************************************++
	button convention
*/

	print '<a class="butAction" onclick="abrirForm()"  id="cotization">Seleccionar moneda de consolidación</a><br>';

/*
	end button convention
	**************************************************************************************************++
*/

/*
	**************************************************************************************************++
	form convention
*/

print '
	<div  class="tabBar div_convention"  id="conf_consolidation">
		<form action="/projet/resultado.php?id='.$project->id.'" class="convention_form"  method="post">
		<input type="hidden" name="consolidation" value="1">
			<label> Divisa de Consolidación :</label>
			<select class="seleccion_de_divisa" name="seleccion_de_divisa" >';
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
					SELECT FK_CURRENCY,VALUE 
					FROM  llx_consolidation_detail
					WHERE FK_PROJET ={$project->id}
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
						<th>Divisa</th>
						<th>Valor de Coversión</th>
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
										<td>{$objp->FK_CURRENCY}</td>
										<td>
											<input type='NUMBER' step='any' name='divisas[{$objp->FK_CURRENCY}]' value="; 
												 if(!empty($objp->VALUE)){ echo number_format($objp->VALUE, 2, '.', ' ');}
											 echo" required>
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


/*
 * Referers types
 */

$listofreferent=array(
'salesorder'=>array(
	'title'=>"Listado de Ordenes de Venta asociadas al proyecto",
	'class'=>'Salesorder',
	'test'=>$conf->salesorder->enabled),
'trip'=>array(
	'title'=>"ListTripAssociatedProject",
	'class'=>'Deplacement',
	'test'=>$conf->deplacement->enabled),
'policy'=>array(
	'title'=>"Listado de pólizas asociadas al proyecto",
	'class'=>'Policy',
	'test'=>$conf->deplacement->enabled)
);
$arrayCurrencys = array();
foreach ($listofreferent as $key => $value)
{
	$title=$value['title'];
	$classname=$value['class'];
	$qualified=$value['test'];
	if ($qualified)
	{
		print '<br>';

		print_titre($langs->trans($title));
		print '<table class="noborder" width="100%">';

		print '<tr class="liste_titre">';
		print '<td width="100">'.$langs->trans("Ref").'</td>';
		print '<td width="100" align="center">'.$langs->trans("Date").'</td>';
		print '<td>'.$langs->trans("ThirdParty").'</td>';
		if (empty($value['disableamount'])) print '<td align="right" width="120">'.$langs->trans("Importe").'</td>';
		//FEDE
		print '<td align="right" width="120">'.$langs->trans("Divisa").'</td>';
		//FIN FEDE

		if (empty($value['disableamount']))
			if($classname=='Salesorder')
				print '<td align="right" width="120">'.$langs->trans("Venta").'</td>';
			else
				print '<td align="right" width="120"></td>';
		
		if($classname=='Deplacement')
			print '<td align="right" width="120">'.$langs->trans("Gasto").'</td>';
		else
			print '<td align="right" width="120">'.$langs->trans("Costo").'</td>';
		
		
		print '<td align="right" width="200">'.$langs->trans("Status").'</td>';
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

                // Amount
				if (empty($value['disableamount'])) print '<td align="right">'.(isset($element->total_ht)?price($element->total_ht):'&nbsp;').'</td>';

				
                print '<td align="right">'.$element->fk_currency.'</td>';
				
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
				if (empty($value['disableamount'])) print '<td align="right">'.(isset($element->total_ttc)?price($element->total_ttc*$rate,0,'',0,2,2):'&nbsp;').'</td>';
				
					print '<td align="right">'.(isset($element->cost)?price($element->cost*$rate,0,'',0,2,2):'&nbsp;').'</td>';
				
				// Status
				print '<td align="right">'.$element->getLibStatut(5).'</td>';

				print '</tr>';
	
				/******************************************************************************************************************
				  Array currencys 
				*/
				if(!array_key_exists($element->fk_currency,$arrayCurrencys)){
					$arrayCurrencys[$element->fk_currency]['ht']=$element->total_ht;
					$arrayCurrencys[$element->fk_currency]['c']=$element->cost;
				}else{
					$arrayCurrencys[$element->fk_currency]['ht']= $arrayCurrencys[$element->fk_currency]['ht']+$element->total_ht;
					$arrayCurrencys[$element->fk_currency]['c']= $arrayCurrencys[$element->fk_currency]['c']+$element->cost;
				}	
				/**********************************************************************************************************************/
				$total_ht = $total_ht + $element->total_ht;
				$total_ttc = $total_ttc + $element->total_ttc*$rate;
				$total_cost= $total_cost + $element->cost*$rate;
			}
		//	print_r($arrayCurrencys);
			$total[$value['class']]=$total_ttc;
			$costo[$value['class']]=$total_cost;
			
				/*
			************************************************************************************
			Subtotal Base imponible	y Importe total	por moneda
			*/ 
			foreach ($arrayCurrencys as $divisa => $arrayTotales)	{
				print '<tr class="liste_total">';
				print '<td>Total</td>';
				
				
				
				foreach ($arrayTotales as $tipo_total => $total)	{
					if($tipo_total==='ht'){
						print '<td>&nbsp;</td>';
						print '<td>&nbsp;</td>';
						print '<td align="right" title="Gasto"><b><I>'.$divisa.' '.price($total).'</I></b></td>';
						print '<td>&nbsp;</td>';
					}else{
						print '<td>&nbsp;</td>';
						print '<td>&nbsp;</td>';
						//print '<td align="right" title="Importe"><b><I> '.$divisa.' '.price($total).'</I></b></td>';
					}
				
				}
				print '<td></td>';
				print '<td>&nbsp;</td>';
				print '<td> </td>';
				print '<td> </td>';
				print '</tr>';
			}
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
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td width="90%">Resultado</td>';
print '<td width="10%" align="center"></td>';
print '<td></td>';
print '</tr>';
print '<tr>';
print '<td width="90%">Resultado</td>';
print '<td width="10%" align="right">'.price($total['Salesorder']-$costo['Salesorder']-$costo['Deplacement']-$costo['Policy'],0,'',0,2,2).'</td>';
print '<td></td>';
print '</tr>';
print "</table>";

llxFooter();

$db->close();





?>


