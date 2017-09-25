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

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/propal.lib.php';

$langs->load('propal');
$langs->load('compta');
$langs->load('bills');

$id = GETPOST('id','int');
$ref=GETPOST('ref','alpha');
$action=GETPOST('action','alpha');

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'propale', $id, 'propal');

$object = new Propal($db);


/******************************************************************************/
/*                     Actions                                                */
/******************************************************************************/

if ($action == 'setnote_public' && $user->rights->propale->creer)
{
	$object->fetch($id);
	$result=$object->update_note_public(dol_html_entity_decode(GETPOST('note_public'), ENT_QUOTES));
	if ($result < 0) dol_print_error($db,$object->error);
}

else if ($action == 'setnote' && $user->rights->propale->creer)
{
	$object->fetch($id);
	$result=$object->update_note(dol_html_entity_decode(GETPOST('note'), ENT_QUOTES));
	if ($result < 0) dol_print_error($db,$object->error);
}
else if ($action == 'deletedeplacement' && $user->rights->propale->creer)
{
	$object->fetch($id);
	$result = $object->delete_deplacement($_GET["lineid"]);

	if ($result >= 0)
	{
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
		exit;
	}
	else {
		dol_print_error($db);
	}
}

/******************************************************************************/
/* Affichage fiche                                                            */
/******************************************************************************/

llxHeader('',$langs->trans('Proposal'),'EN:Commercial_Proposals|FR:Proposition_commerciale|ES:Presupuestos');

$form = new Form($db);

if ($id > 0 || ! empty($ref))
{
	if ($mesg) print $mesg;

	$now=dol_now();

	if ($object->fetch($id, $ref))
	{
		$societe = new Societe($db);
		$societe->fetch($object->socid);		

		$head = propal_prepare_head($object);
		dol_fiche_head($head, 'gastos', $langs->trans('Proposal'), 0, 'propal');

		print '<table class="border" width="100%">';

		$linkback = '<a href="'.DOL_URL_ROOT.'/comm/propal/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans('BackToList').'</a>';

		// Ref
		print '<tr><td width="25%">'.$langs->trans('Ref').'</td><td colspan="3">';
		print $form->showrefnav($object,'ref',$linkback,1,'ref','ref','');
		print '</td></tr>';

		// Ref client
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
		if ( is_null($object->client) )
			$object->fetch_thirdparty();
		print "<tr><td>".$langs->trans("Company")."</td>";
		print '<td colspan="3">'.$object->client->getNomUrl(1).'</td></tr>';

		// Delivery address
		if (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT))
		{
			print '<tr><td>';
			print '<table class="nobordernopadding" width="100%"><tr><td>';
			print $langs->trans('DeliveryAddress');
			print '</td>';

			if ($action != 'editdelivery_address' && $object->brouillon) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editdelivery_address&amp;socid='.$object->socid.'&amp;id='.$object->id.'">'.img_edit($langs->transnoentitiesnoconv('SetDeliveryAddress'),1).'</a></td>';
			print '</tr></table>';
			print '</td><td colspan="3">';

			if ($action == 'editdelivery_address')
			{
				$formother->form_address($_SERVER['PHP_SELF'].'?id='.$object->id,$object->fk_delivery_address,GETPOST('socid','int'),'fk_address','salesorder',$object->id);
			}
			else
			{
				$formother->form_address($_SERVER['PHP_SELF'].'?id='.$object->id,$object->fk_delivery_address,GETPOST('socid','int'),'none','salesorder',$object->id);
			}
			print '</td></tr>';
		}

		print "</table>";

		print '</div>';

		print '<br>';

		// Contacts lines (modules that overwrite templates must declare this into descriptor)
		$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl'));
		foreach($dirtpls as $reldir)
		{
			//$res=@include dol_buildpath($reldir.'/contacts.tpl.php');
			$res=@include dol_buildpath($reldir.'/deplacements.tpl.php');
			if ($res) break;
		}
	}
	else
	{
		// Contrat non trouve
		print "ErrorRecordNotFound";
	}
}


llxFooter();
$db->close();
?>
