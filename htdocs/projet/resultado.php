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
echo "
<style>
	 
	  a {
		font-size:1em;
	 }
	 
	  table {
		font-size:1em;
	 }
	</style>
";

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

	
		
 	function checkEntity(t){
		console.log(t);
		
		var id_domain				= t.context.dataset["entity_id"];
	  	var consolidationDomain_id	= t.context.dataset["consolidationdomain"];
		var id_project				= t.context.dataset["pj"];
		var domainName				= t.context.dataset["domain"];
		var domain					= 0;
		if ($(t[0]).is(":checked"))
		{
		    domain=1;
		   console.log("cecked");
		} 
		

        
/*        
        var id_project = 178; 
        var domain="salesorder";
        var id_domain=208;
        var consolidationDomain_id=1;
*/        
		function url_redirect(options){
			 var $form = $("<form />");
			 
			 $form.attr("action",options.url);
			 $form.attr("method",options.method);
			 
			 for (var data in options.data)
			 $form.append(\'<input type="hidden" name="\'+data+\'" value="\'+options.data[data]+\'" />\');
			  
			 $("body").append($form);
			 $form.submit();
		}
		 
		$(function(){
			/*jquery statements */
			url_redirect({url: "'.DOL_URL_ROOT.'/projet/resultado.php?id="+id_project,
			  method: "post",
			  data: {
			    	"domainName":domainName,
			    	"id_domain":id_domain,
			    	"consolidationDomain_id":consolidationDomain_id,
			    	"domain":domain
			  		}
			 });
		});
				
                            	
	}
</script>';
/*************************************************************************/

 /**************************************************************************************************************/

/*************************************************************************************************************
INSERT INTO llx_consolidation_domain

 */
if(!empty($_POST['domainName'])) {
    $date_domain 			= new DateTime();
    $fecha_ingreso_domain 	= date_format($date_domain, 'Y-m-d');
    $entitiDomain			= $_POST['domainName'];
    $domain					= $_POST['domain'];
    $id_domain 				= $_POST['id_domain'];
    $consolidationDomain_id = $_POST['consolidationDomain_id'];
    if(empty($consolidationDomain_id) ) {
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "consolidation_domain_" . $entitiDomain . " (entidad_id,fecha_ingreso,domain)";
        $sql .= " VALUES (" . $id_domain . ",'" . $fecha_ingreso_domain . "','" . $domain . "')";
    }else{
		$sql = "INSERT INTO " . MAIN_DB_PREFIX . "consolidation_domain_" . $entitiDomain . " (id,entidad_id,fecha_ingreso)";
        $sql .= " VALUES (".$consolidationDomain_id .",". $id_domain . ",'" . $fecha_ingreso_domain . "')";
	}
	if(!empty( $consolidationDomain_id)){
        $sql.= " ON DUPLICATE KEY UPDATE domain=".$domain.",fecha_ingreso='".$fecha_ingreso_domain."';";
	}else{
        $sql.=";";
	}
    $resql = $db->query($sql);
}




/*************************************************************************************************************
INSERT INTO llx_consolidation_(dinamyc entity)

 */

if(!empty($_POST['entidad_id']) &&  empty($_POST['resetTipo']) ){
    $divisa_origen 					= $_POST['divisa_origen'];
    $valor_divisa_origen_format_dot	= empty($_POST['valor_divisa_origen'])  ? 1 : str_replace(".", "", $_POST['valor_divisa_origen']);
    $valor_divisa_origen			= empty($_POST['valor_divisa_origen'])  ? 1 : str_replace(",", ".",$valor_divisa_origen_format_dot);
    $valor_divisa_destino_format_dot= empty($_POST['valor_divisa_destino'])  ? 1 : str_replace(".", "", $_POST['valor_divisa_destino']);
    $valor_divisa_destino			= empty($_POST['valor_divisa_destino'])  ? 1 : str_replace(",", ".",$valor_divisa_destino_format_dot);
    $entidad_id						= $_POST['entidad_id'];
    $linea							= $_POST['linea'];
    $fi_aux							= "fecha_ingreso-".$linea;
    $f_format					 	= DateTime::createFromFormat('d/m/Y',$_POST[$fi_aux]);
    $fecha_i						= $f_format->format('Y-m-d');
    $id_consolidation				= $_POST['id_consolidation'];
    $entidad						= $_POST['entidad'];

	if(isset($id_consolidation) && !empty( $id_consolidation)){
		$sql ="INSERT INTO ".MAIN_DB_PREFIX."consolidation_".$entidad." (id,".$entidad."_id,fecha_ingreso,divisa_origen,valor_divisa_origen,valor_divisa_destino)";
		$sql.= " VALUES (".$id_consolidation.",".$entidad_id.",'".$fecha_i."','".$divisa_origen."',".$valor_divisa_origen.",'".$valor_divisa_destino."')";
	}else{
		$sql ="INSERT INTO ".MAIN_DB_PREFIX."consolidation_".$entidad." (".$entidad."_id,fecha_ingreso,divisa_origen,valor_divisa_origen,valor_divisa_destino)";
		$sql.= " VALUES (".$entidad_id.",'".$fecha_i."','".$divisa_origen."',".$valor_divisa_origen.",'".$valor_divisa_destino."')";
	}
	if(isset($id_consolidation) && !empty( $id_consolidation)){
        $sql.= " ON DUPLICATE KEY UPDATE valor_divisa_origen=".$valor_divisa_origen.",valor_divisa_destino='".$valor_divisa_destino."',fecha_ingreso='".$fecha_i."';";
	}else{
        $sql.=";";
	}
	$resql = $db->query($sql);

}else if(!empty($_POST['resetTipo'])){
	//Elimina la cotizacion cargada para una tupla
    $entidad= $_POST['entidad'];
    $id_consolidation= $_POST['id_consolidation'];
    $sql ="DELETE FROM ".MAIN_DB_PREFIX. "consolidation_".$entidad."  WHERE id={$id_consolidation}";
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

echo
	"<script>
		function conversionManual(linea){

		    var id_conversion_general='#conversion_general-'+linea;
			var id_conversion_manual ='#conversion_manual-'+linea;
		    $(id_conversion_general).hide();
		    $(id_conversion_manual).show();
		 	
		}
		function cancelarConversionManual(linea){
			var id_conversion_general='#conversion_general-'+linea;
			var id_conversion_manual ='#conversion_manual-'+linea;
			var id_input_nueva_conversion='.input_nueva_conversion-'+linea;
		    $(id_conversion_general).show();
		    $(id_conversion_manual).hide();
		    $(id_input_nueva_conversion).prop('required',false); 
		    $(id_input_nueva_conversion).val('');
		    
		}	  
		function resetTipoGeneral(linea){
			var id_conversion_general='#conversion_general-'+linea;
			var id_conversion_manual ='#conversion_manual-'+linea;
			var id_input_nueva_conversion='.input_nueva_conversion-'+linea;	
			var id_resetTipoGeneral ='#resetTipoGeneral-'+linea;
			var id_form='#form-'+linea;
		    $(id_input_nueva_conversion).val('');
		    $(id_resetTipoGeneral).val('1');		    
		    $(id_input_nueva_conversion).attr('required',false);  
		 	$(id_form).submit(); 
		}			
		$( document ).ready(function() {
	
	
			/*valida que los campos valor_unitario y valor_total de detalle pedido sean numericos pero permitan ingresar coma*/
			$(document).on('keydown', '.input_only_number', function(e){
				 -1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190,188])||(/65|67|86|88/.test(e.keyCode)&&(e.ctrlKey===true||e.metaKey===true))&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()
			});
		
			/*controla el ingreso de datos con formato ###.###,## para los campos valor_unitario y valor_total de detalle pedido */
			$(document).on('keyup', '.input_only_number', function () {
				$(this).val( numberFormat($(this).val() )  );
			
			});
		
			function numberFormat(numero){
				// Variable que contendra el resultado final
				var resultado = '';
		
				// Si el numero empieza por el valor \"-\" (numero negativo)
				if(numero[0]=='-')
				{
					// Cogemos el numero eliminando los posibles puntos que tenga, y sin
					// el signo negativo
					nuevoNumero=numero.replace(/\./g,'').substring(1);
		
				}else{
					// Cogemos el numero eliminando los posibles puntos que tenga
					nuevoNumero=numero.replace(/\./g,'');
				}
				// Si tiene decimales, se los quitamos al numero
				if(numero.indexOf(',')>=0)
					nuevoNumero=nuevoNumero.substring(0,nuevoNumero.indexOf(','));
		
				// Ponemos un punto cada 3 caracteres
				for (var j, i = nuevoNumero.length - 1, j = 0; i >= 0; i--, j++)
		
					resultado = nuevoNumero.charAt(i) + ((j > 0) && (j % 3 == 0)? '.': '') + resultado;
				// Si tiene decimales, se lo añadimos al numero una vez forateado con 
				// los separadores de miles
				if(numero.indexOf(',')>=0)
					resultado+=numero.substring(numero.indexOf(','));
				if(numero[0]=='-')
				{
					// Devolvemos el valor añadiendo al inicio el signo negativo
					return '-'+resultado;
				}else{
					return resultado;
				}
			}		 	
		});	
	</script>";


