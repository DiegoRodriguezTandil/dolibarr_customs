<?php
/* Copyright (C) 2005-2011	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2012		Charles-Fr BENKE	<charles.fr@benke.fr>
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
 *	\file       htdocs/exports/class/export.class.php
 *	\ingroup    export
 *	\brief      File of class to manage exports
 */


/**
 *	Class to manage exports
 */
class Export
{
	var $db;

	var $array_export_code=array();             // Tableau de "idmodule_numlot"
	var $array_export_module=array();           // Tableau de "nom de modules"
	var $array_export_label=array();            // Tableau de "libelle de lots"
	var $array_export_sql_start=array();        // Tableau des "requetes sql"
	var $array_export_sql_end=array();          // Tableau des "requetes sql"
	var $array_export_sql_order=array();        // Tableau des "requetes sql"
	var $array_export_fields=array();           // Tableau des listes de champ+libelle a exporter
	var $array_export_TypeFields=array();		// Tableau des listes de champ+Type de filtre
	var $array_export_FilterValue=array();		// Tableau des listes de champ+Valeur a filtrer
	var $array_export_entities=array();         // Tableau des listes de champ+alias a exporter
	var $array_export_dependencies=array();     // array of list of entities that must take care of the DISTINCT if a field is added into export
	var $array_export_special=array();          // Tableau des operations speciales sur champ

	// To store export modules
	var $hexa;
	var $hexafilter;
	var $hexafiltervalue;
	var $datatoexport;
	var $model_name;

	var $sqlusedforexport;


	/**
	 *    Constructor
	 *
	 *    @param  	DoliDB		$db		Database handler
	 */
	function __construct($db)
	{
		$this->db=$db;
	}


