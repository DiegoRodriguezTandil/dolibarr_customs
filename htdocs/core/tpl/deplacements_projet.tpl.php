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
    
    $langs->load("trips");
    $id_project= $object->id;
    
    if (! class_exists('Deplacement')) {
        require DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';
    }
    if (! class_exists('FormCompany')) {
        require DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
    }
    
    
    echo "
<script>

	$( document ).ready(function() {
	
	
			/*valida que los campos valor_unitario y valor_total de detalle pedido sean numericos pero permitan ingresar coma*/
			$(document).on('keydown', '.importe', function(e){
				 -1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190,188])||(/65|67|86|88/.test(e.keyCode)&&(e.ctrlKey===true||e.metaKey===true))&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()
			});
		
			/*controla el ingreso de datos con formato ###.###,## para los campos valor_unitario y valor_total de detalle pedido */
			$(document).on('keyup', '.importe', function () {
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
	
	
            function numberFormat($this){
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
    
    $module = $object->element;
    
    // Special cases
    if ($module == 'propal')				{ $permission=$user->rights->propale->creer; }
    elseif ($module == 'fichinter')			{ $permission=$user->rights->ficheinter->creer; }
    elseif ($module == 'invoice_supplier')	{ $permission=$user->rights->fournisseur->facture->creer; }
    elseif ($module == 'order_supplier')	{ $permission=$user->rights->fournisseur->commande->creer; }
    elseif (! isset($permission))			{ $permission=$user->rights->$module->creer; } // If already defined by caller page
    
    if($module=='propal')$document_code= "CO";
    if($module=='salesorder')$document_code= "OV";
    if($module=='commande')$document_code= "PR";
    if($module=='order_supplier')$document_code= "PO";
    
    $formcompany= new FormCompany($db);
    $gasto=new Deplacement($db);
    $userstatic=new User($db);
    $soc=new Societe($db);
    
    
    $action = GETPOST('action','alpha');
    
    if ($action == 'add' )
    {
        if (! GETPOST('cancel','alpha'))
        {
            $error=0;
            $importe_crudo= GETPOST('total_ht');
            $importe_format_dot	= empty($importe_crudo)   ? 0 : str_replace(".", "", $importe_crudo);
            $final_importe		= empty($importe_crudo)   ? 0 : str_replace(",", ".",$importe_format_dot);
            
            $gasto->date			= dol_mktime(12, 0, 0, GETPOST('remonth','int'), GETPOST('reday','int'), GETPOST('reyear','int'));
            $gasto->km				= GETPOST('km','int');
            //FEDE
            $gasto->total_ht		= $final_importe;
            $gasto->fk_currency		= GETPOST('fk_currency','alpha');
            //FIN FEDE
            $gasto->type			= GETPOST('type','alpha');
            $gasto->socid			= GETPOST('socid','int');
            $gasto->fk_user			= GETPOST('fk_user','int');
            $gasto->note_private	= GETPOST('note_private','alpha');
            $gasto->note_public		= GETPOST('note_public','alpha');
            $gasto->statut     		= 0;
            $gasto->fk_project      = GETPOST('id','int');
            $gasto->statut			=1;
            if (! $gasto->date)
            {
                $mesg=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Date"));
                $error++;
            }
            if ($gasto->type == '-1') 	// Otherwise it is TF_LUNCH,...
            {
                $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")).'</div>';
                $error++;
            }
            if (! ($gasto->fk_user > 0))
            {
                $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Person")).'</div>';
                $error++;
            }
            
            if (! $error)
            {
               
                $id = $gasto->create($user);
                if ($id > 0)
                {
                    header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $object->id);
                    //exit;
                }
                else
                {
                    $mesg=$gasto->error;
                    $action='create';
                }
            }
            else
            {
                $action='create';
            }
        }
        else
        {
            header("Location: gastos.php?id=".$object->id);
            //exit;
        }
    }
    
   
    if ($action == 'create')
    {
        //WYSIWYG Editor
        require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
        
        print_fiche_titre($langs->trans("NewTrip"));
        
        dol_htmloutput_errors($mesg);
        
        $datec = dol_mktime(12, 0, 0, GETPOST('remonth','int'), GETPOST('reday','int'), GETPOST('reyear','int'));
        
        print '<form name="add" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '" method="POST">' . "\n";
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="add">';
        
        print '<table class="border" width="100%">';
        
        print "<tr>";
        print '<td width="25%" class="fieldrequired">'.$langs->trans("Type").'</td><td>';
        print $form->select_type_fees_projet(GETPOST('type','int'),'type',1);
        print '</td></tr>';
        
        print "<tr>";
        print '<td class="fieldrequired">'.$langs->trans("Person").'</td><td>';
        print $form->select_users(GETPOST('fk_user','int'),'fk_user',1);
        print '</td></tr>';
        
        print "<tr>";
        print '<td class="fieldrequired">'.$langs->trans("Date").'</td><td>';
        print $form->select_date($datec?$datec:-1,'','','','','add',1,1);
        print '</td></tr>';
        
        // Km
        print '<tr><td class="fieldrequired">'.$langs->trans("Km").'</td><td><input name="km" size="10" value="' . GETPOST("km") . '"></td></tr>';
        
        //FEDE Importe
        print '<tr><td class="fieldrequired">'.$langs->trans("Importe").'</td><td>';
        print '<input class="importe" name="total_ht" class="flat" size="10" value="'.price($gasto->total_ht).'">';
        print $form->select_currency($object->fk_currency,'fk_currency');
        print '</td></tr>';
        
        // Company
        print "<tr>";
        print '<td>'.$langs->trans("CompanyVisited").'</td><td>';
        print $form->select_company(GETPOST('socid','int'),'socid','',1);
        print '</td></tr>';
        
        // Public note
        print '<tr>';
        print '<td class="border" valign="top">'.$langs->trans('NotePublic').'</td>';
        print '<td valign="top" colspan="2">';
        
        $doleditor = new DolEditor('note_public', GETPOST('note_public', 'alpha'), 600, 200, 'dolibarr_notes', 'In', false, true, true, ROWS_8, 100);
        print $doleditor->Create(1);
        
        print '</td></tr>';
        
        // Private note
        if (! $user->societe_id)
        {
            print '<tr>';
            print '<td class="border" valign="top">'.$langs->trans('NotePrivate').'</td>';
            print '<td valign="top" colspan="2">';
            
            $doleditor = new DolEditor('note_private', GETPOST('note_private', 'alpha'), 600, 200, 'dolibarr_notes', 'In', false, true, true, ROWS_8, 100);
            print $doleditor->Create(1);
            
            print '</td></tr>';
        }
        
        // Other attributes
        $parameters=array('colspan' => ' colspan="2"');
        $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
        
        print '</table>';
        
        print '<br><center><input class="button" type="submit" value="'.$langs->trans("Save").'"> &nbsp; &nbsp; ';
        print '<input class="button" type="submit" name="cancel" value="'.$langs->trans("Cancel").'"></center';
        
        print '</form><br><br>';
    }
    else
    {
        print "\n<div class=\"tabsAction\">\n";
        print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=create">';
        print $langs->trans("Registrar Gasto").'</a>';
        print "\n</div>\n<BR>";
    }
    
    
  /*  if($allowDelete ===0 ) {
        echo "
            <h3>
              <i style='color: red'>
                 No se puede eliminar el gasto ".$id_not_allow_Delete.". Tiene asociada una cotización.
              </i>
            </h3>";
    }
    */
    ?>


<!-- BEGIN PHP TEMPLATE CONTACTS -->
<table class="noborder allwidth">
    
    <tr class="liste_titre">
        <td><?php echo $langs->trans("Ref"); ?></td>
        <td><?php echo $langs->trans("Fecha"); ?></td>
        <td><?php echo $langs->trans("Tipo"); ?></td>
        <td><?php echo $langs->trans("Empresa"); ?></td>
        <td><?php echo $langs->trans("Detalle"); ?></td>
        <td><?php echo $langs->trans("Moneda"); ?></td>
        <td><?php echo $langs->trans("Importe"); ?></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    
    <?php $var=true; ?>
    
    <?php
        
        $tab = $object->liste_deplacements_projet($document_code,$object->id,-1,null,$id_project);
        $num=count($tab);
        $i = 0;
        while ($i < $num) {
            $var = !$var;
            if(is_array($tab[$i]) && isset($tab[$i])){
            $soc->fetch($tab[$i]['fk_soc']);
            ?>

                <?php
                    if($allowDelete ===0 && $id_not_allow_Delete===$tab[$i]['rowid']) {
                        echo "
                        <tr style='background-color:#A9A9A9;margin:0;padding: 0;'>
                            <td colspan='8'>
                                <span>
                                    <b>
                                       <i style='color: black'>
                                         Está seguro que desea eliminar el gasto ". $tab[$i]['rowid'].", al cual se le ha realizado una cotización?.
                                       </i>
                                    </b>
                                    <a  class='butAction' href='".DOL_URL_ROOT."/projet/gastosGenerales.php?id=".$object->id."&action=deletedeplacementCozation&lineid=".$tab[$i]['rowid']."'> Elimnar<a>
                                    <a  class='butAction' href='".DOL_URL_ROOT."/projet/gastosGenerales.php?id=".$object->id."'> Cancelar<a>
                                </span>
                            </td>
                        </tr>
                        <tr style='background-color:#A9A9A9' valign='top'>";
                    }else{
                        echo "<tr".$bc[$var]." valign='top'>";
                    }    ?>
             
                
                <td  ><?php echo '<a href="'.DOL_URL_ROOT.'/compta/deplacement/fiche.php?id='.      $tab[$i]['rowid'].'">'.$tab[$i]['rowid'].'</a>';?></td>
                <td><?php echo dol_print_date($db->jdate($tab[$i]['dated']),'day'); ?></td>
                <td><?php echo $langs->trans($tab[$i]['type']); ?></td>
                <td>
                    <?php
                        if ($tab[$i]['fk_soc']) echo $soc->getNomUrl(1);
                    ?>
                <td align="left">
                    <?php
                        if( is_array($tab[$i]) && array_key_exists('note_public',$tab[$i]) && isset($tab[$i]['note_public']) ){
                            echo substr($tab[$i]['note_public'], 0, 55). "...";
                        }else{
                            echo "-";
                        }
                    ?>
                </td>
                <td><?php echo $tab[$i]['fk_currency']; ?></td>
                <td align="left"><?php echo price($tab[$i]['total_ht']); ?></td>
                <td align="center" nowrap="nowrap" colspan="2">
                    <?php if ($permission) { ?>
                        &nbsp;<a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deletedeplacement&amp;lineid='.$tab[$i]['rowid']; ?>"><?php echo img_delete(); ?></a>
                    <?php }
                        if ($permission) {
                            echo "<a target='black' href='".DOL_URL_ROOT."/compta/deplacement/fiche.php?action=edit&id=".$tab[$i]['rowid']."'>".img_edit()."</a>";
                       }
                    ?>
                </td>
            </tr>
            
            <?php
                }
                $i++;
            ?>
    <?php } ?>

</table>
<!-- END PHP TEMPLATE CONTACTS -->