/*
 * Referers types
 */

$listofreferent=array(
    'invoice'=>array(
        'title'=>"Facturas",
        'class'=>'facture',
        'test'=>$conf->facture->enabled,
        'entity_table'=>'facture',
        'operation'=>'+',
		'searchDomain'=>1,
		'defaultDomain'=>0,
		'entityFather'=>'salesorder'
		),
	'salesorder'=>array(
		'title'=>"Listado de Ordenes de Venta asociadas al proyecto",
		'class'=>'Salesorder',
		'test'=>$conf->salesorder->enabled,
        'entity_table'=>'salesorder',
		'operation'=>'+',
		'searchDomain'=>1,
        'defaultDomain'=>1),
    'order_supplier'=>array(
        'title'=>"ListSupplierOrdersAssociatedProject",
        'class'=>'CommandeFournisseur',
        'entity_table'=>'commande_fournisseur',
        'test'=>$conf->fournisseur->enabled,
        'operation'=>'-',
        'searchDomain'=>0),
	'trip'=>array(
		'title'=>"ListTripAssociatedProject",
		'class'=>'Deplacement',
        'entity_table'=>'deplacement',
		'test'=>$conf->deplacement->enabled,
		'operation'=>'-',
        'searchDomain'=>0),

/*
'policy'=>array(
	'title'=>"Listado de pólizas asociadas al proyecto",
	'class'=>'Policy',
	'test'=>$conf->deplacement->enabled,
	'operation'=>'-'),

'Resultado'=>array(
	'title'=>"Totales",
	'class'=>'Policy',
	'test'=>$conf->deplacement->enabled)
*/
);