	/**
	 *    Load an exportable dataset
	 *
	 *    @param  	User		$user      	Object user making export
	 *    @param  	string		$filter    	Load a particular dataset only
	 *    @return	int						<0 if KO, >0 if OK
	 */
	function load_arrays($user,$filter='')
	{
		global $langs,$conf,$mysoc;

		dol_syslog(get_class($this)."::load_arrays user=".$user->id." filter=".$filter);

        $var=true;
        $i=0;

        // Define list of modules directories into modulesdir
        require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

        $modulesdir = dolGetModulesDirs();

		foreach($modulesdir as $dir)
		{
			// Search available exports
			$handle=@opendir(dol_osencode($dir));
			if (is_resource($handle))
			{
                // Search module files
			    while (($file = readdir($handle))!==false)
				{
					if (is_readable($dir.$file) && preg_match("/^(mod.*)\.class\.php$/i",$file,$reg))
					{
						$modulename=$reg[1];

						// Defined if module is enabled
						$enabled=true;
						$part=strtolower(preg_replace('/^mod/i','',$modulename));
						if ($part == 'propale') $part='propal';
						if (empty($conf->$part->enabled)) $enabled=false;

						if ($enabled)
						{
							// Chargement de la classe
							$file = $dir.$modulename.".class.php";
							$classname = $modulename;
							require_once $file;
							$module = new $classname($this->db);

							if (isset($module->export_code) && is_array($module->export_code))
							{
							    foreach($module->export_code as $r => $value)
								{
                                    //print $i.'-'.$filter.'-'.$modulename.'-'.join(',',$module->export_code).'<br>';
								    if ($filter && ($filter != $module->export_code[$r])) continue;

                                    // Test if condition to show are ok
                                    if (! empty($module->export_enabled[$r]) && ! verifCond($module->export_enabled[$r])) continue;

                                    // Test if permissions are ok
									$bool=true;
									foreach($module->export_permission[$r] as $val)
									{
    									$perm=$val;
    									//print_r("$perm[0]-$perm[1]-$perm[2]<br>");
    									if (! empty($perm[2]))
    									{
    										$bool=$user->rights->$perm[0]->$perm[1]->$perm[2];
    									}
    									else
    									{
    										$bool=$user->rights->$perm[0]->$perm[1];
    									}
    									if ($perm[0]=='user' && $user->admin) $bool=true;
    									if (! $bool) break;
									}
									//print $bool." $perm[0]"."<br>";

									// Permissions ok
									//	          if ($bool)
									//	          {
									// Charge fichier lang en rapport
									$langtoload=$module->getLangFilesArray();
									if (is_array($langtoload))
									{
										foreach($langtoload as $key)
										{
											$langs->load($key);
										}
									}

									// Module
									$this->array_export_module[$i]=$module;
									// Permission
									$this->array_export_perms[$i]=$bool;
									// Icon
									$this->array_export_icon[$i]=(isset($module->export_icon[$r])?$module->export_icon[$r]:$module->picto);
									// Code du dataset export
									$this->array_export_code[$i]=$module->export_code[$r];
									// Libelle du dataset export
									$this->array_export_label[$i]=$module->getExportDatasetLabel($r);
									// Tableau des champ a exporter (cle=champ, valeur=libelle)
									$this->array_export_fields[$i]=$module->export_fields_array[$r];
									// Tableau des champs a filtrer (cle=champ, valeur1=type de donnees) on verifie que le module a des filtres
									$this->array_export_TypeFields[$i]=(isset($module->export_TypeFields_array[$r])?$module->export_TypeFields_array[$r]:'');
									// Tableau des entites a exporter (cle=champ, valeur=entite)
									$this->array_export_entities[$i]=$module->export_entities_array[$r];
									// Tableau des entites qui requiert abandon du DISTINCT (cle=entite, valeur=champ id child records)
									$this->array_export_dependencies[$i]=(! empty($module->export_dependencies_array[$r])?$module->export_dependencies_array[$r]:'');
									// Tableau des operations speciales sur champ
									$this->array_export_special[$i]=(! empty($module->export_special_array[$r])?$module->export_special_array[$r]:'');

									// Requete sql du dataset
									$this->array_export_sql_start[$i]=$module->export_sql_start[$r];
									$this->array_export_sql_end[$i]=$module->export_sql_end[$r];
									$this->array_export_sql_order[$i]=$module->export_sql_order[$r];
									//$this->array_export_sql[$i]=$module->export_sql[$r];

									dol_syslog(get_class($this)."::load_arrays loaded for module ".$modulename." with index ".$i.", dataset=".$module->export_code[$r].", nb of fields=".(! empty($module->export_fields_code[$r])?count($module->export_fields_code[$r]):''));
									$i++;
									//	          }
								}
							}
						}
					}
				}
                closedir($handle);
			}
		}

		return 1;
	}


	/**
	 *      Build the sql export request.
	 *      Arrays this->array_export_xxx are already loaded for required datatoexport
	 *
	 *      @param      int		$indice				Indice of export
	 *      @param      array	$array_selected     Filter on array of fields to export
	 *      @param      array	$array_filterValue  Filter on array of fields to export
	 *      @return		string						SQL String. Example "select s.rowid as r_rowid, s.status as s_status from ..."
	 */
	function build_sql($indice, $array_selected, $array_filterValue)
	{
		// Build the sql request
		$sql=$this->array_export_sql_start[$indice];
		$i=0;

		//print_r($array_selected);
		foreach ($this->array_export_fields[$indice] as $key => $value)
		{
			if (! array_key_exists($key, $array_selected)) continue;		// Field not selected

			if ($i > 0) $sql.=', ';
			else $i++;
			$newfield=$key.' as '.str_replace(array('.', '-'),'_',$key);;

			$sql.=$newfield;
		}
		$sql.=$this->array_export_sql_end[$indice];

		//construction du filtrage si le parametrage existe
		if (is_array($array_filterValue) && !empty($array_filterValue))
		{
			$sqlWhere='';
			// pour ne pas a gerer le nombre de condition
			foreach ($array_filterValue as $key => $value)
			{
				if ($value != '') $sqlWhere.=" and ".$this->build_filterQuery($this->array_export_TypeFields[0][$key], $key, $array_filterValue[$key]);
			}
			$sql.=$sqlWhere;
		}
		$sql.=$this->array_export_sql_order[$indice];

		return $sql;
	}

