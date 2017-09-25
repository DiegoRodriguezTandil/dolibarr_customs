<?php
/* Copyright (C) 2003-2006	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2005-2012	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2012		Juanjo Menent			<jmenent@2byte.es>
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
 *	\file       htdocs/expedition/shipment.php
 *	\ingroup    expedition
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/expedition/class/expedition.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/sendings.lib.php';
if (! empty($conf->projet->enabled))
	require DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
/*if (! empty($conf->stock->enabled))
	require DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';*/
if (! empty($conf->propal->enabled)) {
	if (! class_exists('Propal')) {
		require DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
	}
}
if (! empty($conf->salesorder->enabled)) {
	if (! class_exists('Salesorder')) {
		require DOL_DOCUMENT_ROOT.'/salesorder/class/salesorder.class.php';
	}
}
if (! empty($conf->product->enabled) || ! empty($conf->service->enabled)) {
	if (! class_exists('Product')) {
		require DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	}
}

$langs->load('salesorders');
$langs->load("companies");
$langs->load("bills");
$langs->load('propal');
$langs->load('deliveries');
$langs->load('stocks');

$id=GETPOST('id','int');
$ref= GETPOST('ref','alpha');
$action=GETPOST('action','alpha');

// Security check
$socid=0;
if (! empty($user->societe_id)) $socid=$user->societe_id;
$result=restrictedArea($user,'salesorder',$id);


/*
 * Actions
 */

// Categorisation dans projet
if ($action == 'classin')
{
	$salesorder = new Salesorder($db);
	$salesorder->fetch($id);
	$salesorder->setProject(GETPOST('projectid','int'));
}

if ($action == 'confirm_cloture' && GETPOST('confirm','alpha') == 'yes')
{
	$salesorder = new Salesorder($db);
	$salesorder->fetch($id);
	$result = $salesorder->cloture($user);
}

// Positionne ref salesorder client
if ($action == 'setrefcustomer' && $user->rights->salesorder->creer)
{
	$salesorder = new Salesorder($db);
	$salesorder->fetch($id);
	$salesorder->set_ref_client($user,GETPOST('ref_customer','alpha'));
}

if ($action == 'setdatedelivery' && $user->rights->salesorder->creer)
{
	//print "x ".$_POST['liv_month'].", ".$_POST['liv_day'].", ".$_POST['liv_year'];
	$datelivraison=dol_mktime(0, 0, 0, GETPOST('liv_month','int'), GETPOST('liv_day','int'),GETPOST('liv_year','int'));

	$salesorder = new Salesorder($db);
	$salesorder->fetch($id);
	$result=$salesorder->set_date_livraison($user,$datelivraison);
	if ($result < 0)
	{
		$mesg='<div class="error">'.$salesorder->error.'</div>';
	}
}

if ($action == 'setdeliveryaddress' && $user->rights->salesorder->creer)
{
	$salesorder = new Salesorder($db);
	$salesorder->fetch($id);
	$salesorder->setDeliveryAddress(GETPOST('delivery_address_id','int'));
}

if ($action == 'setmode' && $user->rights->salesorder->creer)
{
	$salesorder = new Salesorder($db);
	$salesorder->fetch($id);
	$result = $salesorder->setPaymentMethods(GETPOST('mode_reglement_id','int'));
	if ($result < 0) dol_print_error($db,$salesorder->error);
}

if ($action == 'setconditions' && $user->rights->salesorder->creer)
{
	$salesorder = new Salesorder($db);
	$salesorder->fetch($id);
	$result=$salesorder->setPaymentTerms(GETPOST('cond_reglement_id','int'));
	if ($result < 0) dol_print_error($db,$salesorder->error);
}



/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formproduct = new FormProduct($db);

llxHeader('',$langs->trans('OrderCard'),'');


