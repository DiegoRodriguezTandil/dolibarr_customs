<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *  \file       dev/skeletons/skeleton_class.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Put here some comments
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Policy // extends CommonObject
{
    public $element='policy';
    public $table_element='policy';
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='skeleton';			//!< Id that identify managed objects
	//var $table_element='skeleton';	//!< Name of table without prefix where object is stored

	var $id;
    var $fk_docid;
    var $fk_doctype;
    var $fk_soc;
	var $assurance;
	var $ref;
	var $endorsement;
	var $expiration_date;
	var $cost;
	var $total_ht;
	var $status;
	var $client;
	var $fk_currency;
	//...


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
		$this->endorsemen=1;
		$this->fk_currency='USD';
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        /*if (isset($this->fk_docid)) $this->fk_docid=trim($this->prop1);
        if (isset($this->fk_doctype)) $this->fk_doctype=trim($this->prop2);*/
		//...

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."policy(";
		$sql.= " fk_docid,";
		$sql.= " fk_doctype,";
		$sql.= " fk_soc,";
		$sql.= " ref,";
		$sql.= " endorsement,";
		$sql.= " expiration_date,";
        $sql.= " price,";
		$sql.= " fk_status";
		$sql.= ") VALUES (";
        $sql.= " ".$this->fk_docid.",";
        $sql.= " '".$this->fk_doctype."',";
		$sql.= " ".$this->fk_soc.",";
		$sql.= " '".$this->ref."',";
		$sql.= " 0,";
		$sql.= " '".$this->db->idate(strtotime('+90 day'))."',";
		$sql.= " ".$this->price;
		$sql.= ",0)";
	
		$this->expiration_date=$this->db->idate(strtotime('+90 day'));
	
		//print var_dump($sql);
		
		$this->db->begin();
		
	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."policy");

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_docid,";
		$sql.= " t.fk_doctype,";
		$sql.= " t.fk_soc,";
		$sql.= " t.ref,";
		$sql.= " t.expiration_date,";
		$sql.= " t.price,";
		$sql.= " t.endorsement";
        $sql.= " FROM ".MAIN_DB_PREFIX."policy as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                $this->fk_docid = $obj->fk_docid;
                $this->fk_doctype = $obj->fk_doctype;
				$this->fk_soc = $obj->fk_soc;
				$this->ref = $obj->ref;
				$this->total_ht=$obj->price;
				$this->cost=$obj->price;
				$this->endorsement = $obj->endorsement;
				$this->expiration_date=$this->db->jdate($obj->expiration_date);
				$this->assurance= new Societe($this->db);
				$this->assurance->fetch($this->fk_soc);
				$this->client=$this->assurance;
            }
            $this->db->free($resql);
			
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }
	function fetch_by_doc($docid,$doctype)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.fk_docid,";
		$sql.= " t.fk_doctype,";
		$sql.= " t.fk_soc,";
		$sql.= " t.ref,";
		$sql.= " t.endorsement,";
		$sql.= " t.expiration_date,";
		$sql.= " t.price,";
		$sql.= " t.fk_status";
        $sql.= " FROM ".MAIN_DB_PREFIX."policy as t";
        $sql.= " WHERE t.fk_docid = ".$docid;
		$sql.= " AND t.fk_doctype = '".$doctype."'";
		$sql.= " ORDER BY t.endorsement DESC";

		
    	dol_syslog(get_class($this)."::fetch_by_doc sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
			if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                $this->fk_docid = $obj->fk_docid;
                $this->fk_doctype = $obj->fk_doctype;
				$this->fk_soc = $obj->fk_soc;
				$this->ref = $obj->ref;
				$this->endorsement = $obj->endorsement;
				$this->expiration_date=$this->db->jdate($obj->expiration_date);
				$this->cost = $obj->price;
				$this->status = $obj->fk_status;
				
				$this->assurance= new Societe($this->db);
				$this->assurance->fetch($this->fk_soc);
				$this->client=$this->assurance;
				return 1;
            }
            $this->db->free($resql);

            return 0;
        }
        else
        {			
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch_by_doc ".$this->error, LOG_ERR);
            return -1;
        }
    }

	function getRate($id,$currency)
	{
		$sql = "SELECT rate FROM llx_currency_conversion a join llx_policy b on a.source='USD' and a.target='".$currency."' where rowid=".$id." order by date desc";
		dol_syslog(get_class($this)."::getRate sql=".$sql, LOG_DEBUG);
		$result = $this->db->query($sql);
		if ( $result )
		{
			$obj = $this->db->fetch_object($result);
			return $obj->rate;
		}
		else
		{
			$this->error=$this->db->error();
			return 0;
		}
	}
	
	function fetch_thirdparty()
    {
        global $conf;

        if (empty($this->fk_soc)) return 0;

        $thirdparty = new Societe($this->db);
        $result=$thirdparty->fetch($this->fk_soc);
        $this->client = $thirdparty;  // deprecated
        $this->thirdparty = $thirdparty;

        return $result;
    }
	function getNomUrl($withpicto=0,$option=0,$max=0,$short=0)
    {
        global $conf, $langs;

        $result='';

		$url = DOL_URL_ROOT.'/salesorder/poliza.php?id='.$this->fk_docid;

        if ($short) return $url;

        $linkstart = '<a href="'.$url.'">';
        $linkend='</a>';

        $picto='order';
        $label=$langs->trans("ShowOrder").': '.$this->ref;

        if ($withpicto) $result.=($linkstart.img_object($label,$picto).$linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';
        $result.=$linkstart.$this->ref.'('.$this->endorsement.')'.$linkend;
        return $result;
    }
	function getLibStatut($mode)
    {
        return 1;
    }
    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        if (isset($this->prop1)) $this->prop1=trim($this->prop1);
        if (isset($this->prop2)) $this->prop2=trim($this->prop2);
		//...

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."mytable SET";
        $sql.= " field1=".(isset($this->field1)?"'".$this->db->escape($this->field1)."'":"null").",";
        $sql.= " field2=".(isset($this->field2)?"'".$this->db->escape($this->field2)."'":"null")."";
		//...
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }
	function update_ref_number($reference, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        if (isset($this->prop1)) $this->prop1=trim($this->prop1);
        $this->ref=trim($reference);
		//...

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."policy SET";
        $sql.= " ref=".(isset($reference)?"'".$this->db->escape($reference)."'":"null");
        $sql.= " WHERE rowid=".$this->id;
		
		//print var_dump($sql);
		
		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }
	function update_assurance_soc($socid, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        if (isset($this->prop1)) $this->prop1=trim($this->prop1);
        $this->fk_soc=$socid;
		//...

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."policy SET";
        $sql.= " fk_soc=".$socid;
        $sql.= " WHERE rowid=".$this->id;
		
		//print var_dump($sql);
		
		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }
	
	function update_expiration_date($datep, $notrigger=0)
    {
    	global $conf, $langs, $user;
		$error=0;

		// Clean parameters
        if (isset($this->prop1)) $this->prop1=trim($this->prop1);
        
		//...

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."policy SET";
        $sql.= " expiration_date='".$this->db->idate($datep)."'";
        $sql.= " WHERE rowid=".$this->id;
			
		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->expiration_date=$datep;
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            $interface=new Interfaces($this->db);
	            $result=$interface->run_triggers('POLICY_MODIFY',$this,$user,$langs,$conf);
	            if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }
	function update_price($price, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        if (isset($this->prop1)) $this->prop1=trim($this->prop1);
        
		//...

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."policy SET";
        $sql.= " price=".price2num($price);
        $sql.= " WHERE rowid=".$this->id;
			
		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->price=$price;
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }
	function update_status($status, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."policy SET";
        $sql.= " fk_status=".$status;
        $sql.= " WHERE rowid=".$this->id;
			
		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->status=$status;
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }
 	/**
	 *  Delete object in database
	 *
     *	@param  User	$user        User that deletes
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action calls a trigger.

		        //// Call triggers
		        //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."mytable";
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(get_class($this)."::delete sql=".$sql);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Skeleton_Class($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{


		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}
	
	function load_board($user,$mode)
    {
        global $conf, $user;

        $now=dol_now();

        $this->nbtodo=$this->nbtodolate=0;
        $clause = " WHERE";

        $sql = "SELECT p.rowid, p.ref, p.fk_soc, p.expiration_date as datec, p.price, p.fk_status";
        $sql.= " FROM ".MAIN_DB_PREFIX."policy as p";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as sc ON p.fk_soc = sc.fk_soc";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            while ($obj=$this->db->fetch_object($resql))
            {
                $this->nbtodo++;
                // TODO Definir regle des propales a facturer en retard
                // if ($mode == 'signed' && ! count($this->FactureListeArray($obj->rowid))) $this->nbtodolate++;
            }
            return 1;
        }
        else
        {
            $this->error=$this->db->error();
            return -1;
        }
    }

	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		$this->prop1='prop1';
		$this->prop2='prop2';
	}

}
?>
