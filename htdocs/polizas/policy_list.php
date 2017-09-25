<?php
/* Copyright (C) 2002-2003	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 * Copyright (C) 2011-2012	Juanjo Menent			<jmenent@2byte.es>
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
 *	\file       htdocs/fichinter/list.php
 *	\brief      List of all interventions
 *	\ingroup    ficheinter
 */

require '../main.inc.php';
require_once '../contact/class/contact.class.php';
//require_once '../fichinter/class/exchangerate.class.php';
//require_once 'exchangerate.class.php';
require_once '../core/lib/date.lib.php';
require_once 'policynew.class.php';

$langs->load("companies");
$langs->load("exchangerate");

$socid=GETPOST('socid','int');

// Security check
$fichinterid = GETPOST('id','int');
if ($user->societe_id) $socid=$user->societe_id;
//$result = restrictedArea($user, 'ficheinter', $fichinterid,'fichinter');

$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="DESC";
if (! $sortfield) $sortfield="p.expiration_date";
$limit = $conf->liste_limit;


$search_ref=GETPOST('search_ref','alpha');
$search_company=GETPOST('search_company','alpha');
$search_desc=GETPOST('search_desc','alpha');


/*
 *	View
 */

llxHeader();


$sql = "SELECT p.rowid, p.fk_docid, p.fk_doctype, p.fk_soc, p.ref, p.expiration_date";
$sql.= ", p.price, p.endorsement"; 
$sql.= ",s.nom";
$sql.= " FROM ".MAIN_DB_PREFIX."policy as p";
//
$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON p.fk_soc = s.rowid";
 
//

if ($search_ref)     $sql .= " WHERE p.rowid LIKE '%".$db->escape($search_ref)."%'";
if ($search_desc)     $sql .= " WHERE p.fk_doctype LIKE '%".$db->escape($search_desc)."%'";
if ($search_company)     $sql .= " WHERE p.expiration_date LIKE '%".$db->escape($search_company)."%'"; 
$sql.= " ORDER BY ".$sortfield." ".$sortorder;

//$sql.= " ORDER BY ".$sortfield." ".$sortorder;
$sql.= $db->plimit($limit+1, $offset);

$result=$db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);

	
	//$interventionstatic=new Fichinter($db);
	$interventionstatic=new Policy($db);

     $urlparam="&amp;socid=$socid";
   

	print_barre_liste($langs->trans("Insurance Policies"), $page, $_SERVER['PHP_SELF'], $urlparam, $sortfield, $sortorder, '', $num);

	print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<table class="noborder" width="100%">';

	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Policy ID"),$_SERVER["PHP_SELF"],"p.rowid","",$urlparam,'width="15%"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Document ID"),$_SERVER["PHP_SELF"],"p.fk_docid","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Document Type"),$_SERVER["PHP_SELF"],"p.fk_doctype","",$urlparam,'',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Third Party"),$_SERVER["PHP_SELF"],"p.fk_soc","",$urlparam,'',$sortfield,$sortorder);
	//print_liste_field_titre($langs->trans("Ref"),$_SERVER["PHP_SELF"],"p.ref","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Endorsement"),$_SERVER["PHP_SELF"],"p.endorsement","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Expiration Date"),$_SERVER["PHP_SELF"],"p.expiration_date","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Price"),$_SERVER["PHP_SELF"],"p.price","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Aseguradora"),$_SERVER["PHP_SELF"],"s.nom","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre('',$_SERVER["PHP_SELF"],'');
	print'<td></td><td></td><td></td><td></td><td></td>';
	
	print "</tr>\n";

	print '<tr class="liste_titre">';
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="8">';
	print '</td><td class="liste_titre"></td><td class="liste_titre">';
	print '<input type="text" class="flat" name="search_desc" value="'.$search_desc.'" size="10">';
	print '</td><td class="liste_titre"></td><td class="liste_titre">';
	print '<input type="text" class="flat" name="search_company" value="'.$search_company.'" size="12">';
	print '</td><td></td><td></td><td></td><td></td><td></td>';
	print '<td class="liste_titre">&nbsp;</td>';
	print '<td class="liste_titre" align="right"><input class="liste_titre" type="image" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'"></td>';
	print "</tr>\n";

	$companystatic=new Societe($db);

	$var=True;
	$total = 0;
	$i = 0;
	
	
	
	
	while ($i < min($num, $limit))


	{
    	$objp = $db->fetch_object($result);
		$var=!$var;
		print "<tr $bc[$var]>";
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->rowid,20)).'</td>';
        print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->fk_docid,20)).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->fk_doctype,20)).'</td>';
		//print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->fk_soc,20)).'</td>';
		//print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->fk_ref,20)).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->endorsement,20)).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->expiration_date,20)).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->price,20)).'</td>';
		//print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->status,20)).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->nom,20)).'</td>';
		
				
				
				print "<td>";
		
		$interventionstatic->fk_doctype=$objp->fk_doctype;
		$interventionstatic->fk_docid=$objp->fk_docid;
		print $interventionstatic->getNomUrl(1);
        print "</td>\n";

		print "</tr>\n";

		$total += $objp->duree;
		$i++;
	}

	print '</tr>';

	print '</table>';
	print "</form>\n";
	$db->free($result);
	
	llxFooter();
}
else
{
	dol_print_error($db);
}

$db->close();

llxFooter();
?>
