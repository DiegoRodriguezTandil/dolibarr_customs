<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (c) 2005      Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2012      Marcos García        <marcosgdf@gmail.com>
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
 *       \file       htdocs/salesorder/class/salesorderstats.class.php
 *       \ingroup    salesorders
 *       \brief      Fichier de la classe de gestion des stats des salesorders
 */
include_once DOL_DOCUMENT_ROOT . '/core/class/stats.class.php';
include_once DOL_DOCUMENT_ROOT . '/salesorder/class/salesorder.class.php';
include_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.salesorder.class.php';


/**
 *    Class to manage order statistics
 */
class salesorderStats extends Stats
{
	public $table_element;

	var $socid;
    var $userid;
	var $office;
	
    var $from;
	var $field;
    var $where;


	/**
	 * Constructor
	 *
	 * @param 	DoliDB	$db		   Database handler
	 * @param 	int		$socid	   Id third party for filter
	 * @param 	string	$mode	   Option
	 * @param   int		$userid    Id user for filter
	 */
	function __construct($db, $socid, $mode, $office, $userid=0)
	{
		global $user, $conf;

		$this->db = $db;

		$this->socid = $socid;
        $this->userid = $userid;
		$this->office = $office;

		if ($mode == 'customer')
		{
			$object=new salesorder($this->db);
			$this->from = MAIN_DB_PREFIX.$object->table_element." as c";
			$this->from.= ", ".MAIN_DB_PREFIX."societe as s";
			$this->field='total_ht';
			$this->where.= " c.fk_statut > 0";    // Not draft and not cancelled
		}
		if ($mode == 'supplier')
		{
			$object=new salesorderFournisseur($this->db);
			$this->from = MAIN_DB_PREFIX.$object->table_element." as c";
			$this->from.= ", ".MAIN_DB_PREFIX."societe as s";
			$this->field='total_ht';
			$this->where.= " c.fk_statut > 2";    // Only approved & ordered
		}
		$this->where.= " AND c.fk_soc = s.rowid AND c.entity = ".$conf->entity;

		if (!$user->rights->societe->client->voir && !$this->socid) $this->where .= " AND c.fk_soc = sc.fk_soc AND sc.fk_user = " .$user->id;
		if($this->socid)
		{
			$this->where .= " AND c.fk_soc = ".$this->socid;
		}
		if($this->office)
		{
			$this->where .= " AND c.office = ".$this->office;
		}
        if ($this->userid > 0) $this->where.=' AND c.fk_user_author = '.$this->userid;
	}

	/**
	 * Return orders number by month for a year
	 *
	 * @param	int		$year	year for stats
	 * @return	array			array with number by month
	 */
	function getNbByMonth($year)
	{
		global $conf;
		global $user;

		$sql = "SELECT date_format(c.date_salesorder,'%m') as dm, count(*) nb";
		$sql.= " FROM ".$this->from;
		if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql.= " WHERE date_format(c.date_salesorder,'%Y') = '".$year."'";
		$sql.= " AND ".$this->where;
		$sql.= " GROUP BY dm";
        $sql.= $this->db->order('dm','DESC');

		return $this->_getNbByMonth($year, $sql);
	}

	/**
	 * Return orders number by year
	 *
	 * @return	array	array with number by year
	 *
	 */
	function getNbByYear()
	{
		global $conf;
		global $user;

		$sql = "SELECT date_format(c.date_salesorder,'%Y') as dm, count(*), sum(c.".$this->field."*(select rate from llx_currency_conversion where source=c.fk_currency and target='USD' and date<=c.date_salesorder order by date desc limit 1))";
		$sql.= " FROM ".$this->from;
		if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql.= " WHERE ".$this->where;
		$sql.= " GROUP BY dm";
        $sql.= $this->db->order('dm','DESC');

		return $this->_getNbByYear($sql);
	}

	/**
	 * Return the orders amount by month for a year
	 *
	 * @param	int		$year	year for stats
	 * @return	array			array with number by month
	 */
	function getAmountByMonth($year)
	{
		global $conf;
		global $user;

		$sql = "SELECT date_format(c.date_salesorder,'%m') as dm, sum(c.".$this->field."*(select rate from llx_currency_conversion where source=c.fk_currency and target='USD' and date<=c.date_salesorder order by date desc limit 1))";
		$sql.= " FROM ".$this->from;
		if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql.= " WHERE date_format(c.date_salesorder,'%Y') = '".$year."'";
		$sql.= " AND ".$this->where;
		$sql.= " GROUP BY dm";
        $sql.= $this->db->order('dm','DESC');

		return $this->_getAmountByMonth($year, $sql);
	}

	/**
	 * Return the orders amount average by month for a year
	 *
	 * @param	int		$year	year for stats
	 * @return	array			array with number by month
	 */
	function getAverageByMonth($year)
	{
		global $conf;
		global $user;

		$sql = "SELECT date_format(c.date_salesorder,'%m') as dm, avg(c.".$this->field."*(select rate from llx_currency_conversion where source=c.fk_currency and target='USD' and date<=c.date_salesorder order by date desc limit 1))";
		$sql.= " FROM ".$this->from;
		if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql.= " WHERE date_format(c.date_salesorder,'%Y') = '".$year."'";
		$sql.= " AND ".$this->where;
		$sql.= " GROUP BY dm";
        $sql.= $this->db->order('dm','DESC');

		return $this->_getAverageByMonth($year, $sql);
	}

	/**
	 *	Return nb, total and average
	 *
	 *	@return	array	Array of values
	 */
	function getAllByYear()
	{
		global $user;

		$sql = "SELECT date_format(c.date_salesorder,'%Y') as year, count(*) as nb, sum(c.".$this->field."*(select rate from llx_currency_conversion where source=c.fk_currency and target='USD' and date<=c.date_salesorder order by date desc limit 1)) as total, avg(".$this->field."*(select rate from llx_currency_conversion where source=c.fk_currency and target='USD' and date<=c.date_salesorder order by date desc limit 1)) as avg";
		$sql.= " FROM ".$this->from;
		if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql.= " WHERE ".$this->where;
		$sql.= " GROUP BY year";
        $sql.= $this->db->order('year','DESC');

		return $this->_getAllByYear($sql);
	}
}

?>
