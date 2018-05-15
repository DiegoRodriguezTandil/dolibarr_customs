<?php
/* Copyright (C) 2004      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/comm/propal/note.php
 *	\ingroup    propale
 *	\brief      Fiche d'information sur une proposition commerciale
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT .'/salesorder/class/salesorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/policy.class.php';

$langs->load("companies");
$langs->load("bills");
$langs->load("orders");

$id = GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');

// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'salesorder', $id, 'salesorder');

$object = new Salesorder($db);
if (! $object->fetch($id, $ref) > 0)
{
	dol_print_error($db);
}


/******************************************************************************/
/*                     Actions                                                */
/******************************************************************************/
$object->policy= new Policy($db);
if(!$object->policy->fetch_by_doc(GETPOST('id'),'SO'))
{	
	$object->policy->fk_docid=GETPOST('id');
	$object->policy->fk_doctype='SO';
	$object->policy->fk_soc=0;
	$object->policy->ref='';
	$object->policy->expiration_date=$object->date_livraison;
	$object->policy->price=0;
	$object->policy->create($user);
}
   

if ($action == 'setpolicy_number' && $user->rights->propale->creer)
{	
	$result=$object->policy->update_ref_number(dol_html_entity_decode(GETPOST('policy_number'), ENT_QUOTES));
	if ($result < 0) dol_print_error($db,$object->error);
}

else if ($action == 'setassurance_soc_id' && $user->rights->propale->creer)
{
	$result=$object->policy->update_assurance_soc(dol_html_entity_decode(GETPOST('socid'), ENT_QUOTES));
	if ($result < 0) dol_print_error($db,$object->error);
}
else if ($action == 'setexpiration_date' && $user->rights->propale->creer)
{
	//print var_dump(GETPOST('expiration_date'));
	$expiration=dol_mktime(12, 0, 0, GETPOST('exp_month'), GETPOST('exp_day'), GETPOST('exp_year'));
	$result=$object->policy->update_expiration_date($expiration);
	if ($result < 0) dol_print_error($db,$object->error);
}
else if ($action == 'setprice' && $user->rights->propale->creer)
{
	$result=$object->policy->update_price(GETPOST('price'));
	if ($result < 0) dol_print_error($db,$object->error);
}
else if ($action == 'setstatus' && $user->rights->propale->creer)
{
	if(GETPOST('status')=='yes')
		$status=1;
	else
		$status=0;
	$result=$object->policy->update_status($status);
	if ($result < 0) dol_print_error($db,$object->error);
}
else if ($action == 'setCurrency' && $user->rights->propale->creer)
{
    if(!empty(GETPOST('currency_val'))){
        $result=$object->policy->update_currency(GETPOST('currency_val'));
        if ($result < 0) dol_print_error($db,$object->error);
    }
}


/******************************************************************************/
/* Affichage fiche                                                            */
/******************************************************************************/

llxHeader('',$langs->trans('Salesorder'),'EN:Commercial_Proposals|FR:Proposition_commerciale|ES:Presupuestos');

$form = new Form($db);

if ($id > 0 || ! empty($ref))
{
	$soc = new Societe($db);
	$soc->fetch($object->socid);

	$head = salesorder_prepare_head($object);

	dol_fiche_head($head, 'poliza', $langs->trans("CustomerSalesOrder"), 0, 'salesorder');

	print '<table class="border" width="100%">';

	$linkback = '<a href="'.DOL_URL_ROOT.'/salesorder/liste.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

	// Ref
	print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td colspan="3">';
	print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
	print "</td></tr>";

	// Ref salesorder client
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td nowrap>';
	print $langs->trans('RefCustomer').'</td><td align="left">';
	print '</td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	print $object->ref_client;
	print '</td>';
	print '</tr>';

	// Customer
	print "<tr><td>".$langs->trans("Company")."</td>";
	print '<td colspan="3">'.$soc->getNomUrl(1).'</td></tr>';

	print "</table>";

	print '<br>';

	include DOL_DOCUMENT_ROOT.'/core/tpl/polizas.tpl.php';

	print '</div>';
}


llxFooter();
$db->close();
?>
