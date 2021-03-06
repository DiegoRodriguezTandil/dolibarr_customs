<?php
/* Copyright (C) 2006-2012	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@capnetworks.com>
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
 * or see http://www.gnu.org/
 */


/**
 *	    \file       htdocs/core/lib/usergroups.lib.php
 *		\brief      Ensemble de fonctions de base pour la gestion des utilisaterus et groupes
 */

/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to shoc
 */
function user_prepare_head($object)
{
	global $langs, $conf, $user;

	$langs->load("users");

	$canreadperms=true;
	if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS))
	{
		$canreadperms=($user->admin || ($user->id != $object->id && $user->rights->user->user_advance->readperms) || ($user->id == $object->id && $user->rights->user->self_advance->readperms));
	}

	$h = 0;
	$head = array();

    $head[$h][0] = DOL_URL_ROOT.'/user/fiche.php?id='.$object->id;
    $head[$h][1] = $langs->trans("UserCard");
    $head[$h][2] = 'user';
    $h++;

	if (! empty($conf->ldap->enabled) && ! empty($conf->global->LDAP_SYNCHRO_ACTIVE))
	{
		$langs->load("ldap");
	    $head[$h][0] = DOL_URL_ROOT.'/user/ldap.php?id='.$object->id;
	    $head[$h][1] = $langs->trans("LDAPCard");
	    $head[$h][2] = 'ldap';
	    $h++;
	}

	if ($canreadperms)
	{
		$head[$h][0] = DOL_URL_ROOT.'/user/perms.php?id='.$object->id;
		$head[$h][1] = $langs->trans("UserRights");
		$head[$h][2] = 'rights';
		$h++;
	}

    $head[$h][0] = DOL_URL_ROOT.'/user/param_ihm.php?id='.$object->id;
    $head[$h][1] = $langs->trans("UserGUISetup");
    $head[$h][2] = 'guisetup';
    $h++;

    if (! empty($conf->clicktodial->enabled))
    {
        $head[$h][0] = DOL_URL_ROOT.'/user/clicktodial.php?id='.$object->id;
        $head[$h][1] = $langs->trans("ClickToDial");
	    $head[$h][2] = 'clicktodial';
        $h++;
    }

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'user');

    if (! empty($user->societe_id))
    {
    	$head[$h][0] = DOL_URL_ROOT.'/user/note.php?id='.$object->id;
    	$head[$h][1] = $langs->trans("Note");
    	$head[$h][2] = 'note';
    	$h++;

    	$head[$h][0] = DOL_URL_ROOT.'/user/info.php?id='.$object->id;
    	$head[$h][1] = $langs->trans("Info");
    	$head[$h][2] = 'info';
    	$h++;
    }

    complete_head_from_modules($conf,$langs,$object,$head,$h,'user','remove');

	return $head;
}


function group_prepare_head($object)
{
	global $langs, $conf, $user;

	$canreadperms=true;
	if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS))
	{
		$canreadperms=($user->admin || $user->rights->user->group_advance->readperms);
	}

	$h = 0;
	$head = array();

    $head[$h][0] = DOL_URL_ROOT.'/user/group/fiche.php?id='.$object->id;
    $head[$h][1] = $langs->trans("GroupCard");
    $head[$h][2] = 'group';
    $h++;

	if (! empty($conf->ldap->enabled) && ! empty($conf->global->LDAP_SYNCHRO_ACTIVE))
	{
		$langs->load("ldap");
	    $head[$h][0] = DOL_URL_ROOT.'/user/group/ldap.php?id='.$object->id;
	    $head[$h][1] = $langs->trans("LDAPCard");
	    $head[$h][2] = 'ldap';
	    $h++;
	}

	if ($canreadperms)
	{
		$head[$h][0] = DOL_URL_ROOT.'/user/group/perms.php?id='.$object->id;
		$head[$h][1] = $langs->trans("GroupRights");
		$head[$h][2] = 'rights';
		$h++;
	}

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname);   												to remove a tab
    complete_head_from_modules($conf,$langs,$object,$head,$h,'group');

    complete_head_from_modules($conf,$langs,$object,$head,$h,'group','remove');

    return $head;
}



/**
 * Prepare array with list of tabs
 *
 * @return  array				Array of tabs to shoc
 */
function user_admin_prepare_head()
{
	global $langs, $conf, $user;

	$langs->load("users");
	$h=0;
	
    $head[$h][0] = DOL_URL_ROOT.'/admin/user.php';
    $head[$h][1] = $langs->trans("Parameters");
    $head[$h][2] = 'card';
    $h++;
	
    $head[$h][0] = DOL_URL_ROOT.'/user/admin/user_extrafields.php';
    $head[$h][1] = $langs->trans("ExtraFields");
    $head[$h][2] = 'attributes';
    $h++;
    
	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'useradmin');

	complete_head_from_modules($conf,$langs,$object,$head,$h,'useradmin','remove');

	return $head;
}



/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @param	array	$aEntities	Entities array
 * @return  array				Array of tabs
 */
function entity_prepare_head($object, $aEntities)
{
	global $mc;

	$head = array();

	foreach($aEntities as $entity)
	{
		$mc->getInfo($entity);
		$head[$entity][0] = $_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;entity='.$entity;
		$head[$entity][1] = $mc->label;
		$head[$entity][2] = $entity;
	}

	return $head;
}

/**
 * 	Show list of themes. Show all thumbs of themes
 *
 * 	@param	User	$fuser				User concerned or '' for global theme
 * 	@param	int		$edit				1 to add edit form
 * 	@param	boolean	$foruserprofile		Show for user profile view
 * 	@return	void
 */
