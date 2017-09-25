<?php
/* Copyright (C) 2005-2006 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *      \file       htdocs/salesorder/info.php
 *      \ingroup    salesorder
 *		\brief      Page des informations d'une salesorder
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/salesorder/class/salesorder.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';

if (!$user->rights->salesorder->lire)	accessforbidden();

$langs->load("orders");
$langs->load("sendings");

// Security check
$socid=0;
$comid = isset($_GET["id"])?$_GET["id"]:'';
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user,'salesorder',$comid,'');



/*
 * View
 */

llxHeader('',$langs->trans('Order'),'EN:Customers_Orders|FR:Salesorders_Clients|ES:Pedidos de clientes');

$salesorder = new Salesorder($db);
$salesorder->fetch($_GET["id"]);
$salesorder->info($_GET["id"]);
$soc = new Societe($db);
$soc->fetch($salesorder->socid);

$head = salesorder_prepare_head($salesorder);
dol_fiche_head($head, 'info', $langs->trans("CustomerOrder"), 0, 'order');


print '<table width="100%"><tr><td>';
dol_print_object_info($salesorder);
print '</td></tr></table>';

print '</div>';


$db->close();

llxFooter();
?>
