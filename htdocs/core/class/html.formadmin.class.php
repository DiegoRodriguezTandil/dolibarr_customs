<?php
/* Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2007      Patrick Raguin 		<patrick.raguin@gmail.com>
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
 *      \file       htdocs/core/class/html.formadmin.class.php
 *      \ingroup    core
 *      \brief      File of class for html functions for admin pages
 */


/**
 *      Class to generate html code for admin pages
 */
class FormAdmin
{
	var $db;
	var $error;


	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}

	/**
	 *    	Return html select list with available languages (key='en_US', value='United States' for example)
	 *
	 *    	@param      string		$selected       Langue pre-selectionnee
	 *    	@param      string		$htmlname       Nom de la zone select
	 *    	@param      int			$showauto       Affiche choix auto
	 * 		@param		array		$filter			Array of keys to exclude in list
	 * 		@param		int			$showempty		Add empty value
	 *      @param      int			$showwarning    Show a warning if language is not complete
	 *      @return		string						Return HTML select string with list of languages
	 */
	function select_language($selected='',$htmlname='lang_id',$showauto=0,$filter=0,$showempty=0,$showwarning=0)
	{
		global $langs;

		$langs_available=$langs->get_available_languages(DOL_DOCUMENT_ROOT,12);

		$out='';

		$out.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'">';
		if ($showempty)
		{
			$out.= '<option value=""';
			if ($selected == '') $out.= ' selected="selected"';
			$out.= '>&nbsp;</option>';
		}
		if ($showauto)
		{
			$out.= '<option value="auto"';
			if ($selected == 'auto') $out.= ' selected="selected"';
			$out.= '>'.$langs->trans("AutoDetectLang").'</option>';
		}

		asort($langs_available);

		$uncompletelanguages=array('da_DA','fi_FI','hu_HU','is_IS','pl_PL','ro_RO','ru_RU','sv_SV','tr_TR','zh_CN');
		foreach ($langs_available as $key => $value)
		{
		    if ($showwarning && in_array($key,$uncompletelanguages))
		    {
		        //$value.=' - '.$langs->trans("TranslationUncomplete",$key);
		    }
			if ($filter && is_array($filter))
			{
				if ( ! array_key_exists($key, $filter))
				{
					$out.= '<option value="'.$key.'">'.$value.'</option>';
				}
			}
			else if ($selected == $key)
			{
				$out.= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
			}
			else
			{
				$out.= '<option value="'.$key.'">'.$value.'</option>';
			}
		}
		$out.= '</select>';

		return $out;
	}

	/**
     *    Return list of available menus (eldy_backoffice, ...)
     *
     *    @param	string		$selected        Preselected menu value
     *    @param    string		$htmlname        Name of html select
     *    @param    array		$dirmenuarray    Array of directories to scan
     *    @param    string		$moreattrib      More attributes on html select tag
     *    @return	void
     */
    function select_menu($selected, $htmlname, $dirmenuarray, $moreattrib='')
    {
        global $langs,$conf;

        // Clean parameters
        if ($selected == 'eldy.php') $selected='eldy_backoffice.php';  // For compatibility

        // Check parameters
        if (! is_array($dirmenuarray)) return -1;

		$menuarray=array();
        foreach ($conf->file->dol_document_root as $dirroot)
        {
            foreach($dirmenuarray as $dirtoscan)
            {
                $dir=$dirroot.$dirtoscan;
                //print $dir.'<br>';
                if (is_dir($dir))
                {
    	            $handle=opendir($dir);
    	            if (is_resource($handle))
    	            {
    	                while (($file = readdir($handle))!==false)
    	                {
    	                    if (is_file($dir."/".$file) && substr($file, 0, 1) <> '.' && substr($file, 0, 3) <> 'CVS' && substr($file, 0, 5) != 'index')
    	                    {
    	                        if (preg_match('/lib\.php$/i',$file)) continue;	// We exclude library files
    	                    	$filelib=preg_replace('/\.php$/i','',$file);
    	        				$prefix='';
    	        				// 0=Recommanded, 1=Experimental, 2=Developpement, 3=Other
    	        				if (preg_match('/^eldy/i',$file)) $prefix='0';
                                else if (preg_match('/^smartphone/i',$file)) $prefix='2';
    	        				else $prefix='3';

    	                        if ($file == $selected)
    	                        {
    	        					$menuarray[$prefix.'_'.$file]='<option value="'.$file.'" selected="selected">'.$filelib.'</option>';
    	                        }
    	                        else
    	                        {
    	                            $menuarray[$prefix.'_'.$file]='<option value="'.$file.'">'.$filelib.'</option>';
    	                        }
    	                    }
    	                }
    	                closedir($handle);
    	            }
                }
            }
        }
		ksort($menuarray);

		// Output combo list of menus
        print '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.($moreattrib?' '.$moreattrib:'').'>';
        $oldprefix='';
		foreach ($menuarray as $key => $val)
		{
			$tab=explode('_',$key);
			$newprefix=$tab[0];
			if ($newprefix=='1' && ($conf->global->MAIN_FEATURES_LEVEL < 1)) continue;
			if ($newprefix=='2' && ($conf->global->MAIN_FEATURES_LEVEL < 2)) continue;
			if (! empty($conf->browser->firefox) && $newprefix != $oldprefix)	// Add separators
			{
				// Affiche titre
				print '<option value="-1" disabled="disabled">';
				if ($newprefix=='0') print '-- '.$langs->trans("VersionRecommanded").' --';
                if ($newprefix=='1') print '-- '.$langs->trans("VersionExperimental").' --';
				if ($newprefix=='2') print '-- '.$langs->trans("VersionDevelopment").' --';
				if ($newprefix=='3') print '-- '.$langs->trans("Other").' --';
				print '</option>';
				$oldprefix=$newprefix;
			}
			print $val."\n";	// Show menu entry
		}
		print '</select>';
    }

    /**
     *  Return combo list of available menu families
     *
     *  @param	string	$selected        Menu pre-selected
     *  @param  string	$htmlname        Name of html select
     *  @param	string	$dirmenuarray    Directories to scan
     *  @return	void
     */
    function select_menu_families($selected, $htmlname, $dirmenuarray)
    {
		global $langs,$conf;

        //$expdevmenu=array('smartphone_backoffice.php','smartphone_frontoffice.php');  // Menu to disable if $conf->global->MAIN_FEATURES_LEVEL is not set
		$expdevmenu=array();

		$menuarray=array();

		foreach($dirmenuarray as $dirmenu)
		{
            foreach ($conf->file->dol_document_root as $dirroot)
            {
                $dir=$dirroot.$dirmenu;
                if (is_dir($dir))
                {
	                $handle=opendir($dir);
	                if (is_resource($handle))
	                {
	        			while (($file = readdir($handle))!==false)
	        			{
	        				if (is_file($dir."/".$file) && substr($file, 0, 1) <> '.' && substr($file, 0, 3) <> 'CVS')
	        				{
	        					$filelib=preg_replace('/(_backoffice|_frontoffice)?\.php$/i','',$file);
	        					if (preg_match('/^index/i',$filelib)) continue;
	        					if (preg_match('/^default/i',$filelib)) continue;
	        					if (preg_match('/^empty/i',$filelib)) continue;
	        					if (preg_match('/\.lib/i',$filelib)) continue;
	        					if (empty($conf->global->MAIN_FEATURES_LEVEL) && in_array($file,$expdevmenu)) continue;

	        					$menuarray[$filelib]=1;
	        				}
	        				$menuarray['all']=1;
	        			}
	        			closedir($handle);
	                }
                }
            }
		}

		ksort($menuarray);

		// Affichage liste deroulante des menus
        print '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'">';
        $oldprefix='';
		foreach ($menuarray as $key => $val)
		{
			$tab=explode('_',$key);
			$newprefix=$tab[0];
			print '<option value="'.$key.'"';
            if ($key == $selected)
			{
				print '	selected="selected"';
			}
			print '>';
			if ($key == 'all') print $langs->trans("AllMenus");
			else print $key;
			print '</option>'."\n";
		}
		print '</select>';
    }


    /**
     *  Return a HTML select list of timezones
     *
     *  @param	string		$selected        Menu pre-selectionnee
     *  @param  string		$htmlname        Nom de la zone select
     *  @return	void
     */
    function select_timezone($selected,$htmlname)
    {
		global $langs,$conf;

        print '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'">';
		print '<option value="-1">&nbsp;</option>';

		$arraytz=array(
			"Pacific/Midway"=>"GMT-11:00",
			"Pacific/Fakaofo"=>"GMT-10:00",
			"America/Anchorage"=>"GMT-09:00",
			"America/Los_Angeles"=>"GMT-08:00",
			"America/Dawson_Creek"=>"GMT-07:00",
			"America/Chicago"=>"GMT-06:00",
			"America/Bogota"=>"GMT-05:00",
			"America/Anguilla"=>"GMT-04:00",
			"America/Araguaina"=>"GMT-03:00",
			"America/Noronha"=>"GMT-02:00",
			"Atlantic/Azores"=>"GMT-01:00",
			"Africa/Abidjan"=>"GMT+00:00",
			"Europe/Paris"=>"GMT+01:00",
			"Europe/Helsinki"=>"GMT+02:00",
			"Europe/Moscow"=>"GMT+03:00",
			"Asia/Dubai"=>"GMT+04:00",
			"Asia/Karachi"=>"GMT+05:00",
			"Indian/Chagos"=>"GMT+06:00",
			"Asia/Jakarta"=>"GMT+07:00",
			"Asia/Hong_Kong"=>"GMT+08:00",
			"Asia/Tokyo"=>"GMT+09:00",
			"Australia/Sydney"=>"GMT+10:00",
			"Pacific/Noumea"=>"GMT+11:00",
			"Pacific/Auckland"=>"GMT+12:00",
			"Pacific/Enderbury"=>"GMT+13:00"
		);
		foreach ($arraytz as $lib => $gmt)
		{
			print '<option value="'.$lib.'"';
			if ($selected == $lib || $selected == $gmt) print ' selected="selected"';
			print '>'.$gmt.'</option>'."\n";
		}
		print '</select>';
	}



	/**
	 *    	Return html select list with available languages (key='en_US', value='United States' for example)
	 *
	 *    	@param      string	$selected       Paper format pre-selected
	 *    	@param      string	$htmlname       Name of HTML select field
	 * 		@param		string	$filter			Key to filter
	 * 		@param		int		$showempty		Add empty value
	 * 		@return		string					Return HTML output
	 */
	function select_paper_format($selected='',$htmlname='paperformat_id',$filter=0,$showempty=0)
	{
		global $langs;

		$sql="SELECT code, label, width, height, unit FROM ".MAIN_DB_PREFIX."c_paper_format where active=1";
        if ($filter) $sql.=" WHERE code LIKE '%".$filter."%'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num=$this->db->num_rows($resql);
            $i=0;
            while ($i < $num)
            {
                $obj=$this->db->fetch_object($resql);
                $unitKey = $langs->trans('SizeUnit'.$obj->unit);

                $paperformat[$obj->code]= $langs->trans('PaperFormat'.strtoupper($obj->code)).' - '.round($obj->width).'x'.round($obj->height).' '.($unitKey == 'SizeUnit'.$obj->unit ? $obj->unit : $unitKey);

                $i++;
            }
        }
        else dol_print_error($this->db);
		$out='';

		$out.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'">';
		if ($showempty)
		{
			$out.= '<option value=""';
			if ($selected == '') $out.= ' selected="selected"';
			$out.= '>&nbsp;</option>';
		}
		foreach ($paperformat as $key => $value)
		{
            if ($selected == $key)
			{
				$out.= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
			}
			else
			{
				$out.= '<option value="'.$key.'">'.$value.'</option>';
			}
		}
		$out.= '</select>';

		return $out;
	}
}
?>