$arrayCurrencys = array();
$importeTotales = array();
$linea=0;
foreach ($listofreferent as $key => $value)
{
	$title=$value['title'];
	$operation=$value['operation'];
	$classname=strtolower($value['class']);
    $entidadName=strtolower($value[entity_table]);
	$qualified=$value['test'];
	$importeTotales[$title]['operation']=$operation;

	$view_tcc=false;
	if ($qualified)
	{
		print '<br>';

		print_titre($langs->trans($title));
		print '<table class="noborder" width="100%" >';

		echo "<tr class='liste_titre' >";
		print '<td width="100">'.$langs->trans("Ref").'</td>';
		print '<td width="100" align="center">'.$langs->trans("Date").'</td>';
		print '<td width="500">'.$langs->trans("ThirdParty").'</td>';
		//FEDE
		print '<td align="center" width="50">'.$langs->trans("Divisa").'</td>';
		
		if (empty($value['disableamount'])) print '<td align="center" width="150">'.$langs->trans("Importe").'</td>';

		//FIN FEDE

		/*if (empty($value['disableamount']))
			if($classname=='Salesorder')
				print '<td align="center" width="150">'.$langs->trans("Importe Venta (+IVA) ").'</td>';
			else
				print '<td align="right" width="150"></td>';
		*/
		/*if($classname=='Deplacement')
			print '<td align="right" width="120">'.$langs->trans("Gasto").'</td>';
		else
			print '<td align="center" width="120">'.$langs->trans("Costo").'</td>';
		*/
        print '<td class=""  align="center" width="300">Cotización</td>';

        print '<td  width="180" >(USD) Importe</td>';

		print '<td align="center" width="200">'.$langs->trans("Status").'</td>';
		print '<td align="center" width="10">Priorizar</td>';
		print '</tr>';
		
		$elementarray = $project->get_element_list($key);

		if (count($elementarray)>0 && is_array($elementarray))
		{
			$var=true;
			$total_ht = 0;
			$total_ttc = 0;
			$total_cost=0;
            $total_tcccot=0;
			$num=count($elementarray);
            $styleTr= "style=''";
			for ($i = 0; $i < $num; $i++)
			{
				$element = new $classname($db);
				$element->fetch($elementarray[$i]);
				$element->fetch_thirdparty();

                /*****************************************************************************************************************************************************
                 *Para el caso de facturas y ordenes de venta, busca cual es la entidad que domina o se priorizara. Si el total del valor de las facturas es > que el total de la o.v entonces domina la factura
                 *
                 */

                //configuraciones del reporte
                $dom= $_POST['domain'];
                $searchDomain=$value['searchDomain'];
                if($searchDomain===1){
                    $defaultDomain=$value['defaultDomain'];
                    if($defaultDomain==0){
						$entityFather= $value['entityFather'];
					}
                }else{
                    $defaultDomain=-1;
                }

                $retSearchDomain=0;
                //si se permite la busqueda de que entidad domina o se priorizara
                if($searchDomain){
                    $sqlSearchDomain = " 
						SELECT *
						FROM   vw_salesorder_facture_cotizacion 
						where  {$entidadName}_rowid = {$element->id} limit 1;";
                    $domainEntidad="domain_".$entidadName;

                    $sqlSearchDomain = $db->query($sqlSearchDomain);
                    $retSearchDomain = $db->fetch_object($sqlSearchDomain);
                    $domain= $retSearchDomain->$domainEntidad;

                    //si no es la entidad que domina o es priorizada por defecto entinces:
					if($defaultDomain==0){
						$entityNameConsolidationDomain=$entityFather;
                        $domainEntidad_id ="{$entityNameConsolidationDomain}_rowid";
					}else{
                        $entityNameConsolidationDomain=$entidadName;
                        $domainEntidad_id="{$entidadName}_rowid";
					}
                    $domain_id= $retSearchDomain->$domainEntidad_id;


					// verifica si hay una restriccion de dominio o priorizacion configurada.
					// Si es asi existira una tupla en la tabla   llx_consolidation_domain_entityNameConsolidationDomain
                    $sqlsetDomain = " 
						SELECT case 
								   when count(*) >= 1  then 1 
								   when count(*) < 1  then 0
							   end as exist,
							   case 
								   when count(*) >= 1  then domain
								   when count(*) < 1  then 1
							   end as domain,
							   id							   							   
						FROM   llx_consolidation_domain_{$entityNameConsolidationDomain} 
						where  entidad_id = {$domain_id}
						group by  domain,id limit 1;";

                    $sqlsetDomain 				= $db->query($sqlsetDomain);
                    $retSqlsetDomain 			= $db->fetch_object($sqlsetDomain);
                    $entityFatherDomainExists	= !isset($retSqlsetDomain->exist)  ?  0  : $retSqlsetDomain->exist;
                    $entityFatherDomain			= !isset($retSqlsetDomain->domain) ?  1  : $retSqlsetDomain->domain ;
                    $entityFatherDomainId 		= !isset($retSqlsetDomain->id) 	   ? ''  : $retSqlsetDomain->id ;

					// si no es la entidad que domina o se priorizo por defecto
                    if($defaultDomain==0){
                       if ( ($domain==0  && $entityFatherDomainExists==0) || ($entityFatherDomainExists==1 && $entityFatherDomain==1)  ){
                           $styleTr		= "style='background-color: #8C9CAB'";
                           $esCotizable	=0;
                       } else{
                           $styleTr= "style=''";
                           $esCotizable=1;
                       }
                    }

                    // si es la entidad que domina o se priorizo por defecto
                    if($defaultDomain==1){
                        if ( ($domain==0 && $entityFatherDomainExists==0 ) ||  ($entityFatherDomainExists==1 && $entityFatherDomain==0) ){
                            $styleTr			= "style='background-color: #8C9CAB'";
                            $esCotizable		= 0;
                        } else{
                            $styleTr			= "style=''";
                            $esCotizable		=	1;

                        }
                    }
                $sqlDimainNames = "
                    SELECT *
                    FROM   vw_salesorder_facture_cotizacion_priorizada 
                    where  {$entityNameConsolidationDomain}_rowid = {$domain_id};";
                $sqlDimainNames		= $db->query($sqlDimainNames);
                $arryNameEntity		= array();
 
                $i_aux_names=0;
                $continue=true;
                while($sqlDimainNames && $db->num_rows($sqlDimainNames)>$i_aux_names && $continue){
                    $ob  = $db->fetch_object($sqlDimainNames);                  
                    $name= $ob->name_domain;                    
                    $arryNameEntity[]=$name;                      
                    if($ob->domain==1){    
                        $continue=false;  
                    }                    
                    $i_aux_names++;   
                }
                    
                    
                }



              
                /****************************************************************************************************************************************************/








                //print $classname;

				$var=!$var;
				echo "<tr $bc[$var] {$styleTr}>";

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

			
                print '<td  align="center" >'.$element->fk_currency.'</td>';

                // Amount
				if (empty($value['disableamount'])) print "<td align='right' style='padding-left:5px;'>".(isset($element->total_ht)?price($element->total_ht):'&nbsp;')."</td>";
				
                $resqlFinal=0;
                $id_de_consolidacion_manual=false;
                //Solo si es diferente de usd y es cotizable si visualizara el form
                if($element->fk_currency!='USD' &&  $esCotizable==1){
                	//query de la entidad a realizar cotizacion
               		 $sqlQueryEntidad = " 
						SELECT 	cs.id
								,cs.fecha_ingreso
								,cs.divisa_origen
								,cs.divisa_destino
								,cs.valor_divisa_origen
								,cs.valor_divisa_destino 
								,tipo
						FROM   llx_consolidation_".$entidadName." cs 
						left join llx_".$entidadName."   on (cs.".$entidadName."_id=llx_".$entidadName.".rowid)
						where llx_".$entidadName.".rowid={$element->id} ;
					";

					$resqlEntidad = $db->query($sqlQueryEntidad);
					if( $resqlEntidad  && $db->num_rows($resqlEntidad)>0) {
                        $resqlFinal = $resqlEntidad;
                        $id_de_consolidacion_manual=true;
                    }else{
							//de no existir una cotizacin para la entidad entonces se obtiene de consolidation_day segun la fecha obtenida de la entidad
							$fecha=dol_print_date($date,'day');
							$fecha_ingreso	= DateTime::createFromFormat('d/m/Y',$fecha)
							->format('Y-m-d');
							$sqlQuery = " 
								SELECT  id
										,fecha_ingreso
										,divisa_origen
										,divisa_destino
										,valor_divisa_origen
										,valor_divisa_destino 
										,'General' as tipo
								FROM " . MAIN_DB_PREFIX . "consolidation_day
								WHERE fecha_ingreso='{$fecha_ingreso}' 
								AND divisa_origen='{$element->fk_currency}'
								ORDER BY fecha_ingreso DESC LIMIT 1";
							$resqlConsolidationDay = $db->query($sqlQuery);
							if( $resqlConsolidationDay  && $db->num_rows($resqlConsolidationDay)>0) {
								$resqlFinal = $resqlConsolidationDay;
							}else{
								//Obtiene la fecha mas cercana a la fecha de la entidad ya sea < o >
                                $sqlQueryFechaMinMax =
                                    "
										SELECT  id
												,fecha_ingreso
												,divisa_origen
												,divisa_destino
												,valor_divisa_origen
												,valor_divisa_destino 
												,'General' as tipo
										FROM llx_consolidation_day
										where exists 
											(
												SELECT idMin, idMax FROM
												 	(												
														SELECT  max(fecha_ingreso) as fMin,id as idMin	 
														FROM llx_consolidation_day
														where fecha_ingreso  < '{$fecha_ingreso}'
													) AS fmin,
													(
														SELECT  min(fecha_ingreso) as fMax,id as idMax	 
														FROM llx_consolidation_day
														where fecha_ingreso  > '{$fecha_ingreso}'
													) as fmax
												where (llx_consolidation_day.fecha_ingreso=fmin.fMin
												or   llx_consolidation_day.fecha_ingreso=fmax.fMax)
												AND divisa_origen='{$element->fk_currency}'
											
										);
									";
                                $resqlFechaMinMax = $db->query($sqlQueryFechaMinMax);
                                if($resqlFechaMinMax  && $db->num_rows($resqlFechaMinMax)){
                                    $resqlFinal= $resqlFechaMinMax;
								}
							}
					}

                    $date = new DateTime();
                    $fecha_ingreso_format_db= date_format($date, 'Y-m-d');
                    $fecha_ingreso_format_view = date_format($date, 'd/m/Y');
					if( $resqlFinal  && $db->num_rows($resqlFinal)>0){
                        //Si existe cotizacion ya sea en consolidation_day o en la tabla de cotizacion de la entidad a cotizar

                        $no_exite_cotizacion=false;
                        $obj = $db->fetch_object($resqlFinal);
                        $fecha_ingreso_format_view	= DateTime::createFromFormat('Y-m-d',$obj->fecha_ingreso)->format('d/m/Y');
                        echo"
						<td  style='font-size:80%; padding-left:10px;' align='left' width='200px' >
							 
							 <div id='conversion_general-{$linea}'>
							 <span>Cotización al dia <b>{$fecha_ingreso_format_view}</b><span><br>
								<span>Tipo: {$obj->tipo}</span><br>
								<span>
									<b>
										{$obj->divisa_origen}
									</b>
									".price($obj->valor_divisa_origen ,0,'',0,2,2)." / 
									<b>
										{$obj->divisa_destino}
									</b> 
										".price($obj->valor_divisa_destino ,0,'',0,2,2)."
									<br>
								</span>
								<a id='boton_conversion-{$linea}' onclick='conversionManual({$linea})'><i>Modificar Cotización<i></a>
							 </div>";
							echo
							"<div hidden id='conversion_manual-{$linea}'>                        					
								<form method='POST' id='form-{$linea}' action=".DOL_URL_ROOT."/projet/resultado.php?id={$projectid}  > 							
									<div style='width: 100%;padding-top:2px; margin-bottom: 2px; height: 20px; '>
										<div style='width:15%;float: left; '>												 
												<b>Fecha</b> 												 
										</div>
										<div style='width: 80%; float: left;'>											
											<b>";
												echo $form->select_date(($date_start?$date_start:''), ('fecha_ingreso-'.$linea))."</b>
										</div>
									</div>	
									<div style='width: 100%;padding-top:2px; margin-bottom: 2px; height: 20px; '>
										<div style='width:15%;float: left; '>												 
												<b>Tipo</b> 												 
										</div>
										<div style='width: 80%; float: left;'>											
											<b>";
												echo $obj->tipo."</b>
										</div>
									</div>
									<b>
										{$obj->divisa_origen}
									</b> 
									<input name='valor_divisa_origen' class='input_nueva_conversion-{$linea} input_only_number' style='width:50px;margin-left:10px;'   required> /
									<b>
										{$obj->divisa_destino}
										
									</b> 
										<input name='valor_divisa_destino' class='input_nueva_conversion-{$linea} input_only_number'  style='width:50px;'   required>
										<input name='divisa_origen' class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->fk_currency}' >
										<input name='entidad_id'  class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->id}' >
										<input name='linea' id='linea-{$linea}' value='{$linea}'  type='hidden' >											
										";
									echo $id_de_consolidacion_manual===true ? "
										<input name='id_consolidation'  class=''  type='hidden' value='{$obj->id}' >" :  "";
									echo "
										<input name='resetTipo' id='resetTipoGeneral-{$linea}' value='0'  type='hidden' >
										
										<input name='entidad' id='resetTipoGeneral-{$linea}' value='{$entidadName}'  type='hidden' >
											
									
									<br>
									<div style='margin-top: 5px; margin-left:1px;'>
											<input class=\"button\" value=\"Aceptar\" name=\"addline\" type=\"submit\">
											<input   onclick='cancelarConversionManual({$linea})' class='button' value='Cancelar' name='addline' type='button'>
											<input  onclick='resetTipoGeneral({$linea})' class='button' value='General' name='addline' type='button'>
									</div> 
								</form>
							 </div> 							
						</td>";
                        $no_exite_cotizacion=false;
					}else{
						//Si no existe cotizacion entonces por defecto se mostrara este formulario

                        $no_exite_cotizacion=true;
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
									<form method='POST' action=".DOL_URL_ROOT."/projet/resultado.php?id={$projectid}>
										<div style='width: 100%;padding-top:2px; margin-bottom: 2px; height: 20px; '>
											<div style='width:15%;float: left; '>												 
													<b>Fecha</b> 												 
											</div>
											<div style='width: 80%; float: left;'>";
											echo
												"<b>". $form->select_date(($date_start?$date_start:''), ('fecha_ingreso-'.$linea))."</b>
											</div>
										</div>
										<div>
											<b>
												{$element->fk_currency}
											</b>
												<input name='valor_divisa_origen' class='input_nueva_conversion-{$linea}'   style='width:50px;margin-left: 10px;' type='NUMBER'  step='.01' required>
											<b>
												USD
											</b>
												<input name='valor_divisa_destino' class='input_nueva_conversion-{$linea}'  style='width:50px;'  type='NUMBER' step='any' required>
												<input name='divisa_origen' class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->fk_currency}' >
												<input name='entidad_id'  class='input_nueva_conversion-{$linea}'  type='hidden' value='{$element->id}' >																																
												<input name='entidad' id='resetTipoGeneral-{$linea}' value='{$entidadName}'  type='hidden' >
												<input name='linea' id='linea-{$linea}' value='{$linea}'  type='hidden' >						
											<br>
											
										</div>
										<div style='margin-top: 5px; margin-left:1px;'>
												<input class='button' value='Aceptar' name='addline' type='submit'>
												<input   onclick='cancelarConversionManual({$linea})' class='button' value='Cancelar' name='addline' type='button'>
										</div> 
									</form>
								</div>  
							</td>";
					}

                }else{
                    if($searchDomain===1 and $esCotizable==0){                        
                        echo "<td  align='center' ><i>Se tomará la cotización de <b>".implode(",", $arryNameEntity)."</b></i></td>";                                            
                    }else{
                         echo "<td  align='center' > - </td>";
                    }
                    
                    $obj='';
                }

				if(!empty($element->total_ht) and !empty($obj->valor_divisa_destino) and $element->fk_currency<>$obj->divisa_destino and $no_exite_cotizacion===false){
                    $total_conversion=$element->total_ht * $obj->valor_divisa_destino;
                    $total_conversion_sin_formato=$total_conversion/$obj->valor_divisa_origen;
                    $total_conversion=price($total_conversion_sin_formato,0,'',0,2,2);
                    echo "<td  align='right' width='120px'>
						USD {$total_conversion} 
					  </td>";
				}else if ($element->fk_currency==='USD' &&  $esCotizable==1){
                    $total_conversion_sin_formato=floatval($element->total_ht);
                    $total_conversion=price($element->total_ht,0,'',0,2,2);
                    echo "<td  align='right' width='120px'>
							USD {$total_conversion} 
					  </td>";
				}
				else{
                    $total_conversion_sin_formato=floatval(0,00);
                    $total_conversion=price(0,0,'',0,2,2);
                    echo "<td  align='right' width='120px'>
							-
					  </td>";
				}

                // Status
                print '<td align="center">'.$element->getLibStatut(5).'</td>';


                    // seleccion de priorida/dominio ante entidad hija
                    // si es checked entonces domina entidad padre de lo contrario la entidad hija
                    if($searchDomain===1 and  $defaultDomain==1){
                        if ( ($domain==0 && $entityFatherDomainExists==0 ) ||  ($entityFatherDomainExists==1 && $entityFatherDomain==0) ){
                                print '<td align="center"><input  type="checkbox" data-pj="'.$projectid.'" data-consolidationDomain="'.$entityFatherDomainId.'" data-entity_id="'.$domain_id.'" data-domain="salesorder" name="ov" value="1" onchange="var t=$(this);checkEntity(t);"  	></td>';

                        } else{
                                print '<td align="center"><input  type="checkbox" data-pj="'.$projectid.'" data-consolidationDomain="'.$entityFatherDomainId.'" data-entity_id="'.$domain_id.'" data-domain="salesorder" name="ov" value="0" onchange="var t=$(this);checkEntity(t);" checked="1"></td>';

                        }
                }else{
                    print '<td align="center"> </td>';
                }


                print '</tr>';
				/******************************************************************************************************************
				  Array currencys 
				*/
				if(!array_key_exists($element->fk_currency,$arrayCurrencys)){
					$arrayCurrencys[$element->fk_currency]['ht']=$element->total_ht;
                    $arrayCurrencys[$element->fk_currency]['tcc']=$element->total_ttc;
                    //caso especial tcccot el cual siempre es en USD sin importar la moneda. Segun la mondeda debe salvarse su valor con conversion o sin
                    if($element->fk_currency==='USD'){
                        $arrayCurrencys['USD']['tcccot']=$total_conversion_sin_formato;
					}else{
                        $arrayCurrencys['USD']['tcccot']= $arrayCurrencys['USD']['tcccot']+$total_conversion_sin_formato;
					}
					$arrayCurrencys[$element->fk_currency]['c']=$element->cost*$rate;
				}else{
					$arrayCurrencys[$element->fk_currency]['ht']= $arrayCurrencys[$element->fk_currency]['ht']+$element->total_ht;
					$arrayCurrencys[$element->fk_currency]['c']= $arrayCurrencys[$element->fk_currency]['c']+($element->cost*$rate);
                    $arrayCurrencys[$element->fk_currency]['tcc']= $arrayCurrencys[$element->fk_currency]['tcc']+$element->total_ttc;
                    $arrayCurrencys['USD']['tcccot']= $arrayCurrencys['USD']['tcccot']+$total_conversion_sin_formato;
				}

				/**********************************************************************************************************************/
				$total_ht = $total_ht + $element->total_ht;
				$total_ttc = $total_ttc + $element->total_ttc;
                $total_tcccot = $total_tcccot + $total_conversion_sin_formato;
				$total_cost= $total_cost + $element->cost*$rate;
                $linea++;
			}
            $linea;
		//	print_r($arrayCurrencys);
			$total[$value['class']]=$total_ttc;
			$costo[$value['class']]=$total_cost;
            $costo[$value['class']]=$total_tcccot;
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

				if(isset($arrayTotales['ht'])){
                    print '<td>&nbsp;</td>';
					print '<td align="right" title="Importe"><b><I>'.$divisa.' '.price($arrayTotales['ht']).'</I></b></td>';

				}else{
					print '<td>&nbsp;</td>';
				}				
				/*
				if(isset($arrayTotales['tcc']) ){ //&& 	$view_tcc ){
					print '<td align="right" title="venta"><b><I> '.$divisa.' '.price($arrayTotales['tcc']).'</I></b></td>';
				}
				*/
				if(isset($arrayTotales['c'])){											
					//print '<td align="right" title="Gasto"><b><I> '.$divisa.' '.price($arrayTotales['c'],0,'',0,2,2).'</I></b></td>';
				}else{
					print '<td>&nbsp;</td>';
				}					

                if(isset($arrayTotales['tcccot']) AND $divisa=='USD'){
                    print '<td>&nbsp;</td>';
                    print '<td align="right" title="venta"><b><I> '.$divisa.' '.price($arrayTotales['tcccot'],0,'',0,2,2) .'</I></b></td>';
                }else{
                    print '<td>&nbsp;</td>';
                    print '<td>&nbsp;</td>';
                }

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

