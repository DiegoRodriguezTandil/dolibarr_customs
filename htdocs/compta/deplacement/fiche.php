<?php
/* Copyright (C) 2003		Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012	Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2012		Juanjo Menent        <jmenent@2byte.es>
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
 *  \file       	htdocs/compta/deplacement/fiche.php
 *  \brief      	Page to show a trip card
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/trip.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
if (! empty($conf->projet->enabled))
{
    require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
}
    
    

$langs->load("trips");


// Security check
$id = GETPOST('id','int');
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'deplacement', $id,'');

$action = GETPOST('action','alpha');
$confirm = GETPOST('confirm','alpha');

$mesg = '';

$object = new Deplacement($db);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
$hookmanager=new HookManager($db);
$hookmanager->initHooks(array('tripsandexpensescard'));

/*
 * Actions
*/
if ($action == 'validate' && $user->rights->deplacement->creer)
{
    $object->fetch($id);
    if ($object->statut == 0)
    {
        $result = $object->setStatut(1);
        if ($result > 0)
        {
            header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
            exit;
        }
        else
        {
            $mesg=$object->error;
        }
    }
}

/*
else if ($action == 'unblock' && $user->rights->deplacement->unvalidate)
{
    $object->fetch($id);
    if ($object->fk_statut == '1') 	// Not blocked...
    {
        $mesg='<div class="error">'.$langs->trans("Error").'</div>';
        $action='';
        $error++;
    }
    else
    {
        $result = $object->fetch($id);

        $object->fk_statut	= '1';

        $result = $object->update($user);

        if ($result > 0)
        {
            header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
            exit;
        }
        else
        {
            $mesg=$object->error;
        }
    }
}*/

else if ($action == 'confirm_delete' && $confirm == "yes" && $user->rights->deplacement->supprimer)
{
    $result=$object->delete($id);
    if ($result >= 0)
    {
        header("Location: index.php");
        exit;
    }
    else
    {
        $mesg=$object->error;
    }
}