function show_theme($fuser,$edit=0,$foruserprofile=false)
{
    global $conf,$langs,$bc;

    $forcethemedir=(! empty($conf->global->MAIN_FORCETHEMEDIR) ? $conf->global->MAIN_FORCETHEMEDIR : '');
    $dirthemes=array($forcethemedir.'/theme');
    if (! empty($conf->modules_parts['themes'])) {
    	$dirthemes=array_merge(array($forcethemedir.'/theme'),(array) $conf->modules_parts['themes']);
    }

    $selected_theme='';
    if (empty($foruserprofile)) $selected_theme=$conf->global->MAIN_THEME;
    else $selected_theme=empty($fuser->conf->MAIN_THEME)?'':$fuser->conf->MAIN_THEME;

    $colspan=2;
    if ($foruserprofile) $colspan=4;

    $thumbsbyrow=6;
    print '<table class="noborder" width="100%">';

    $var=false;

    // Title
    if ($foruserprofile)
    {
    	print '<tr class="liste_titre"><th width="25%">'.$langs->trans("Parameter").'</th><th width="25%">'.$langs->trans("DefaultValue").'</th>';
    	print '<th colspan="2">&nbsp;</th>';
	    print '</tr>';

	    print '<tr '.$bc[$var].'>';
	    print '<td>'.$langs->trans("DefaultSkin").'</td>';
	    print '<td>'.$conf->global->MAIN_THEME.'</td>';
	    print '<td align="left" nowrap="nowrap" width="20%"><input '.$bc[$var].' name="check_MAIN_THEME"'.($edit?'':' disabled').' type="checkbox" '.($selected_theme?" checked":"").'> '.$langs->trans("UsePersonalValue").'</td>';
	    print '<td>&nbsp;</td>';
	    print '</tr>';
    }
    else
    {
    	print '<tr class="liste_titre"><th width="35%">'.$langs->trans("DefaultSkin").'</th>';
    	print '<th align="right">';
    	$url='http://www.dolistore.com/lang-en/4-skins';
    	if (preg_match('/fr/i',$langs->defaultlang)) $url='http://www.dolistore.com/lang-fr/4-themes';
    	//if (preg_match('/es/i',$langs->defaultlang)) $url='http://www.dolistore.com/lang-es/4-themes';
    	print '<a href="'.$url.'" target="_blank">';
    	print $langs->trans('DownloadMoreSkins');
    	print '</a>';
    	print '</th></tr>';

    	print '<tr '.$bc[$var].'>';
    	print '<td>'.$langs->trans("ThemeDir").'</td>';
    	print '<td>';
    	foreach($dirthemes as $dirtheme)
    	{
    		echo '"'.$dirtheme.'" ';
    	}
    	print '</td>';
    	print '</tr>';
    }

    $var=!$var;
    print '<tr '.$bc[$var].'><td colspan="'.$colspan.'">';

    print '<table class="nobordernopadding" width="100%">';

    $i=0;

    foreach($dirthemes as $dir)
    {
    	$dirtheme=dol_buildpath($dir,0);
    	$urltheme=dol_buildpath($dir,1);

    	if (is_dir($dirtheme))
    	{
    		$handle=opendir($dirtheme);
    		if (is_resource($handle))
    		{
    			while (($subdir = readdir($handle))!==false)
    			{
    				if (is_dir($dirtheme."/".$subdir) && substr($subdir, 0, 1) <> '.'
    						&& substr($subdir, 0, 3) <> 'CVS' && ! preg_match('/common|phones/i',$subdir))
    				{
    					// Disable not stable themes
    					//if ($conf->global->MAIN_FEATURES_LEVEL < 1 && preg_match('/bureau2crea/i',$subdir)) continue;

    					if ($i % $thumbsbyrow == 0)
    					{
    						print '<tr '.$bc[$var].'>';
    					}

    					print '<td align="center">';
    					$file=$dirtheme."/".$subdir."/thumb.png";
    					$url=$urltheme."/".$subdir."/thumb.png";
    					if (! file_exists($file)) $url=$urltheme."/common/nophoto.jpg";
    					print '<table><tr><td>';
    					print '<a href="'.$_SERVER["PHP_SELF"].($edit?'?action=edit&theme=':'?theme=').$subdir.(GETPOST("optioncss")?'&optioncss='.GETPOST("optioncss",'alpha',1):'').($fuser?'&id='.$fuser->id:'').'" style="font-weight: normal;" alt="'.$langs->trans("Preview").'">';
    					if ($subdir == $conf->global->MAIN_THEME) $title=$langs->trans("ThemeCurrentlyActive");
    					else $title=$langs->trans("ShowPreview");
    					print '<img src="'.$url.'" border="0" width="80" height="60" alt="'.$title.'" title="'.$title.'">';
    					print '</a>';
    					print '</td></tr><tr><td align="center">';
    					if ($subdir == $selected_theme)
    					{
    						print '<input '.($edit?'':'disabled').' type="radio" '.$bc[$var].' style="border: 0px;" checked name="main_theme" value="'.$subdir.'"> <b>'.$subdir.'</b>';
    					}
    					else
    					{
    						print '<input '.($edit?'':'disabled').' type="radio" '.$bc[$var].' style="border: 0px;" name="main_theme" value="'.$subdir.'"> '.$subdir;
    					}
    					print '</td></tr></table></td>';

    					$i++;

    					if ($i % $thumbsbyrow == 0) print '</tr>';
    				}
    			}
    		}
    	}
    }

    if ($i % $thumbsbyrow != 0)
    {
        while ($i % $thumbsbyrow != 0)
        {
            print '<td>&nbsp;</td>';
            $i++;
        }
        print '</tr>';
    }
    print '</table>';

    print '</td></tr>';
    print '</table>';
}

?>
