<?php
/* Copyright (C) 2012 Regis Houssin <regis.houssin@capnetworks.com>
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
 *
 */

$module = $object->element;
$policy_number = 'policy_number';
$price = 'price';
$endorsement='endorsement';

$colwidth=(isset($colwidth)?$colwidth:25);
$permission=(isset($permission)?$permission:(isset($user->rights->$module->creer)?$user->rights->$module->creer:0));    // If already defined by caller page
$moreparam=(isset($moreparam)?$moreparam:'');

// Special cases
if ($module == 'propal')                 { $permission=$user->rights->propale->creer; }
elseif ($module == 'fichinter')         { $permission=$user->rights->ficheinter->creer; $note_private = 'note_private'; }
elseif ($module == 'project')           { $permission=$user->rights->projet->creer; $note_private = 'note_private'; }
elseif ($module == 'project_task')      { $permission=$user->rights->projet->creer; $note_private = 'note_private'; }
elseif ($module == 'invoice_supplier')  { $permission=$user->rights->fournisseur->facture->creer; }
elseif ($module == 'order_supplier')    { $permission=$user->rights->fournisseur->commande->creer; }

if (! empty($conf->global->FCKEDITOR_ENABLE_SOCIETE)) $typeofdata='ckeditor:dolibarr_notes:100%:200::1:12:100';
else $typeofdata='textarea:1:50';
?>

<!-- BEGIN PHP TEMPLATE NOTES -->
<div class="table-border">
	<div class="table-border-row">
		<div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>><?php echo $form->editfieldkey("Número de poliza", $policy_number, $object->policy->ref, $object, $permission, 'numeric:10', $moreparam); ?></div>
		<div class="table-val-border-col"><?php echo $form->editfieldval("Número de poliza", $policy_number, $object->policy->ref, $object, $permission, 'numeric:10', '', null, null, $moreparam); ?></div>
	</div>
<?php if (! $user->societe_id) { ?>
	<div class="table-border-row">
		<div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>><table class="nobordernopadding" width="100%"><tr><td nowrap="nowrap">Aseguradora</td><td align="right">
		<?php
			if ($action != 'editassurance_soc_id') print '<a href="'.$_SERVER['PHP_SELF'].'?action=editassurance_soc_id&amp;id='.$object->id.'">'.img_edit($langs->trans('SetAssuranceSoc')).'</a>';
		?></td></tr></table></div>
		<div class="table-val-border-col">
		<?php
			if ($action == 'editassurance_soc_id')
			{
				$form->form_thirdparty($_SERVER['PHP_SELF'].'?action=setassurance_soc_id&amp;id='.$object->id, $object->policy->fk_soc,'socid','fk_typent=102'); 
			}
			else
			{
				if($object->policy->assurance)print $object->policy->assurance->getNomURL();
			}
		?></div>
	</div>
	<div class="table-border-row">
		<div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>><?php echo $form->editfieldkey("Endoso", $endorsement, $object->policy->endorsement, $object, $permission, 'numeric:10', $moreparam); ?></div>
		<div class="table-val-border-col"><?php echo $form->editfieldval("Endoso", $endorsement, $object->policy->endorsement, $object, $permission, 'numeric:10', '', null, null, $moreparam); ?></div>
	</div>
	<div class="table-border-row">
		<div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>><table class="nobordernopadding" width="100%"><tr><td nowrap="nowrap">Fecha de expiración</td><td align="right">
		<?php
			if ($action != 'editexpiration_date') print '<a href="'.$_SERVER['PHP_SELF'].'?action=editexpiration_date&amp;id='.$object->id.'">'.img_edit($langs->trans('SetExpirDate')).'</a>';
		?></td></tr></table></div>
		<div class="table-val-border-col">
		<?php
			if ($action == 'editexpiration_date')
			{
				$form->form_date($_SERVER['PHP_SELF'].'?action=setexpiration_date&amp;id='.$object->id, $object->policy->expiration_date,'exp_'); 
			}
			else
			{
				print dol_print_date($object->policy->expiration_date,'daytext');
			}
		?></div>
	</div>
	<div class="table-border-row">
		<div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>><?php echo $form->editfieldkey("Price", $price, price($object->policy->price), $object, $permission, 'numeric:10', $moreparam); ?></div>
		<div class="table-val-border-col"><?php echo $form->editfieldval("Price", $price, price($object->policy->price), $object, $permission, 'numeric:10', '', null, null, $moreparam); ?></div>
	</div>
	<div class="table-border-row">
		<div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>><table class="nobordernopadding" width="100%"><tr><td nowrap="nowrap">Cancelada ?</td><td align="right">
		<?php
			if ($action != 'editstatus') print '<a href="'.$_SERVER['PHP_SELF'].'?action=editstatus&amp;id='.$object->id.'">'.img_edit($langs->trans('SetStatus')).'</a>';
		?></td></tr></table></div>
		<div class="table-val-border-col">
		<?php
			if ($action == 'editstatus')
			{
				print '<form method="post" action="'.$_SERVER['PHP_SELF'].'?action=setstatus&amp;id='.$object->id.'" name="formstatus">';
				print '<input type="hidden" name="action" value="setstatus">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
				print '<table class="nobordernopadding" cellpadding="0" cellspacing="0">';
				print '<tr><td>';
				print $form->selectyesno("status", $object->fk_status); 
				print '</td>';
				print '<td align="left"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
				print '</tr></table></form>';
			}
			else
			{
				if($object->policy->status)
					print 'Si';
				else
					print 'No';
			}
		?></div>
	</div>
<?php } ?>
</div>
<!-- END PHP TEMPLATE NOTES-->