	/**
	 *      Build the conditionnal string from filter the query
	 *
	 *      @param		string	$TypeField		Type of Field to filter
	 *      @param		string	$NameField		Name of the field to filter
	 *      @param		string	$ValueField		Value of the field for filter. Must not be ''
	 *      @return		string					sql string of then field ex : "field='xxx'>"
	 */
	function build_filterQuery($TypeField, $NameField, $ValueField)
	{
		//print $TypeField." ".$NameField." ".$ValueField;
		$InfoFieldList = explode(":", $TypeField);
		// build the input field on depend of the type of file
		switch ($InfoFieldList[0]) {
			case 'Text':
				if (! (strpos($ValueField, '%') === false))
					$szFilterQuery.=" ".$NameField." LIKE '".$ValueField."'";
				else
					$szFilterQuery.=" ".$NameField."='".$ValueField."'";
				break;
			case 'Date':
				if (strpos($ValueField, "+") > 0)
				{
					// mode plage
					$ValueArray = explode("+", $ValueField);
					$szFilterQuery ="(".$this->conditionDate($NameField,$ValueArray[0],">=");
					$szFilterQuery.=" AND ".$this->conditionDate($NameField,$ValueArray[1],"<=").")";
				}
				else
				{
					if (is_numeric(substr($ValueField,0,1)))
						$szFilterQuery=$this->conditionDate($NameField,$ValueField,"=");
					else
						$szFilterQuery=$this->conditionDate($NameField,substr($ValueField,1),substr($ValueField,0,1));
				}
				break;
			case 'Duree':
				break;
			case 'Numeric':
				// si le signe -
				if (strpos($ValueField, "+") > 0)
				{
					// mode plage
					$ValueArray = explode("+", $ValueField);
					$szFilterQuery ="(".$NameField.">=".$ValueArray[0];
					$szFilterQuery.=" AND ".$NameField."<=".$ValueArray[1].")";
				}
				else
				{
					if (is_numeric(substr($ValueField,0,1)))
						$szFilterQuery=" ".$NameField."=".$ValueField;
					else
						$szFilterQuery=" ".$NameField.substr($ValueField,0,1).substr($ValueField,1);
				}
				break;
			case 'Boolean':
				$szFilterQuery=" ".$NameField."=".(is_numeric($ValueField) ? $ValueField : ($ValueField =='yes' ? 1: 0) );
				break;
			case 'Status':
			case 'List':
				if (is_numeric($ValueField))
					$szFilterQuery=" ".$NameField."=".$ValueField;
				else
					$szFilterQuery=" ".$NameField."='".$ValueField."'";
				break;
		}

		return $szFilterQuery;
	}

	/**
	 *	conditionDate
	 *
	 *  @param 	string	$Field		Field operand 1
	 *  @param 	string	$Value		Value operand 2
	 *  @param 	string	$Sens		Comparison operator
	 *  @return string
	 */
	function conditionDate($Field, $Value, $Sens)
	{
		// FIXME date_format is forbidden, not performant and no portable. Use instead BETWEEN
		if (strlen($Value)==4) $Condition=" date_format(".$Field.",'%Y') ".$Sens." ".$Value;
		elseif (strlen($Value)==6) $Condition=" date_format(".$Field.",'%Y%m') ".$Sens." '".$Value."'";
		else  $Condition=" date_format(".$Field.",'%Y%m%d') ".$Sens." ".$Value;
		return $Condition;
	}

