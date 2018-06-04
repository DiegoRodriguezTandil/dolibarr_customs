<?php
    /* Copyright (C) 2005      Patrick Rouillon     <patrick@rouillon.net>
     * Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
     * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
     * Copyright (C) 2011-2012 Philippe Grand       <philippe.grand@atoo-net.com>
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
     *     \file       htdocs/salesorder/contact.php
     *     \ingroup    salesorder
     *     \brief      Onglet de gestion des contacts de salesorder
     */
    
    require '../main.inc.php';
    require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
    require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
    require_once DOL_DOCUMENT_ROOT.'/compta/deplacement/class/deplacement.class.php';
    require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
    
    $langs->load("orders");
    $langs->load("sendings");
    $langs->load("companies");
    
    $id=GETPOST('id','int');
    $ref=GETPOST('ref','alpha');
    $action=GETPOST('action','alpha');
 
    
    $object = new Commande($db);
    
    /*
     * Ajout d'un nouveau contact
     */
    
    if ($action == 'addcontact' && $user->rights->salesorder->creer)
    {
        $result = $object->fetch($id);
        
        if ($result > 0 && $id > 0)
        {
            $contactid = (GETPOST('userid','int') ? GETPOST('userid','int') : GETPOST('contactid','int'));
            $result = $object->add_contact($contactid, $_POST["type"], $_POST["source"]);
        }
        
        if ($result >= 0)
        {
            header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
            exit;
        }
        else
        {
            if ($object->error == 'DB_ERROR_RECORD_ALREADY_EXISTS')
            {
                $langs->load("errors");
                $mesg = '<div class="error">'.$langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType").'</div>';
            }
            else
            {
                $mesg = '<div class="error">'.$object->error.'</div>';
            }
        }
    }

// bascule du statut d'un contact
    else if ($action == 'swapstatut' && $user->rights->salesorder->creer)
    {
        if ($object->fetch($id))
        {
            $result=$object->swapContactStatus(GETPOST('ligne'));
        }
        else
        {
            dol_print_error($db);
        }
    }
    else if ($action == 'deletedeplacementCozation' && $user->rights->salesorder->creer)
    {
        $object->fetch($id);
        $allow=0;
        $lineid=$_GET["lineid"];
        $allow=$object->allow_delete_deplacement($lineid);
        if($allow==0){
           if($object->delete_cotization($lineid)){
               $result = $object->delete_deplacement($lineid);
           }
        }
    }
// Efface un contact
    else if ($action == 'deletedeplacement' && $user->rights->salesorder->creer)
    {
        $object->fetch($id);
        $lineid=$_GET["lineid"];
        $allowDelete=$object->allow_delete_deplacement($lineid);
        if($allowDelete==1){
            $result = $object->delete_deplacement($lineid);
        }else{
            $id_not_allow_Delete=$lineid;
        }
        
    }
    
    
    else if ($action == 'setaddress' && $user->rights->salesorder->creer)
    {
        $object->fetch($id);
        $result=$object->setDeliveryAddress($_POST['fk_address']);
        if ($result < 0) dol_print_error($db,$object->error);
    }
    
    /*
     * View
     */
    
    llxHeader('',$langs->trans('Order'),'EN:Customers_Orders|FR:Salesorders_Clients|ES:Pedidos de clientes');
    
    $form = new Form($db);
    $formcompany = new FormCompany($db);
    $formother = new FormOther($db);
    $contactstatic=new Deplacement($db);
    $userstatic=new User($db);
    
    /* *************************************************************************** */
    /*                                                                             */
    /* Mode vue et edition                                                         */
    /*                                                                             */
    /* *************************************************************************** */
    dol_htmloutput_mesg($mesg);
    
    if ($id > 0 || ! empty($ref))
    {
        $langs->trans("OrderCard");
        
        if ($object->fetch($id, $ref) > 0)
        {
            $soc = new Societe($db);
            $soc->fetch($object->socid);
            
            $project = new Project($db);
            $project->fetch($_GET["id"],$_GET["ref"]);
            $project->societe->fetch($project->societe->id);
            $head = project_prepare_head($project);
 
            dol_fiche_head($head, 'gastosGenerales', $langs->trans("Project"),0,($project->public?'projectpub':'project'));
            
            /*
             *   Facture synthese pour rappel
             */
            
            print '<table class="border" width="100%">';
            
            $linkback = '<a href="'.DOL_URL_ROOT.'/commande/liste.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';
            
                // Ref
                print '<tr>
                           <td width="18%">'.$langs->trans("Ref").'</td><td colspan="3">';
                print           $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref');
                print "    </td>
                      </tr>";
                
                // Ref salesorder client
                print '<tr>
                          <td>';
                print '      <table class="nobordernopadding" width="100%">
                                <tr>
                                   <td nowrap>';
                print                $langs->trans('RefCustomer').'
                                   </td>
                                   <td align="left">
                                   </td>
                                 </tr>
                             </table>';
                print   '</td>
                         <td colspan="3">';
                print       $object->ref_client;
                print   '</td>
                      </tr>';
            
            // Customer
            if (is_null($object->client))	$object->fetch_thirdparty();
                
                print "<tr>
                        <td>".$langs->trans("Company")."
                        </td>";
                print  '<td colspan="3">'.$object->client->getNomUrl(1).'
                        </td>
                       </tr>';
        print "</table>
            </div>
        <br>";
  
            
            // Contacts lines (modules that overwrite templates must declare this into descriptor)
            $dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl'));
            foreach($dirtpls as $reldir)
            {
                
                //$res=@include dol_buildpath($reldir.'/contacts.tpl.php');
                $res=@include dol_buildpath($reldir.'/deplacements_projet.tpl.php');
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