if ($id > 0 || ! empty($ref))
{
	$salesorder = new Salesorder($db);
	if ( $salesorder->fetch($id,$ref) > 0)
	{
		$salesorder->loadExpeditions(1);

		$product_static=new Product($db);

		$soc = new Societe($db);
		$soc->fetch($salesorder->socid);

		$author = new User($db);
		$author->fetch($salesorder->user_author_id);

		$head = salesorder_prepare_head($salesorder);
		dol_fiche_head($head, 'shipping', $langs->trans("SalesOrder"), 0, 'salesorder');

		/*
		 * Confirmation de la validation
		 */
		if ($action == 'cloture')
		{
			$ret=$form->form_confirm($_SERVER['PHP_SELF']."?id=".$id,$langs->trans("CloseShipment"),$langs->trans("ConfirmCloseShipment"),"confirm_cloture");
			if ($ret == 'html') print '<br>';
		}

		// Onglet salesorder
		$nbrow=7;
		if (! empty($conf->projet->enabled)) $nbrow++;

		print '<table class="border" width="100%">';

		// Ref
		print '<tr><td width="18%">'.$langs->trans('Ref').'</td>';
		print '<td colspan="3">';
		print $form->showrefnav($salesorder,'ref','',1,'ref','ref');
		print '</td>';
		print '</tr>';

		// Ref salesorder client
		print '<tr><td>';
		print '<table class="nobordernopadding" width="100%"><tr><td nowrap>';
		print $langs->trans('RefCustomer').'</td><td align="left">';
		print '</td>';
		if ($action != 'RefCustomerOrder' && $salesorder->brouillon) print '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?action=RefCustomerOrder&amp;id='.$salesorder->id.'">'.img_edit($langs->trans('Modify')).'</a></td>';
		print '</tr></table>';
		print '</td><td colspan="3">';
		if ($user->rights->salesorder->creer && $action == 'RefCustomerOrder')
		{
			print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$id.'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="setrefcustomer">';
			print '<input type="text" class="flat" size="20" name="ref_customer" value="'.$salesorder->ref_client.'">';
			print ' <input type="submit" class="button" value="'.$langs->trans('Modify').'">';
			print '</form>';
		}
		else
		{
			print $salesorder->ref_client;
		}
		print '</td>';
		print '</tr>';

		// Third party
		print '<tr><td>'.$langs->trans('Company').'</td>';
		print '<td colspan="3">'.$soc->getNomUrl(1).'</td>';
		print '</tr>';

		// Discounts for third party
		print '<tr><td>'.$langs->trans('Discounts').'</td><td colspan="3">';
		if ($soc->remise_client) print $langs->trans("CompanyHasRelativeDiscount",$soc->remise_client);
		else print $langs->trans("CompanyHasNoRelativeDiscount");
		print '. ';
		$absolute_discount=$soc->getAvailableDiscounts('','fk_facture_source IS NULL');
		$absolute_creditnote=$soc->getAvailableDiscounts('','fk_facture_source IS NOT NULL');
		$absolute_discount=price2num($absolute_discount,'MT');
		$absolute_creditnote=price2num($absolute_creditnote,'MT');
		if ($absolute_discount)
		{
			if ($salesorder->statut > 0)
			{
				print $langs->trans("CompanyHasAbsoluteDiscount",price($absolute_discount),$langs->transnoentities("Currency".$conf->currency));
			}
			else
			{
				// Remise dispo de type non avoir
				$filter='fk_facture_source IS NULL';
				print '<br>';
				$form->form_remise_dispo($_SERVER["PHP_SELF"].'?id='.$salesorder->id,0,'remise_id',$soc->id,$absolute_discount,$filter);
			}
		}
		if ($absolute_creditnote)
		{
			print $langs->trans("CompanyHasCreditNote",price($absolute_creditnote),$langs->transnoentities("Currency".$conf->currency)).'. ';
		}
		if (! $absolute_discount && ! $absolute_creditnote) print $langs->trans("CompanyHasNoAbsoluteDiscount").'.';
		print '</td></tr>';

		// Date
		print '<tr><td>'.$langs->trans('Date').'</td>';
		print '<td colspan="2">'.dol_print_date($salesorder->date,'daytext').'</td>';
		print '<td width="50%">'.$langs->trans('Source').' : '.$salesorder->getLabelSource().'</td>';
		print '</tr>';

		// Delivery date planned
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('DateDeliveryPlanned');
		print '</td>';

		if ($action != 'editdate_livraison') print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdate_livraison&amp;id='.$salesorder->id.'">'.img_edit($langs->trans('SetDeliveryDate'),1).'</a></td>';
		print '</tr></table>';
		print '</td><td colspan="2">';
		if ($action == 'editdate_livraison')
		{
			print '<form name="setdate_livraison" action="'.$_SERVER["PHP_SELF"].'?id='.$salesorder->id.'" method="post">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="setdatedelivery">';
			$form->select_date($salesorder->date_livraison>0?$salesorder->date_livraison:-1,'liv_','','','',"setdatedelivery");
			print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
			print '</form>';
		}
		else
		{
			print dol_print_date($salesorder->date_livraison,'daytext');
		}
		print '</td>';
		print '<td rowspan="'.$nbrow.'" valign="top">'.$langs->trans('NotePublic').' :<br>';
		print nl2br($salesorder->note_public);
		print '</td>';
		print '</tr>';

		// Terms of payment
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('PaymentConditionsShort');
		print '</td>';

		if ($action != 'editconditions' && ! empty($salesorder->brouillon)) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editconditions&amp;id='.$salesorder->id.'">'.img_edit($langs->trans('SetConditions'),1).'</a></td>';
		print '</tr></table>';
		print '</td><td colspan="2">';
		if ($action == 'editconditions')
		{
			$form->form_conditions_reglement($_SERVER['PHP_SELF'].'?id='.$salesorder->id,$salesorder->cond_reglement_id,'cond_reglement_id');
		}
		else
		{
			$form->form_conditions_reglement($_SERVER['PHP_SELF'].'?id='.$salesorder->id,$salesorder->cond_reglement_id,'none');
		}
		print '</td></tr>';

		// Mode of payment
		print '<tr><td height="10">';
		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('PaymentMode');
		print '</td>';
		if ($action != 'editmode' && ! empty($salesorder->brouillon)) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editmode&amp;id='.$salesorder->id.'">'.img_edit($langs->trans('SetMode'),1).'</a></td>';
		print '</tr></table>';
		print '</td><td colspan="2">';
		if ($action == 'editmode')
		{
			$form->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$salesorder->id,$salesorder->mode_reglement_id,'mode_reglement_id');
		}
		else
		{
			$form->form_modes_reglement($_SERVER['PHP_SELF'].'?id='.$salesorder->id,$salesorder->mode_reglement_id,'none');
		}
		print '</td></tr>';

		// Project
		if (! empty($conf->projet->enabled))
		{
			$langs->load('projects');
			print '<tr><td height="10">';
			print '<table class="nobordernopadding" width="100%"><tr><td>';
			print $langs->trans('Project');
			print '</td>';
			if ($action != 'classify') print '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?action=classify&amp;id='.$salesorder->id.'">'.img_edit($langs->trans('SetProject')).'</a></td>';
			print '</tr></table>';
			print '</td><td colspan="2">';
			if ($action == 'classify')
			{
				$form->form_project($_SERVER['PHP_SELF'].'?id='.$salesorder->id, $salesorder->socid, $salesorder->fk_project, 'projectid');
			}
			else
			{
				$form->form_project($_SERVER['PHP_SELF'].'?id='.$salesorder->id, $salesorder->socid, $salesorder->fk_project, 'none');
			}
			print '</td></tr>';
		}

		// Lignes de 3 colonnes

		// Total HT
		print '<tr><td>'.$langs->trans('AmountHT').'</td>';
		print '<td align="right"><b>'.price($salesorder->total_ht).'</b></td>';
		print '<td>'.$langs->trans('Currency'.$conf->currency).'</td></tr>';

		// Total TVA
		print '<tr><td>'.$langs->trans('AmountVAT').'</td><td align="right">'.price($salesorder->total_tva).'</td>';
		print '<td>'.$langs->trans('Currency'.$conf->currency).'</td></tr>';

		// Total TTC
		print '<tr><td>'.$langs->trans('AmountTTC').'</td><td align="right">'.price($salesorder->total_ttc).'</td>';
		print '<td>'.$langs->trans('Currency'.$conf->currency).'</td></tr>';

		// Statut
		print '<tr><td>'.$langs->trans('Status').'</td>';
		print '<td colspan="2">'.$salesorder->getLibStatut(4).'</td>';
		print '</tr>';

		print '</table><br>';


		/**
		 *  Lignes de salesorders avec quantite livrees et reste a livrer
		 *  Les quantites livrees sont stockees dans $salesorder->expeditions[fk_product]
		 */
		print '<table class="liste" width="100%">';

		$sql = "SELECT cd.rowid, cd.fk_product, cd.product_type, cd.label, cd.description,";
		$sql.= " cd.price, cd.tva_tx, cd.subprice,";
		$sql.= " cd.qty,";
		$sql.= ' cd.date_start,';
		$sql.= ' cd.date_end,';
		$sql.= ' p.label as product_label, p.ref, p.fk_product_type, p.rowid as prodid,';
		$sql.= ' p.description as product_desc, p.fk_product_type as product_type';
		$sql.= " FROM ".MAIN_DB_PREFIX."salesorderdet as cd";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON cd.fk_product = p.rowid";
		$sql.= " WHERE cd.fk_salesorder = ".$salesorder->id;
		$sql.= " ORDER BY cd.rang, cd.rowid";

		//print $sql;
		dol_syslog("shipment.php sql=".$sql, LOG_DEBUG);
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;

			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans("Description").'</td>';
			print '<td align="center">'.$langs->trans("QtyOrdered").'</td>';
			print '<td align="center">'.$langs->trans("QtyShipped").'</td>';
			print '<td align="center">'.$langs->trans("KeepToShip").'</td>';
			if (! empty($conf->stock->enabled))
			{
				print '<td align="center">'.$langs->trans("Stock").'</td>';
			}
			else
			{
				print '<td>&nbsp;</td>';
			}
			print "</tr>\n";

			$var=true;
			$toBeShipped=array();
			$toBeShippedTotal=0;
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);
				$var=!$var;

				// Show product and description
				$type=$objp->product_type?$objp->product_type:$objp->fk_product_type;
				// Try to enhance type detection using date_start and date_end for free lines where type
				// was not saved.
				if (! empty($objp->date_start)) $type=1;
				if (! empty($objp->date_end)) $type=1;

				print "<tr ".$bc[$var].">";

				// Product label
				if ($objp->fk_product > 0)
				{
					// Define output language
					if (! empty($conf->global->MAIN_MULTILANGS) && ! empty($conf->global->PRODUIT_TEXTS_IN_THIRDPARTY_LANGUAGE))
					{
						$salesorder->fetch_thirdparty();
						$prod = new Product($db, $objp->fk_product);
						$outputlangs = $langs;
						$newlang='';
						if (empty($newlang) && ! empty($_REQUEST['lang_id'])) $newlang=$_REQUEST['lang_id'];
						if (empty($newlang)) $newlang=$salesorder->client->default_lang;
						if (! empty($newlang))
						{
							$outputlangs = new Translate("",$conf);
							$outputlangs->setDefaultLang($newlang);
						}

						$label = (! empty($prod->multilangs[$outputlangs->defaultlang]["label"])) ? $prod->multilangs[$outputlangs->defaultlang]["label"] : $objp->product_label;
					}
					else
						$label = (! empty($objp->label)?$objp->label:$objp->product_label);

					print '<td>';
					print '<a name="'.$objp->rowid.'"></a>'; // ancre pour retourner sur la ligne

					// Show product and description
					$product_static->type=$objp->fk_product_type;
					$product_static->id=$objp->fk_product;
					$product_static->ref=$objp->ref;
					$text=$product_static->getNomUrl(1);
					$text.= ' - '.$label;
					$description=($conf->global->PRODUIT_DESC_IN_FORM?'':dol_htmlentitiesbr($objp->description));
					print $form->textwithtooltip($text,$description,3,'','',$i);

					// Show range
					print_date_range($db->jdate($objp->date_start),$db->jdate($objp->date_end));

					// Add description in form
					if (! empty($conf->global->PRODUIT_DESC_IN_FORM))
					{
						print ($objp->description && $objp->description!=$objp->product_label)?'<br>'.dol_htmlentitiesbr($objp->description):'';
					}

					print '</td>';
				}
				else
				{
					print "<td>";
					if ($type==1) $text = img_object($langs->trans('Service'),'service');
					else $text = img_object($langs->trans('Product'),'product');

					if (! empty($objp->label)) {
						$text.= ' <strong>'.$objp->label.'</strong>';
						print $form->textwithtooltip($text,$objp->description,3,'','',$i);
					} else {
						print $text.' '.nl2br($objp->description);
					}

					// Show range
					print_date_range($db->jdate($objp->date_start),$db->jdate($objp->date_end));
					print "</td>\n";
				}

				// Qty ordered
				print '<td align="center">'.$objp->qty.'</td>';

				// Qty already shipped
				$qtyProdCom=$objp->qty;
				print '<td align="center">';
				// Nb of sending products for this line of order
				$qtyAlreadyShipped = (! empty($salesorder->expeditions[$objp->rowid])?$salesorder->expeditions[$objp->rowid]:0);
				print $qtyAlreadyShipped;
				print '</td>';

				// Qty remains to ship
				print '<td align="center">';
				if ($type == 0 || ! empty($conf->global->STOCK_SUPPORTS_SERVICES))
				{
					$toBeShipped[$objp->fk_product] = $objp->qty - $qtyAlreadyShipped;
					$toBeShippedTotal += $toBeShipped[$objp->fk_product];
					print $toBeShipped[$objp->fk_product];
				}
				else
				{
					print '0 ('.$langs->trans("Service").')';
				}
				print '</td>';

				if ($objp->fk_product > 0)
				{
					$product = new Product($db);
					$product->fetch($objp->fk_product);
				}

				if ($objp->fk_product > 0 && $type == 0 && ! empty($conf->stock->enabled))
				{
					print '<td align="center">';
					print $product->stock_reel;
					if ($product->stock_reel < $toBeShipped[$objp->fk_product])
					{
						print ' '.img_warning($langs->trans("StockTooLow"));
					}
					print '</td>';
				}
				else
				{
					print '<td>&nbsp;</td>';
				}
				print "</tr>\n";

				// Show subproducts details
				if ($objp->fk_product > 0 && ! empty($conf->global->PRODUIT_SOUSPRODUITS))
				{
					// Set tree of subproducts in product->sousprods
					$product->get_sousproduits_arbo();
					//var_dump($product->sousprods);exit;

					// Define a new tree with quantiies recalculated
					$prods_arbo = $product->get_arbo_each_prod($qtyProdCom);
					//var_dump($prods_arbo);
					if (count($prods_arbo) > 0)
					{
						foreach($prods_arbo as $key => $value)
						{
							print '<tr><td colspan="4">';

							$img='';
							if ($value['stock'] < $value['stock_alert'])
							{
								$img=img_warning($langs->trans("StockTooLow"));
							}
							print '<tr><td>&nbsp; &nbsp; &nbsp; -> <a href="'.DOL_URL_ROOT."/product/fiche.php?id=".$value['id'].'">'.$value['fullpath'].'</a> ('.$value['nb'].')</td>';
							print '<td align="center"> '.$value['nb_total'].'</td>';
							print '<td>&nbsp</td>';
							print '<td>&nbsp</td>';
							print '<td align="center">'.$value['stock'].' '.$img.'</td></tr>'."\n";

							print '</td></tr>'."\n";
						}
					}
				}

				$i++;
			}
			$db->free($resql);

			if (! $num)
			{
				print '<tr '.$bc[false].'><td colspan="5">'.$langs->trans("NoArticleOfTypeProduct").'<br>';
			}

			print "</table>";
		}
		else
		{
			dol_print_error($db);
		}

		print '</div>';


		/*
		 * Boutons Actions
		 */

		if (empty($user->societe_id))
		{
			print '<div class="tabsAction">';

            // Bouton expedier sans gestion des stocks
            if (empty($conf->stock->enabled) && ($salesorder->statut > 0 && $salesorder->statut < 3))
			{
				if ($user->rights->expedition->creer)
				{
					print '<a class="butAction" href="'.DOL_URL_ROOT.'/expedition/fiche.php?action=create&amp;origin=salesorder&amp;object_id='.$id.'">'.$langs->trans("NewSending").'</a>';
					if ($toBeShippedTotal <= 0)
					{
						print ' '.img_warning($langs->trans("WarningNoQtyLeftToSend"));
					}
				}
				else
				{
					print '<a class="butActionRefused" href="#">'.$langs->trans("NewSending").'</a>';
				}
			}
			print "</div>";
		}


        // Bouton expedier avec gestion des stocks
        if (! empty($conf->stock->enabled) && ($salesorder->statut > 0 && $salesorder->statut < 3))
		{
			if ($user->rights->expedition->creer)
			{
				print_titre($langs->trans("NewSending"));

				print '<form method="GET" action="'.DOL_URL_ROOT.'/expedition/fiche.php">';
				print '<input type="hidden" name="action" value="create">';
				print '<input type="hidden" name="id" value="'.$salesorder->id.'">';
				print '<input type="hidden" name="origin" value="salesorder">';
				print '<input type="hidden" name="origin_id" value="'.$salesorder->id.'">';
				print '<table class="border" width="100%">';

				$langs->load("stocks");

				print '<tr>';

				if (! empty($conf->stock->enabled))
				{
					print '<td>'.$langs->trans("WarehouseSource").'</td>';
					print '<td>';
					print $formproduct->selectWarehouses(-1,'entrepot_id','',1);
					if (count($formproduct->cache_warehouses) <= 0)
					{
						print ' &nbsp; '.$langs->trans("WarehouseSourceNotDefined").' <a href="'.DOL_URL_ROOT.'/product/stock/fiche.php?action=create">'.$langs->trans("AddOne").'</a>';
					}
					print '</td>';
				}
				print '<td align="center">';
				print '<input type="submit" class="button" named="save" value="'.$langs->trans("NewSending").'">';
				if ($toBeShippedTotal <= 0)
				{
					print ' '.img_warning($langs->trans("WarningNoQtyLeftToSend"));
				}
				print '</td></tr>';

				print "</table>";
				print "</form>\n";
				print '<br>';

				$somethingshown=1;

			}
			else
			{
				print '<div class="tabsAction">';
				print '<a class="butActionRefused" href="#">'.$langs->trans("NewSending").'</a>';
				print '</div>';
			}
		}

		show_list_sending_receive('salesorder',$salesorder->id);
	}
	else
	{
		/* Salesorder non trouvee */
		print "Salesorder inexistante";
	}
}


llxFooter();

$db->close();
?>