	/**
	 *      Build an input field used to filter the query
	 *
	 *      @param		string	$TypeField		Type of Field to filter
	 *      @param		string	$NameField		Name of the field to filter
	 *      @param		string	$ValueField		Initial value of the field to filter
	 *      @return		string					html string of the input field ex : "<input type=text name=... value=...>"
	 */
	function build_filterField($TypeField, $NameField, $ValueField)
	{
		$szFilterField='';
		$InfoFieldList = explode(":", $TypeField);

		// build the input field on depend of the type of file
		switch ($InfoFieldList[0])
		{
			case 'Text':
			case 'Date':
			case 'Duree':
			case 'Numeric':
				$szFilterField='<input type="text" name='.$NameField." value='".$ValueField."'>";
				break;
			case 'Boolean':
				$szFilterField='<select name="'.$NameField.'" class="flat">';
				$szFilterField.='<option ';
				if ($ValueField=='') $szFilterField.=' selected ';
				$szFilterField.=' value="">&nbsp;</option>';

				$szFilterField.='<option ';
				if ($ValueField=='yes') $szFilterField.=' selected ';
				$szFilterField.=' value="yes">'.yn(1).'</option>';

				$szFilterField.='<option ';
				if ($ValueField=='no') $szFilterField.=' selected ';
				$szFilterField.=' value="no">'.yn(0).'</option>';
				$szFilterField.="</select>";
				break;
			case 'List':
				// 0 : Type du champ
				// 1 : Nom de la table
				// 2 : Nom du champ contenant le libelle
				// 3 : Nom du champ contenant la cle (si different de rowid)
				if (count($InfoFieldList)==4)
					$keyList=$InfoFieldList[3];
				else
					$keyList='rowid';
				$sql = 'SELECT '.$keyList.' as rowid, '.$InfoFieldList[2];
				$sql.= ' FROM '.MAIN_DB_PREFIX .$InfoFieldList[1];

				$resql = $this->db->query($sql);
				if ($resql)
				{
					$szFilterField='<select class="flat" name="'.$NameField.'">';
					$szFilterField.='<option value="0">&nbsp;</option>';
					$num = $this->db->num_rows($resql);

					$i = 0;
					if ($num)
					{
						while ($i < $num)
						{
							$obj = $this->db->fetch_object($resql);
							if ($obj->$InfoFieldList[2] == '-')
							{
								// Discard entry '-'
								$i++;
								continue;
							}

							$labeltoshow=dol_trunc($obj->$InfoFieldList[2],18);
							if (!empty($ValueField) && $ValueField == $obj->rowid)
							{
								$szFilterField.='<option value="'.$obj->rowid.'" selected="selected">'.$labeltoshow.'</option>';
							}
							else
							{
								$szFilterField.='<option value="'.$obj->rowid.'" >'.$labeltoshow.'</option>';
							}
							$i++;
						}
					}
					$szFilterField.="</select>";

					$this->db->free();
				}
				break;
		}

		return $szFilterField;
	}

	/**
	 *      Build an input field used to filter the query
	 *
	 *      @param		string	$TypeField		Type of Field to filter
	 *      @return		string					html string of the input field ex : "<input type=text name=... value=...>"
	 *      TODO replace by translation
	 */
	function genDocFilter($TypeField)
	{
		$szMsg='';
		$InfoFieldList = explode(":", $TypeField);
		// build the input field on depend of the type of file
		switch ($InfoFieldList[0]) {
			case 'Text':
				$szMsg="% permet de remplacer un ou plusieurs caract&egrave;res dans la chaine";
				break;
			case 'Date':
				$szMsg ="'AAAA' 'AAAAMM' 'AAAAMMJJ' : filtre sur une ann&eacute;e/mois/jour <br>";
				$szMsg.="'AAAA+AAAA' 'AAAAMM+AAAAMM' 'AAAAMMJJ+AAAAMMJJ': filtre sur une plage d'ann&eacute;e/mois/jour <br>";
				$szMsg.="'&gt;AAAA' '&gt;AAAAMM' '&gt;AAAAMMJJ' filtre sur les ann&eacute;e/mois/jour suivants <br>";
				$szMsg.="'&lsaquo;AAAA' '&lsaquo;AAAAMM' '&lsaquo;AAAAMMJJ' filtre sur les ann&eacute;e/mois/jour pr&eacute;c&eacute;dent <br>";
				break;
			case 'Duree':
				break;
			case 'Numeric':
				$szMsg ="'NNNNN' filtre sur une valeur <br>";
				$szMsg.="'NNNNN+NNNNN' filtre sur une plage de valeur<br>";
				$szMsg.="'&lsaquo;NNNNN' filtre sur les valeurs inf&eacute;rieurs<br>";
				$szMsg.="'&gt;NNNNN' filtre sur les valeurs sup&eacute;rieurs<br>";
				break;
			case 'Boolean':
				break;
			case 'List':
				break;
		}
		return $szMsg;
	}