echo "<h4>Totales consolidados en la divisa {$moneda_consolidada}</h4>";
print '<table class="noborder" width="100%">
<thead  >
	<tr class="liste_titre">
		<th >Titulo</th>
		<th align=center >Divisa Consolidada</th>
		<th  align=center>Importe</th>
		<!--<th  align=right>Costo</th>-->
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
	print  USD;
	print '</td>';
	$sum=0;
	$sum_c=0;
    $sum_tcccot=0;
	$sum_c_haber=0;
	$sum_haber=0;
	$result_sum=0;
	$resul_c=0;
	
        if( isset($currencies) && array_key_exists('currencies', $currencies)) {
            foreach($currencies['currencies'] as $currency=>$arrayValues){

            /***********************************************************************************/
				$sum_c+=$arrayValues['c'];
				$sum+=$arrayValues['ht'];
				$sum_tcccot+= $arrayValues['tcccot'];

            }            
        }


	print '<td  align=right>';
	print price($sum_tcccot,0,'',0,2,2);
	print '</td>';
	//print '<td   align=right>';
	//print price($sum_c,0,'',0,2,2);
	//print '</td>';


					
	$arr_result[$currencies['operation']]['c']+=$sum_c;
	$arr_result[$currencies['operation']]['ht']+=$sum_tcccot;
	
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
			echo 'USD';
		print 	 '<I><b>';			
	print '</td>';	
	print '<td  align=right>';
	print 	 '<I><b>';
			print price($tt_ht,0,'',0,2,2);
	print 	 '</I></b>';
	print '</td>';

	/*print 	 '<I><b>';
			print price($tt_c,0,'',0,2,2);
	print 	 '</I></b>';
	print '</td>';
	*/
	print '</tr>';	

//print '<td width="10%" align="right">'.price($total['Salesorder']-$costo['Salesorder']-$costo['Deplacement']-$costo['Policy'],0,'',0,2,2).'</td>';

print '<tbody>';
print "</table>";



llxFooter();

$db->close();





?>


