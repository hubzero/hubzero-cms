<?php
/**
 * @version		$Id: install.php 33 2007-12-19 10:26:16Z andrew.eddie $
 * @package		joomdle
 * @copyright	2003-2009 Think Network GmbH, Munich
 * @license		GNU General Public License
 * 
 * This is the special installer addon created by Andrew Eddie and the team of jXtended.
 * We thank for this cool idea of extending the installation process easily
 * copyright	2005-2008 New Life in IT Pty Ltd.  All rights reserved.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

//$nPaths = $this->_paths;
$status = new JObject();
$status->modules = array();
$status->plugins = array();

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* MODULE INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$modules = &$this->manifest->getElementByPath('modules');
if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {

	foreach ($modules->children() as $module)
	{
		$mname		= $module->attributes('module');
		$mclient	= JApplicationHelper::getClientInfo($module->attributes('client'), true);

		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DS.'modules'.DS.$mname);
		} else {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('No module file specified'));
			return false;
		}

		/*
		* If the module directory already exists, then we will assume that the
		* module is already installed or another module is using that directory.
		*/
		if (file_exists($this->parent->getPath('extension_root'))&&!$this->parent->getOverwrite()) {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Another module is already using directory').': "'.$this->parent->getPath('extension_root').'"');
			return false;
		}

		// If the module directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		* Since we created the module directory and will want to remove it if
		* we have to roll back the installation, lets add it to the
		* installation step stack
		*/
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		$element = &$module->getElementByPath('files');
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy language files
		$element = &$module->getElementByPath('languages');
		if ($this->parent->parseLanguages($element, $mclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy media files
		$element = &$module->getElementByPath('media');
		if ($this->parent->parseMedia($element, $mclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		$mtitle		= $module->attributes('title');
		$mposition	= $module->attributes('position');
		$morder		= $module->attributes('order');

		if ($mtitle && $mposition) {
			// if module already installed do not create a new instance
			$db =& JFactory::getDBO();
			$query = 'SELECT `id` FROM `#__modules` WHERE module = '.$db->Quote( $mname);
			$db->setQuery($query);
			if (!$db->Query()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}
			$id = $db->loadResult();

			if (!$id){
				$row = & JTable::getInstance('module');
				$row->title		= $mtitle;
				$row->ordering	= $morder;
				$row->position	= $mposition;
				$row->showtitle	= 1;
				$row->iscore	= 0;
				$row->access	= ($mclient->id) == 1 ? 2 : 0;
				$row->client_id	= $mclient->id;
				$row->module	= $mname;
				$row->published	= 0;
				$row->params	= '';

				if (!$row->store()) {
					// Install failed, roll back changes
					$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
					return false;
				}
				
				// Make visible evertywhere if site module
				if ($mclient->id==0){
					$query = 'REPLACE INTO `#__modules_menu` (moduleid,menuid) values ('.$db->Quote( $row->id).',0)';
					$db->setQuery($query);
					if (!$db->query()) {
						// Install failed, roll back changes
						$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
						return false;
					}
				}


			}


		}

		$status->modules[] = array('name'=>$mname,'client'=>$mclient->name);
	}
}


/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* PLUGIN INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$plugins = &$this->manifest->getElementByPath('plugins');
if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {

	foreach ($plugins->children() as $plugin)
	{
		$pname		= $plugin->attributes('plugin');
		$pgroup		= $plugin->attributes('group');
		$porder		= $plugin->attributes('order');

		// Set the installation path
		if (!empty($pname) && !empty($pgroup)) {
			$this->parent->setPath('extension_root', JPATH_ROOT.DS.'plugins'.DS.$pgroup);
		} else {
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('No plugin file specified'));
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// If the plugin directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		* If we created the plugin directory and will want to remove it if we
		* have to roll back the installation, lets add it to the installation
		* step stack
		*/
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		$element = &$plugin->getElementByPath('files');
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy all necessary files
		$element = &$plugin->getElementByPath('languages');
		if ($this->parent->parseLanguages($element, 1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy media files
		$element = &$plugin->getElementByPath('media');
		if ($this->parent->parseMedia($element, 1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		$db = &JFactory::getDBO();

		// Check to see if a plugin by the same name is already installed
		$query = 'SELECT `id`' .
		' FROM `#__plugins`' .
		' WHERE folder = '.$db->Quote($pgroup) .
		' AND element = '.$db->Quote($pname);
		$db->setQuery($query);
		if (!$db->Query()) {
			// Install failed, roll back changes
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();

		// Was there a plugin already installed with the same name?
		if ($id) {

			if (!$this->parent->getOverwrite())
			{
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Plugin').' "'.$pname.'" '.JText::_('already exists!'));
				return false;
			}

		} else {
			$row =& JTable::getInstance('plugin');
			$row->name = JText::_(ucfirst($pgroup)).' - '.JText::_(ucfirst($pname));
			$row->ordering = $porder;
			$row->folder = $pgroup;
			$row->iscore = 0;
			$row->access = 0;
			$row->client_id = 0;
			$row->element = $pname;
			$row->published = 1;
			$row->params = '';

			if (!$row->store()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}
		}

		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup);
	}
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* SETUP DEFAULTS
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
// Check to see if a plugin by the same name is already installed
/*
$query = 'SELECT `id`' .
' FROM `#__components`' .
' WHERE parent = 0 and name=' .$db->Quote('Joom!Fish').
' AND parent = 0';
$db->setQuery($query);
$componentID = $db->loadResult();

if(!is_null($componentID) && $componentID > 0) {
	$query = 'UPDATE #__components SET params = '
		. $db->Quote("noTranslation=2\n"
		. "defaultText=\n"
		. "overwriteGlobalConfig=1\n"
		. "storageOfOriginal=md5\n"
		. "frontEndPublish=1\n"
		. "frontEndPreview=1\n"
		. "showPanelNews=1\n"
		. "showPanelUnpublished=1\n"
		. "showPanelState=1\n"
		. "copyparams=1\n"
		. "transcaching=0\n"
		. "cachelife=180\n"
		. "qacaching=1\n"
		. "qalogging=0\n")
		. 'WHERE id = ' . $componentID;
	$db->setQuery($query);
		
	if (!$db->Query()) {
		// Install failed, roll back changes
		$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
		return false;
	}
}

// Insert the default lanauage if no language exist
$query = 'SELECT count(*) FROM #__languages';
$db->setQuery($query);
$count = $db->loadResult();

if($count==0) {
	$query = 'INSERT INTO #__languages VALUES (1, '
		.$db->quote('English (United Kingdom)')
		. ', 1, ' .$db->quote('en_GB.utf8, en_GB.UT'). ', ' . $db->quote('en-GB')
		. ', '. $db->quote('en') .', '. $db->quote('').', '. $db->quote('').', '. $db->quote(''). ', 1);';
	$db->execute($query);
}
*/
/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* Execute specific system steps to ensure a consistent installtion
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
/*
// check if prePost_translations.xml exist and if yes remove it
if( JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS.'prepostTranslation.xml') ) {
	JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS.'prepostTranslation.xml');
}
*/

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* Configuration default values
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$db =& JFactory::getDBO();
$query = "UPDATE `#__components` set params='auto_create_users=1
MOODLE_URL=
connection_method=fgc
auto_delete_users=1
auto_login_users=0
linkstarget=wrapper
scrolling=no
width=100%
height=1000
autoheight=1
transparency=0
default_itemid=
show_topìcs_link=1
show_grading_system_link=0
show_teachers_link=0
show_enrol_link=1
show_paypal_button=0
shop_integration=0
courses_category=0
buy_for_children=0
enrol_email_subject=Welcome to COURSE_NAME
enrol_email_text=To enter the course, go to: COURSE_URL
additional_data_source=none
use_xipt_integration=0'
WHERE name='Joomdle'";

$db->setQuery($query);
if (!$db->Query()) {
	return false;
}


// Create tables
    $allQueries     = array();

        $allQueries[]   ='CREATE TABLE IF NOT EXISTS `#__joomdle_field_mappings` (
			  `id` int(11) NOT NULL auto_increment,
			  `joomla_app` varchar(45) NOT NULL,
			  `joomla_field` varchar(45) NOT NULL,
			  `moodle_field` varchar(45) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8';


        $allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_profiletypes` (
			  `id` int(11) NOT NULL auto_increment,
			  `profiletype_id` int(11) NOT NULL,
			  `create_on_moodle` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8';

	$allQueries[]   = 'CREATE TABLE IF NOT EXISTS `#__joomdle_purchased_courses` (
			  `id` int(11) NOT NULL auto_increment,
			  `user_id` int(11) NOT NULL,
			  `course_id` int(11) NOT NULL,
			  `num` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8';


   $db=& JFactory::getDBO();
        foreach($allQueries as $query) {
                $db->setQuery( $query );
                $db->query();
        }



/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* OUTPUT TO SCREEN
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$rows = 0;
?>

<h2>Joomdle Installation</h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'Joomdle '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
	endif;
if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table> 