	/**
	 *      Build export file.
	 *      File is built into directory $conf->export->dir_temp.'/'.$user->id
	 *      Arrays this->array_export_xxx are already loaded for required datatoexport
	 *
	 *      @param      User		$user               User that export
	 *      @param      string		$model              Export format
	 *      @param      string		$datatoexport       Name of dataset to export
	 *      @param      array		$array_selected     Filter on array of fields to export
	 *      @param      array		$array_filterValue  Filter on array of fields with a filter
	 *      @param		string		$sqlquery			If set, transmit a sql query instead of building it from arrays
	 *      @return		int								<0 if KO, >0 if OK
	 */
	function build_file($user, $model, $datatoexport, $array_selected, $array_filterValue, $sqlquery = '')
 	{
		global $conf,$langs;

		$indice=0;
		asort($array_selected);

		dol_syslog("Export::build_file ".$model.", ".$datatoexport.", ".implode(",", $array_selected));

		// Check parameters or context properties
		if (! is_array($this->array_export_fields[$indice]))
		{
			$this->error="ErrorBadParameter";
			return -1;
		}

		// Creation de la classe d'export du model ExportXXX
		$dir = DOL_DOCUMENT_ROOT . "/core/modules/export/";
		$file = "export_".$model.".modules.php";
		$classname = "Export".$model;
		require_once $dir.$file;
		$objmodel = new $classname($this->db);

		if (! empty($sqlquery)) $sql = $sqlquery;
        else $sql=$this->build_sql($indice, $array_selected, $array_filterValue);

		// Run the sql
		$this->sqlusedforexport=$sql;
		dol_syslog("Export::build_file sql=".$sql);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			//$this->array_export_label[$indice]
			$filename="export_".$datatoexport;
			$filename.='.'.$objmodel->getDriverExtension();
			$dirname=$conf->export->dir_temp.'/'.$user->id;

			$outputlangs=dol_clone($langs);	// We clone to have an object we can modify (for example to change output charset by csv handler) without changing original value

			// Open file
			dol_mkdir($dirname);
			$result=$objmodel->open_file($dirname."/".$filename, $outputlangs);

			if ($result >= 0)
			{
				// Genere en-tete
				$objmodel->write_header($outputlangs);

				// Genere ligne de titre
				$objmodel->write_title($this->array_export_fields[$indice],$array_selected,$outputlangs);

				$var=true;

				while ($objp = $this->db->fetch_object($resql))
				{
					$var=!$var;

					// Process special operations
					if (! empty($this->array_export_special[$indice]))
					{
						foreach ($this->array_export_special[$indice] as $key => $value)
						{
							if (! array_key_exists($key, $array_selected)) continue;		// Field not selected
							// Operation NULLIFNEG
							if ($this->array_export_special[$indice][$key]=='NULLIFNEG')
							{
								//$alias=$this->array_export_alias[$indice][$key];
								$alias=str_replace(array('.', '-'),'_',$key);
								if ($objp->$alias < 0) $objp->$alias='';
							}
							// Operation ZEROIFNEG
							if ($this->array_export_special[$indice][$key]=='ZEROIFNEG')
							{
								//$alias=$this->array_export_alias[$indice][$key];
								$alias=str_replace(array('.', '-'),'_',$key);
								if ($objp->$alias < 0) $objp->$alias='0';
							}
						}
					}
					// end of special operation processing

					$objmodel->write_record($array_selected,$objp,$outputlangs);
				}

				// Genere en-tete
				$objmodel->write_footer($outputlangs);

				// Close file
				$objmodel->close_file();

        		return 1;
			}
			else
			{
				$this->error=$objmodel->error;
				dol_syslog("Export::build_file Error: ".$this->error, LOG_ERR);
				return -1;
			}
		}
		else
		{
			$this->error=$this->db->error()." - sql=".$sql;
			dol_syslog("Export::build_file Error: ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Save an export model in database
	 *
	 *  @param		User	$user 	Object user that save
	 *  @return		int				<0 if KO, >0 if OK
	 */
	function create($user)
	{
		global $conf;

		dol_syslog("Export.class.php::create");

		$this->db->begin();

		$filter='';
		if (! empty($this->hexafilter) && ! empty($this->hexafiltervalue)) {
			$filter = json_encode(array('field' => $this->hexafilter, 'value' => $this->hexafiltervalue));
		}

		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'export_model (';
		$sql.= 'label,';
		$sql.= 'type,';
		$sql.= 'field,';
		$sql.= 'filter';
		$sql.= ') VALUES (';
		$sql.= "'".$this->db->escape($this->model_name)."',";
		$sql.= "'".$this->datatoexport."',";
		$sql.= "'".$this->hexa."',";
		$sql.= (! empty($filter)?"'".$filter."'":"null");
		$sql.= ")";

		dol_syslog("Export::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->error=$this->db->lasterror();
			$this->errno=$this->db->lasterrno();
			dol_syslog("Export::create error ".$this->error, LOG_ERR);
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *  Load an export profil from database
	 *
	 *  @param		int		$id		Id of profil to load
	 *  @return		int				<0 if KO, >0 if OK
	 */
	function fetch($id)
	{
		$sql = 'SELECT em.rowid, em.field, em.label, em.type, em.filter';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'export_model as em';
		$sql.= ' WHERE em.rowid = '.$id;

		dol_syslog("Export::fetch sql=".$sql, LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$obj = $this->db->fetch_object($result);
			if ($obj)
			{
				$this->id				= $obj->rowid;
				$this->hexa				= $obj->field;
				$this->model_name		= $obj->label;
				$this->datatoexport		= $obj->type;

				$filter					= json_decode($obj->filter, true);
				$this->hexafilter		= (isset($filter['field'])?$filter['field']:'');
				$this->hexafiltervalue	= (isset($filter['value'])?$filter['value']:'');

				return 1;
			}
			else
			{
				$this->error="ModelNotFound";
				return -2;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -3;
		}
	}


	/**
	 *	Delete object in database
	 *
	 *	@param      User		$user        	User that delete
	 *  @param      int			$notrigger	    0=launch triggers after, 1=disable triggers
	 *	@return		int							<0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."export_model";
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action call a trigger.

				//// Call triggers
				//include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
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
	 *	Output list all export models
	 *  TODO Move this into a class htmlxxx.class.php
	 *
	 *	@return	void
	 */
	function list_export_model()
	{
		global $conf, $langs;

		$sql = "SELECT em.rowid, em.field, em.label, em.type, em.filter";
		$sql.= " FROM ".MAIN_DB_PREFIX."export_model";
		$sql.= " ORDER BY rowid";

		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);
				$keyModel = array_search($obj->type, $this->array_export_code);
				print "<tr>";
				print '<td><a href=export.php?step=2&action=select_model&exportmodelid='.$obj->rowid.'&datatoexport='.$obj->type.'>'.$obj->label.'</a></td>';
				print '<td>';
				print img_object($this->array_export_module[$keyModel]->getName(),$this->array_export_icon[$keyModel]).' ';
				print $this->array_export_module[$keyModel]->getName().' - ';
				// recuperation du nom de l'export

				$string=$langs->trans($this->array_export_label[$keyModel]);
				print ($string!=$this->array_export_label[$keyModel]?$string:$this->array_export_label[$keyModel]);
				print '</td>';
				//print '<td>'.$obj->type.$keyModel.'</td>';
				print '<td>'.str_replace(',',' , ',$obj->field).'</td>';
				if (! empty($obj->filter)) {
					$filter = json_decode($obj->filter, true);
					print '<td>'.str_replace(',',' , ',$filter['field']).'</td>';
					print '<td>'.str_replace(',',' , ',$filter['value']).'</td>';
				}
				// suppression de l'export
				print '<td align="right">';
				print '<a href="'.$_SERVER["PHP_SELF"].'?action=deleteprof&id='.$obj->rowid.'">';
				print img_delete();
				print '</a>';
				print "</tr>";

				$i++;
			}
		}
		else {
			dol_print_error($this->db);
		}
	}

}

?>
