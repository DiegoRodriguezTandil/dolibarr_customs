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

echo "
<script>

	$( document ).ready(function() {
	
	
			/*valida que los campos valor_unitario y valor_total de detalle pedido sean numericos pero permitan ingresar coma*/
			$(document).on('keydown', '#price', function(e){
				 -1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190,188])||(/65|67|86|88/.test(e.keyCode)&&(e.ctrlKey===true||e.metaKey===true))&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()
			});
		
			/*controla el ingreso de datos con formato ###.###,## para los campos valor_unitario y valor_total de detalle pedido */
			$(document).on('keyup', '#price', function () {
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

</script>
";


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
          <div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>>
                <?php
                        echo $form->editfieldkey("Price", $price,$object->policy->price, $object, $permission, null, $moreparam);
                ?>
           </div>
		<div class="table-val-border-col">
                <?php
                        $vf=number_format($object->policy->cost, 2, ',', '.');
                        echo $form->editfieldval("Price", $price, $vf, $object, $permission, 'numeric:10', '', null, null, $moreparam);
                ?>
        </div>
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
    	<div class="table-border-row">
		<div class="table-key-border-col"<?php echo ' style="width: '.$colwidth.'%"'; ?>><table class="nobordernopadding" width="100%"><tr><td nowrap="nowrap">Divisa</td><td align="right">
		<?php
			if ($action != 'editsCurrency') print '<a href="'.$_SERVER['PHP_SELF'].'?action=editsCurrency&amp;id='.$object->id.'">'.img_edit($langs->trans('SetStatus')).'</a>';
		?></td></tr></table></div>
		<div class="table-val-border-col">
		<?php
   
			if ($action == 'editsCurrency')
			{
                //todas las monedas
                
                $sqlAllCurrencies = "SELECT code_iso, label, unicode";
                $sqlAllCurrencies.= " FROM ".MAIN_DB_PREFIX."c_currencies";
                $sqlAllCurrencies.= " WHERE active = 1";
                $sqlAllCurrencies.= " ORDER BY code_iso ASC";
                $sqlAllc    = $db->query($sqlAllCurrencies);
                $stringOptionSelectCurrencies="";
                if($sqlAllc==true){
                    for ($i=0;$i<=$db->num_rows($sqlSalesorder);$i++){
                        $row=$db->fetch_object($sqlSalesorder);
                        $stringOptionSelectCurrencies.="<option value='".$row->code_iso."'>".$row->label." ".$row->code_iso."".$row->unicode."</option>";
                    }
                }
                
                
                
                if(!empty($object->policy->fk_currency) or !isset($object->policy->fk_currency)){
                    // la moneda de la ov
                    $stringOptionSelectOv="<option value='".$sc->code_iso."'>".$object->policy->fk_currency."</option>";
                    $stringOptionSelectOv.=$stringOptionSelectCurrencies;
                    $stringOptionSelectCurrencies=$stringOptionSelectOv;
                }
                
                print '<form method="post" action="'.$_SERVER['PHP_SELF'].'?action=setCurrency&amp;id='.$object->id.'" name="formcurrency">';
                print '<input type="hidden" name="action" value="setCurrency">';
                print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
                print '<table class="nobordernopadding" cellpadding="0" cellspacing="0">';
                print '<tr>
                         <td>';
                if(!empty($object->policy->fk_currency) or !isset($object->policy->fk_currency)){
                    $form->select_currency($object->policy->fk_currency,'currency_val');
                }else{
                    // la moneda de la ov
                    $form->select_currency("USD",'currency_val');
                }
                print   '</td>';
                print   '<td align="left"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
                print '</tr></table></form>';
    
			}else {
                if(!empty($object->policy->fk_currency) or !isset($object->policy->fk_currency)){
               
                   echo $form->input_val_currency($object->policy->fk_currency,'currency_val');
                }else{
                    // la moneda de la ov
                   echo  $form->input_val_currency("USD",'currency_val');
                }
            }
           
			
		?></div>
	</div>
<?php } ?>
</div>
<!-- END PHP TEMPLATE NOTES-->
