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
require_once 'class/exchangerate.class.php';
//require_once 'exchangerate.class.php';
require_once '../core/lib/date.lib.php';

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
if (! $sortfield) $sortfield="f.date";
$limit = $conf->liste_limit;


$search_ref=GETPOST('search_ref','alpha');
$search_company=GETPOST('search_company','alpha');
$search_desc=GETPOST('search_desc','alpha');


/*
 *	View
 */

llxHeader();


$sql = "SELECT f.date, f.source, f.target, f.rate FROM llx_currency_conversion as f";
if ($search_ref)     $sql .= " WHERE f.date LIKE '%".$db->escape($search_ref)."%'";
if ($search_desc)     $sql .= " WHERE f.target LIKE '%".$db->escape($search_desc)."%'";
if ($search_company)     $sql .= " WHERE f.source LIKE '%".$db->escape($search_company)."%'";
$sql.= " ORDER BY ".$sortfield." ".$sortorder;

//$sql.= " ORDER BY ".$sortfield." ".$sortorder;
$sql.= $db->plimit($limit+1, $offset);

$result=$db->query($sql);
if ($result)
{
	$num = $db->num_rows($result);

	
	//$interventionstatic=new Fichinter($db);
	$interventionstatic=new ExchangeRate($db);

     $urlparam="&amp;socid=$socid";
   
	//print_barre_liste($langs->trans("Exchange Rate"), $page, $_SERVER['PHP_SELF'], $urlparam, $sortfield, $sortorder, '', $num);
	print_barre_liste($langs->trans("Exchange Rate"), $page, $_SERVER['PHP_SELF'], $urlparam, $sortfield, $sortorder, '', $num);

	print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
	print '<table class="noborder" width="100%">';

	print '<tr class="liste_titre">';
	print_liste_field_titre($langs->trans("Date"),$_SERVER["PHP_SELF"],"f.date","",$urlparam,'width="15%"',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Currency From"),$_SERVER["PHP_SELF"],"f.source","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Currency To"),$_SERVER["PHP_SELF"],"f.target","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre($langs->trans("Exchange Rate"),$_SERVER["PHP_SELF"],"f.rate","",$urlparam,'',$sortfield,$sortorder);
	print_liste_field_titre('',$_SERVER["PHP_SELF"],'');
	
	print "</tr>\n";

	print '<tr class="liste_titre">';
	print '<td class="liste_titre">';
	print '<input type="text" class="flat" name="search_ref" value="'.$search_ref.'" size="8">';
	print '</td><td class="liste_titre">';
	print '<input type="text" class="flat" name="search_company" value="'.$search_company.'" size="10">';
	print '</td><td class="liste_titre">';
	print '<input type="text" class="flat" name="search_desc" value="'.$search_desc.'" size="12">';
	print '</td>';
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
		
		
		
		
		
		
        print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->date,20)).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->source,20)).'</td>';
		//print '<td align="center">'.dol_print_date($db->jdate($objp->dp),'dayhour')."</td>\n";
		//print '<td align="right">'.convertSecondToTime($objp->duree).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->target,20)).'</td>';
		print '<td>'.dol_htmlentitiesbr(dol_trunc($objp->rate,20)).'</td>';
		//$passdate = dol_htmlentitiesbr(dol_trunc($objp->date,20));
		        //print '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">'."\n";
				//$passdate = dol_htmlentitiesbr(dol_trunc($objp->date,20));
		       
			   /*
			   $passdate = $objp->date;
				print '<form action="delete.php" method="GET">'."\n";
				
				print '<td><input type="hidden" value="' .$passdate . '" name="date">';
				print '<input type="submit" value="Delete">';
				print '</form></td>';	*/	
				
				
				print "<td>";
		//$interventionstatic->id=$objp->fichid;
		//$interventionstatic->ref=$objp->ref;
		$interventionstatic->date=$objp->date;
		print $interventionstatic->getNomUrl(1);
        print "</td>\n";

		print "</tr>\n";

		$total += $objp->duree;
		$i++;
	}
	//print '<tr class="liste_total"><td colspan="5" class="liste_total">'.$langs->trans("Total").'</td>';
	//print '<td align="right" nowrap="nowrap" class="liste_total">'.convertSecondToTime($total).'</td><td>&nbsp;</td>';
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