else if ($action == 'add' && $user->rights->deplacement->creer)
{
    if (! GETPOST('cancel','alpha'))
    {
        $error=0;
        $importe_format_dot=0;
        $final_importe=0;
        $importe_crudo= GETPOST('total_ht');
        $importe_format_dot	= empty($importe_crudo)   ? 0 : str_replace(".", "", $importe_crudo);
        $final_importe		= empty($importe_crudo)   ? 0 : str_replace(",", ".",$importe_format_dot);
        $object->date			= dol_mktime(12, 0, 0, GETPOST('remonth','int'), GETPOST('reday','int'), GETPOST('reyear','int'));
        $object->km				= GETPOST('km','int');
		//FEDE
		$object->total_ht		= $final_importe;
		$object->fk_currency	= GETPOST('fk_currency','alpha');
		//FIN FEDE
        $object->type			= GETPOST('type','alpha');
        $object->socid			= GETPOST('socid','int');
        $object->fk_user		= GETPOST('fk_user','int');
        $object->note_private	= GETPOST('note_private','alpha');
        $object->note_public	= GETPOST('note_public','alpha');
        $object->statut     	= 0;

        if (! $object->date)
        {
            $mesg=$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Date"));
            $error++;
        }
        if ($object->type == '-1') 	// Otherwise it is TF_LUNCH,...
        {
            $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Type")).'</div>';
            $error++;
        }
        if (! ($object->fk_user > 0))
        {
            $mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Person")).'</div>';
            $error++;
        }

        if (! $error)
        {
            $id = $object->create($user);

            if ($id > 0)
            {
                header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
                exit;
            }
            else
            {
                $mesg=$object->error;
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
        header("Location: index.php");
        exit;
    }
}

// Update record
else if ($action == 'update' && $user->rights->deplacement->creer)
{
    if (! GETPOST('cancel','alpha'))
    {
        $result = $object->fetch($id);
        $importe_format_dot=0;
        $final_importe=0;
        $importe_crudo= GETPOST('total_ht');
        $importe_format_dot	= empty($importe_crudo)   ? 0 : str_replace(".", "", $importe_crudo);
        $final_importe		= empty($importe_crudo)   ? 0 : str_replace(",", ".",$importe_format_dot);
        $object->date			= dol_mktime(12, 0, 0, GETPOST('remonth','int'), GETPOST('reday','int'), GETPOST('reyear','int'));
        $object->km				= GETPOST('km','int');
        $object->total_ht		= $final_importe;
        $object->fk_currency	= GETPOST('fk_currency','alpha');
        $object->type			= GETPOST('type','alpha');
        $object->socid			= GETPOST('socid','int');
        $object->fk_user		= GETPOST('fk_user','int');
        $object->note_private	= GETPOST('note_private','alpha');
        $object->note_public	= GETPOST('note_public','alpha');
        
        $result = $object->update($user);
        
        if ($result > 0)
        {
            header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
            exit;
        }
        else
        {
            $mesg=$object->error;
        }
    }
    else
    {
        header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
        exit;
    }
}

// Set into a project
else if ($action == 'classin' && $user->rights->deplacement->creer)
{
    $object->fetch($id);
    $result=$object->setProject(GETPOST('projectid','int'));
    if ($result < 0) dol_print_error($db, $object->error);
}

// Set fields
else if ($action == 'setdated' && $user->rights->deplacement->creer)
{
    $dated=dol_mktime(GETPOST('datedhour','int'), GETPOST('datedmin','int'), GETPOST('datedsec','int'), GETPOST('datedmonth','int'), GETPOST('datedday','int'), GETPOST('datedyear','int'));
    $object->fetch($id);
    $result=$object->setValueFrom('dated',$dated,'','','date');
    if ($result < 0) dol_print_error($db, $object->error);
}
else if ($action == 'setkm' && $user->rights->deplacement->creer)
{
    $object->fetch($id);
    $result=$object->setValueFrom('km',GETPOST('km','int'));
    if ($result < 0) dol_print_error($db, $object->error);
}
else if ($action == 'setnote_public' && $user->rights->deplacement->creer)
{
    $object->fetch($id);
    $result=$object->setValueFrom('note_public',GETPOST('note_public','alpha'));
    if ($result < 0) dol_print_error($db, $object->error);
}
else if ($action == 'setnote' && $user->rights->deplacement->creer)
{
    $object->fetch($id);
    $result=$object->setValueFrom('note',GETPOST('note','alpha'));
    if ($result < 0) dol_print_error($db, $object->error);
}


/*
 * View
*/

llxHeader();

$form = new Form($db);

/*
 * Action create
*/
if ($action == 'create')
{
    //WYSIWYG Editor
    require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

    print_fiche_titre($langs->trans("NewTrip"));

    dol_htmloutput_errors($mesg);

    $datec = dol_mktime(12, 0, 0, GETPOST('remonth','int'), GETPOST('reday','int'), GETPOST('reyear','int'));

    print '<form name="add" action="' . $_SERVER["PHP_SELF"] . '" method="POST">' . "\n";
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';

    print '<table class="border" width="100%">';

    print "<tr>";
    print '<td width="25%" class="fieldrequired">'.$langs->trans("Type").'</td><td>';
    print $form->select_type_fees(GETPOST('type','int'),'type',1);
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
    print '<tr><td class="fieldrequired">'.$langs->trans("FeesKilometersOrAmout").'</td><td><input name="km" size="10" value="' . GETPOST("km") . '"></td></tr>';
	
	//FEDE Importe
	print '<tr><td class="fieldrequired">'.$langs->trans("Importe").'</td><td>';
	print '<input name="total_ht" class="flat" size="10" value="'.price($object->total_ht).'">';
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

    print '</form>';
}
else if ($id)
{
    $result = $object->fetch($id);
    if ($result > 0)
    {
        dol_htmloutput_mesg($mesg);

        $head = trip_prepare_head($object);

        dol_fiche_head($head, 'card', $langs->trans("TripCard"), 0, 'trip');

        if ($action == 'edit' && $user->rights->deplacement->creer)
        {
            //WYSIWYG Editor
            require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

            $soc = new Societe($db);
            if ($object->socid)
            {
                $soc->fetch($object->socid);
            }

            print '<form name="update" action="' . $_SERVER["PHP_SELF"] . '" method="POST">' . "\n";
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="action" value="update">';
            print '<input type="hidden" name="id" value="'.$id.'">';

            print '<table class="border" width="100%">';

            // Ref
            print "<tr>";
            print '<td width="20%">'.$langs->trans("Ref").'</td><td>';
            print $object->ref;
            print '</td></tr>';

            // Type
            print "<tr>";
            print '<td class="fieldrequired">'.$langs->trans("Type").'</td><td>';
            print $form->select_type_fees(GETPOST('type','int')?GETPOST('type','int'):$object->type,'type',0);
            print '</td></tr>';

            // Who
            print "<tr>";
            print '<td class="fieldrequired">'.$langs->trans("Person").'</td><td>';
            print $form->select_users(GETPOST('fk_user','int')?GETPOST('fk_user','int'):$object->fk_user,'fk_user',0);
            print '</td></tr>';

            // Date
            print '<tr><td class="fieldrequired">'.$langs->trans("Date").'</td><td>';
            print $form->select_date($object->date,'','','','','update');
            print '</td></tr>';

            // Km
            print '<tr><td class="fieldrequired">'.$langs->trans("Kilometros").'</td><td>';
            print '<input name="km" class="flat" size="10" value="'.$object->km.'">';
            print '</td></tr>';
					
			//FEDE Importe
            print '<tr><td class="fieldrequired">'.$langs->trans("Importe").'</td><td>';
            print '<input name="total_ht" class="_importe flat " size="10" value="'.price($object->total_ht).'">';
			print $form->select_currency($object->fk_currency,'fk_currency');
            print '</td></tr>';

            // Where
            print "<tr>";
            print '<td>'.$langs->trans("CompanyVisited").'</td><td>';
            print $form->select_company($soc->id,'socid','',1);
            print '</td></tr>';

            // Public note
            print '<tr><td valign="top">'.$langs->trans("NotePublic").'</td>';
            print '<td valign="top" colspan="3">';

            $doleditor = new DolEditor('note_public', $object->note_public, 600, 200, 'dolibarr_notes', 'In', false, true, true, ROWS_8, '100');
            print $doleditor->Create(1);
            
            print "</td></tr>";

            // Private note
            if (! $user->societe_id)
            {
                print '<tr><td valign="top">'.$langs->trans("NotePrivate").'</td>';
                print '<td valign="top" colspan="3">';

                $doleditor = new DolEditor('note_private', $object->note_private, 600, 200, 'dolibarr_notes', 'In', false, true, true, ROWS_8, '100');
                print $doleditor->Create(1);

                print "</td></tr>";
            }

            // Other attributes
            $parameters=array('colspan' => ' colspan="3"');
            $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook

            print '</table>';

            print '<br><center><input type="submit" class="button" value="'.$langs->trans("Save").'"> &nbsp; ';
            print '<input type="submit" name="cancel" class="button" value="'.$langs->trans("Cancel").'">';
            print '</center>';

            print '</form>';

            print '</div>';
        }
        else
        {
            /*
             * Confirmation de la suppression du deplacement
            */
            if ($action == 'delete')
            {
                $ret=$form->form_confirm($_SERVER["PHP_SELF"]."?id=".$id,$langs->trans("DeleteTrip"),$langs->trans("ConfirmDeleteTrip"),"confirm_delete");
                if ($ret == 'html') print '<br>';
            }

            $soc = new Societe($db);
            if ($object->socid) $soc->fetch($object->socid);

            print '<table class="border" width="100%">';

            $linkback = '<a href="'.DOL_URL_ROOT.'/compta/deplacement/list.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

            // Ref
            print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td>';
            print $form->showrefnav($object, 'id', $linkback, 1, 'rowid', 'ref', '');
            print '</td></tr>';

            // Type
            print '<tr><td>';
            print $form->editfieldkey("Type",'type',$langs->trans($object->type),$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'select:types_fees');
            print '</td><td>';
            print $form->editfieldval("Type",'type',$langs->trans($object->type),$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'select:types_fees');
            print '</td></tr>';

            // Who
            print '<tr><td>'.$langs->trans("Person").'</td><td>';
            $userfee=new User($db);
            $userfee->fetch($object->fk_user);
            print $userfee->getNomUrl(1);
            print '</td></tr>';

            // Date
            print '<tr><td>';
            print $form->editfieldkey("Date",'dated',$object->date,$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'datepicker');
            print '</td><td>';
            print $form->editfieldval("Date",'dated',$object->date,$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'datepicker');
            print '</td></tr>';

            // Km/Price
            print '<tr><td valign="top">';
            print $form->editfieldkey("Kilometros",'km',$object->km,$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'numeric:6');
            print '</td><td>';
            print $form->editfieldval("Kilometros",'km',$object->km,$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'numeric:6');
            print "</td></tr>";
			
			//FEDE
			print '<tr><td valign="top">Divisa</td><td>';
			print $langs->trans("Currency".$object->fk_currency);
			print "</td></tr>";
			
			// FEDE Importe
            print '<tr><td valign="top">';
            print $form->editfieldkey("Importe",'total_ht',price($object->total_ht),$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'numeric:6');
            print '</td><td>';
            print $form->editfieldval("Importe",'total_ht',price($object->total_ht),$object,$conf->global->MAIN_EDIT_ALSO_INLINE && $user->rights->deplacement->creer,'numeric:6');
            print "</td></tr>";

            // Where
            print '<tr><td>'.$langs->trans("CompanyVisited").'</td>';
            print '<td>';
            if ($soc->id) print $soc->getNomUrl(1);
            print '</td></tr>';

            // Project
            if (! empty($conf->projet->enabled))
            {
                $langs->load('projects');
                print '<tr>';
                print '<td>';

                print '<table class="nobordernopadding" width="100%"><tr><td>';
                print $langs->trans('Project');
                print '</td>';
                if ($action != 'classify' && $user->rights->deplacement->creer)
                {
                    print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=classify&amp;id='.$object->id.'">';
                    print img_edit($langs->trans('SetProject'),1);
                    print '</a></td>';
                }
                print '</tr></table>';
                print '</td><td colspan="3">';
                if ($action == 'classify')
                {
                    $form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, $object->fk_project,'projectid');
                }
                else
                {
                    $form->form_project($_SERVER['PHP_SELF'].'?id='.$object->id, $object->socid, $object->fk_project,'none');
                }
                print '</td>';
                print '</tr>';
            }

            // Statut
            print '<tr><td>'.$langs->trans("Status").'</td><td>'.$object->getLibStatut(4).'</td></tr>';

            // Other attributes
            $parameters=array('colspan' => ' colspan="3"');
            $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook

            print "</table><br>";

            // Notes
            $blocname = 'notes';
            $title = $langs->trans('Notes');
            include DOL_DOCUMENT_ROOT.'/core/tpl/bloc_showhide.tpl.php';

            print '</div>';

            /*
             * Barre d'actions
            */

            print '<div class="tabsAction">';

            if ($object->statut == 0) 	// if blocked...
            {
                if ($user->rights->deplacement->creer)
                {
                    print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=validate&id='.$id.'">'.$langs->trans('Validate').'</a>';
                }
                else
                {
                    print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans('Validate').'</a>';
                }
            }

            if ($user->rights->deplacement->creer)
            {
                print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&id='.$id.'">'.$langs->trans('Modify').'</a>';
            }
            else
            {
                print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans('Modify').'</a>';
            }
            if ($user->rights->deplacement->supprimer)
            {
                print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?action=delete&id='.$id.'">'.$langs->trans('Delete').'</a>';
            }
            else
            {
                print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans('Delete').'</a>';
            }

            print '</div>';
        }
    }
    else
    {
        dol_print_error($db);
    }
}
    
    echo "
<script>
	$(document).ready(function() {
		
			/*valida que los campos valor_unitario y valor_total de detalle pedido sean numericos pero permitan ingresar coma*/
			$(document).on('keydown', '._importe', function(e){
				 -1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190,188])||(/65|67|86|88/.test(e.keyCode)&&(e.ctrlKey===true||e.metaKey===true))&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()
			});
		
			/*controla el ingreso de datos con formato ###.###,## para los campos valor_unitario y valor_total de detalle pedido */
			$(document).on('keyup', '._importe', function () {
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

$db->close();

llxFooter();
?>
