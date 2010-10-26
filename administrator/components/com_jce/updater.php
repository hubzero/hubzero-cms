<?php
/**
* @version		$Id: updater.php 116 2009-06-23 11:32:04Z happynoodleboy $
* @package		JCE Component
* @copyright	Copyright (C) 2006 - 2009 Ryan Demmer. All rights reserved.
* @license		GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class JCEUpdater extends JObject 
{
	/**
	* Constructor activating the default information of the class
	*
	* @access	protected
	*/
	function __construct()
	{
		$language =& JFactory::getLanguage();	
		$language->load('com_jce', JPATH_ADMINISTRATOR);
	}
	/**
	 * Returns a reference to a editor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser = &JContentEditor::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function &getInstance()
	{
		static $instance;

		if (!is_object($instance)) {
			$instance = new JCEUpdater();
		}
		return $instance;
	}
	/**
	 * Check upgrade / database status
	 */
	function initCheck()
	{	
		global $mainframe;
		// Check Plugins DB
		if (!$this->checkTable('plugins')) {
			$link = JHTML::link('index.php?option=com_jce&amp;task=repair&amp;type=plugins', JText::_('DB CREATE RESTORE'));			
			return $this->redirect(JText::_('DB PLUGINS ERROR') .' - '. $link, 'error');
		}
		// Check Groups DB
		if (!$this->checkTable('groups')) {
			$link = JHTML::link('index.php?option=com_jce&amp;task=repair&amp;type=groups', JText::_('DB CREATE RESTORE'));			
			return $this->redirect(JText::_('DB GROUPS ERROR') .' - '. $link, 'error');
		}
		// Check Editor is installed
		if (!$this->checkEditorFiles()) {
			return $this->redirect(JText::_('EDITOR FILES ERROR'), 'error');
		}
		if (!$this->checkEditor() && $this->checkEditorFiles()) {
			$link = JHTML::link('index.php?option=com_jce&amp;task=repair&amp;type=editor', JText::_('EDITOR INSTALL'));
			return $this->redirect(JText::_('EDITOR INSTALLED MANUAL ERROR') .' - '. $link, 'error');
		}
		// Check Editor is installed
		if (!$this->checkEditor()) {
			return $this->redirect(JText::_('EDITOR INSTALLED ERROR'), 'error');
		}
		// Check Editor is enabled
		if (!$this->checkEditorEnabled()) {
			return $this->redirect(JText::_('EDITOR ENABLED ERROR'), 'error');
		}
		/*if (!$this->checkEditorDefault()) {
			$mainframe->enqueueMessage(JText::_('EDITOR DEFAULT NOTICE'), 'notice');
		}*/
		// Check Update
		if (!$this->checkUpdate()) {
			$link = JHTML::link('index.php?option=com_jce&amp;task=repair&amp;type=update', JText::_('DB UPDATE'));			
			return $this->redirect(JText::_('DB UPDATE MSG') .' - '. $link, 'error');
		}
	}
	/**
	 * Redirect with message
	 * @param object $msg[optional] Message to display
	 * @param object $state[optional] Message type
	 */
	function redirect($msg = '', $state = '')
	{
		global $mainframe;
		if ($msg) {
			$mainframe->enqueueMessage($msg, $state);
		}
		JRequest::setVar('type', 'cpanel');
		JRequest::setVar('task', '');
		
		return false;	
	}
	/**
	 * Backup a table by renaming to [table_name]_tmp
	 * @return boolean
	 * @param string $table Table to backup
	 */
	function backupTable($table)
	{
		$db	=& JFactory::getDBO();
		
		// Table must exist
		if ($this->checkTable($table)) {
			// Check for tmp table
			if (!$this->checkTable($table . '_tmp')) {
				$query = 'RENAME TABLE #__jce_'. $table .' TO #__jce_'. $table .'_tmp';
				$db->setQuery($query);
				return $db->query();
			}
			return true;
		} else {
			return $this->checkTable($table . '_tmp');
		}
		return false;
	}
	/**
	 * Check whether a table exists
	 * @return boolean 
	 * @param string $table Table name
	 */
	function checkTable($table)
	{
		$db		=& JFactory::getDBO();	
		
		$tables = $db->getTableList();
		
		return in_array($db->replacePrefix('#__jce_'.$table), $tables);
	}
	/**
	 * Check whether a field exists
	 * @return boolean 
	 * @param string $table Table name
	 */
	function checkField($table, $field)
	{
		$db		=& JFactory::getDBO();	
		
		$fields = $db->getTableFields($table);
		
		return array_key_exists($field, $fields[$table]);
	}
	/**
	 * Rename / Backup all tables
	 */
	function purgeDB()
	{
		global $mainframe;
		$db		=& JFactory::getDBO();	
		$tables = array('plugins', 'extensions', 'groups');
		
		foreach ($tables as $table) {
			// Backup table to temp. Will be removed on uninstall			
			if (!$this->backupTable($table)) {
				$msg 	= JText::_('DB PURGE '. strtoupper($table) .' ERROR');
				$state 	= 'error';
			} else {
				$msg 	= JText::_('DB PURGE '. strtoupper($table) .' SUCCESS');
				$state 	= '';
			}
			$mainframe->enqueueMessage($msg, $state);
		}
		$this->redirect();
	}
	/**
	 * Check if all backup tables exist
	 * @return boolean
	 */
	function purgeCheck()
	{
		$ret 	= false;
		$tables = array('plugins', 'extensions', 'groups');
		foreach ($tables as $table) {
			$ret = $this->checkTable($table) && !$this->checkTable($table .'_tmp');
		}
		return $ret;
	}
	/**
	 * Remove all backup tables
	 */
	function cleanupDB()
	{
		$db	=& JFactory::getDBO();	
		
		$tables = array('plugins', 'groups', 'extensions');
		
		foreach ($tables as $table) {
			$query = 'DROP TABLE IF EXISTS #__jce_'. $table .'_tmp';
			$db->setQuery($query);
			
			$db->query();
		}
	}
	/**
	 * Check for earlier version to trigger update
	 * @return boolean
	 */
	function checkUpdate()
	{
		// Check for Readmore plugin indicates 1.5.0
		global $mainframe;
		$db	=& JFactory::getDBO();
		
		$ret = false;

		$query = 'SELECT count(id)'
		. ' FROM #__jce_plugins'
		. ' WHERE name = '. $db->Quote('readmore')
		;	
		$db->setQuery($query);
		$ret = $db->loadResult() ? false : true;
		
		return $ret;
	}
	/**
	 * Check whether the editor is installed
	 * @return boolean
	 */
	function checkEditor()
	{
		$db	=& JFactory::getDBO();
		
		$query = 'SELECT id'
		. ' FROM #__plugins'
		. ' WHERE element = '. $db->Quote('jce')
		;
		$db->setQuery($query);		
		return $db->loadResult();
	}
	/**
	 * Check for existence of editor files and folder
	 * @return boolean
	 */
	function checkEditorFiles()
	{
		$path = JPATH_PLUGINS .DS. 'editors';
		// Check for JCE plugin files
		return file_exists($path .DS. 'jce.php') && file_exists($path .DS. 'jce.xml') && is_dir($path .DS. 'jce');
	}
	/**
	 * Check if the editor is enabled
	 * @return boolean
	 */
	function checkEditorEnabled()
	{
		$db	=& JFactory::getDBO();
		
		$query = 'SELECT published FROM #__plugins'
		.' WHERE element = '. $db->Quote('jce')
		;
		$db->setQuery($query);
		return $db->loadResult();
	}
	/** 
	 * Check if the editor is set as the default wysiwyg
	 * @return boolean
	 */
	function checkEditorDefault()
	{
		$conf =& JFactory::getConfig();
		if (JRequest::getVar('type', 'cpanel') == 'cpanel') {
			return $conf->getValue('config.editor') == 'jce';
		}
		return true;
	}
	/**
	 * Format / escape an IN / NOT IN list
	 * @return 
	 * @param object $list
	 */
	function dbList($list)
	{
		if (is_array($list)) {
			$ret = array();
			foreach ($list as $item) {
				$ret[] = "'". $item ."'";
			}
			return implode(',', $ret);
		}
		return $list;
	}
	/**
	 * Update the JCE Tables
	 * @return Redirect
	 * @param object $install[optional]
	 */
	function updateDB($install = false)
	{
		global $mainframe;
		$db =& JFactory::getDBO();

		// Create tmp tables
		if ($this->backupTable('plugins') && $this->backupTable('groups')) {			
			// Update Plugins
			if ($this->updatePlugins()) {
				// Update Groups
				if (!$this->updateGroups()) {
					$mainframe->enqueueMessage(JText::_('UPDATE GROUPS ERROR'), 'error');
				}
			} else {
				$mainframe->enqueueMessage(JText::_('UPDATE PLUGINS ERROR'), 'error');
			}			
		} else {
			// Unable to perform update!
			$mainframe->enqueueMessage(JText::_('UPDATE ERROR'), 'error');
		}
		
		// Add Admin Menu options
		$query = "UPDATE #__components SET `admin_menu_img` = '../administrator/components/com_jce/img/logo.png'"
		. " WHERE link = " . $db->Quote('option=com_jce')
		;
		
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage(JText::_('ADMIN MENU IMAGE ERROR'), 'error');
		}
		if (!$install) {	
			$this->redirect();
		}
	}
	/**
	 * Get an array of commands / plugins
	 * @return array
	 */
	function getButtons()
	{		
		$buttons = array(
			'contextmenu'	=>	array(
				'title'	=>	'Context Menu',
				'row'	=>	0
			),
			'browser'	=>	array(
				'title'	=>	'File Browser',
				'row'	=>	0,
				'editable'	=>	1
			),
			'inlinepopups'	=>	array(
				'title'	=>	'Inline Popups',
				'row'	=>	0
			),
			'media'	=>	array(
				'title'	=>	'Media Support',
				'row'	=>	0,
				'editable'	=>	1
			),
			'safari'	=>	array(
				'title'	=>	'Safari Browser Support',
				'row'	=>	0
			),
			'help'	=>	array(
				'title'	=>	'Help',
				'row'	=>	1
			),
			'newdocument'	=>	array(
				'title'	=>	'New Document',
				'row'	=>	1
			),
			'bold'	=>	array(
				'title'	=>	'Bold',
				'row'	=>	1
			),
			'italic'	=>	array(
				'title'	=>	'Italic',
				'row'	=>	1
			),
			'underline'	=>	array(
				'title'	=>	'Underline',
				'row'	=>	1
			),
			'fontselect'	=>	array(
				'title'	=>	'Font Select',
				'row'	=>	1
			),
			'fontsizeselect'	=>	array(
				'title'	=>	'Font Size Select',
				'row'	=>	1
			),
			'styleselect'	=>	array(
				'title'	=>	'Style Select',
				'row'	=>	1
			),
			'strikethrough'	=>	array(
				'title'	=>	'StrikeThrough',
				'row'	=>	1
			),
			'full'	=>	array(
				'title'	=>	'Justify Full',
				'row'	=>	1,
				'icon'	=>	'justifyfull',
				'layout'=>	'justifyfull'
			),
			'center'	=>	array(
				'title'	=>	'Justify Center',
				'row'	=>	1,
				'icon'	=>	'justifycenter',
				'layout'=>	'justifycenter'
			),
			'left'	=>	array(
				'title'	=>	'Justify Left',
				'row'	=>	1,
				'icon'	=>	'justifyleft',
				'layout'=>	'justifyleft'
			),
			'right'	=>	array(
				'title'	=>	'Justify Right',
				'row'	=>	1,
				'icon'	=>	'justifyright',
				'layout'=>	'justifyright'
			),
			'formatselect'	=>	array(
				'title'	=>	'Format Select',
				'row'	=>	1
			),
			'paste'	=>	array(
				'title'	=>	'Paste',
				'icon'	=>	'cut,copy,paste',
				'row'	=>	2,
				'editable'	=>	1
			),
			'searchreplace'	=>	array(
				'title'	=>	'Search Replace',
				'icon'	=>	'search,replace',
				'row'	=>	2
			),
			'forecolor'	=>	array(
				'title'	=>	'Font ForeColour',
				'row'	=>	2
			),
			'backcolor'	=>	array(
				'title'	=>	'Font BackColour',
				'row'	=>	2
			),
			'unlink'	=>	array(
				'title'	=>	'Unlink',
				'row'	=>	2
			),
			'indent'	=>	array(
				'title'	=>	'Indent',
				'row'	=>	2
			),
			'outdent'	=>	array(
				'title'	=>	'Outdent',
				'row'	=>	2
			),
			'undo'	=>	array(
				'title'	=>	'Undo',
				'row'	=>	2
			),
			'redo'	=>	array(
				'title'	=>	'Redo',
				'row'	=>	2
			),
			'html'	=>	array(
				'title'	=>	'HTML',
				'icon'	=>	'code',
				'layout'=>	'code',
				'row'	=>	2
			),
			'numlist'	=>	array(
				'title'	=>	'Numbered List',
				'row'	=>	2
			),
			'bullist'	=>	array(
				'title'	=>	'Bullet List',
				'row'	=>	2
			),
			/*'clipboard'	=>	array(
				'title'	=>	'Clipboard Actions',
				'icon'	=>	'cut,copy,paste',
				'row'	=>	2
			),*/
			'anchor'	=>	array(
				'title'	=>	'Anchor',
				'row'	=>	2
			),
			'image'	=>	array(
				'title'	=>	'Image',
				'row'	=>	2
			),
			'link'	=>	array(
				'title'	=>	'Link',
				'row'	=>	2
			),
			'cleanup'	=>	array(
				'title'	=>	'Code Cleanup',
				'row'	=>	2
			),
			'directionality'	=>	array(
				'title'	=>	'Directionality',
				'icon'	=>	'ltr,rtl',
				'row'	=>	3
			),
			'emotions'	=>	array(
				'title'	=>	'Emotions',
				'row'	=>	3
			),
			'fullscreen'	=>	array(
				'title'	=>	'Fullscreen',
				'row'	=>	3
			),
			'preview'	=>	array(
				'title'	=>	'Preview',
				'row'	=>	3
			),
			'table'	=>	array(
				'title'	=>	'Tables',
				'icon'	=>	'tablecontrols',
				'layout'=>	'buttons',
				'row'	=>	3
			),
			'print'	=>	array(
				'title'	=>	'Print',
				'row'	=>	3
			),
			'hr'	=>	array(
				'title'	=>	'Horizontal Rule',
				'row'	=>	3
			),
			'sub'	=>	array(
				'title'	=>	'Subscript',
				'row'	=>	3
			),
			'sup'	=>	array(
				'title'	=>	'Superscript',
				'row'	=>	3
			),
			'visualaid'	=>	array(
				'title'	=>	'Visual Aid',
				'row'	=>	3
			),
			'charmap'	=>	array(
				'title'	=>	'Character Map',
				'row'	=>	3
			),
			'removeformat'	=>	array(
				'title'	=>	'Remove Format',
				'row'	=>	2
			),
			'style'	=>	array(
				'title'	=>	'Styles',
				'icon'	=>	'styleprops',
				'row'	=>	4
			),
			'nonbreaking'	=>	array(
				'title'	=>	'Non-Breaking',
				'row'	=>	4
			),
			'visualchars'	=>	array(
				'title'	=>	'Visual Characters',
				'row'	=>	4
			),
			'xhtmlxtras'	=>	array(
				'title'	=>	'XHTML Xtras',
				'icon'	=>	'cite,abbr,acronym,del,ins,attribs',
				'row'	=>	4
			),
			'imgmanager'	=>	array(
				'title'	=>	'Image Manager',
				'row'	=>	4,
				'editable'	=>	1
			),
			'advlink'	=>	array(
				'title'	=>	'Advanced Link',
				'row'	=>	4,
				'editable'	=>	1
			),
			'spellchecker'	=>	array(
				'title'	=>	'Spell Checker',
				'row'	=>	4,
				'editable'	=>	1
			),
			'layer'	=>	array(
				'title'	=>	'Layers',
				'icon'	=>	'insertlayer,moveforward,movebackward,absolute',
				'row'	=>	4
			),
			'advcode'	=>	array(
				'title'	=>	'Advanced Code Editor',
				'row'	=>	4
			),
			'article'	=>	array(
				'title'	=>	'Article Breaks',
				'icon'	=>	'readmore,pagebreak',
				'row'	=>	4
			)
		);
		return $buttons;
	}
	/**
	 * Create the Plugins table
	 * @return boolean
	 */
	function createPluginsTable()
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		$query = "CREATE TABLE IF NOT EXISTS `#__jce_plugins` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`name` varchar(255) NOT NULL,
		`type` varchar(255) NOT NULL,
		`icon` varchar(255) NOT NULL,
		`layout` varchar(255) NOT NULL,
		`row` int(11) NOT NULL,
		`ordering` int(11) NOT NULL,
		`published` tinyint(3) NOT NULL,
	 	`editable` tinyint(3) NOT NULL,
		`iscore` tinyint(3) NOT NULL,
		`elements` varchar(255) NOT NULL,
		`checked_out` int(11) NOT NULL,
		`checked_out_time` datetime NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY `plugin` (`name`)
		);";
		$db->setQuery($query);
		
		if (!$db->query()) {
			$mainframe->enqueueMessage(JText::_('CREATE TABLE PLUGINS ERROR').' : '.$db->stdErr(), 'error');
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Create the Plugin Extensions table
	 * @return boolean
	 */
	function createExtensionsTable()
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		// Extensions
		$query = "CREATE TABLE IF NOT EXISTS `#__jce_extensions` (
		`id` int(11) NOT NULL auto_increment,
		`pid` int(11) NOT NULL,
		`name` varchar(100) NOT NULL,
		`extension` varchar(255) NOT NULL,
		`folder` varchar(255) NOT NULL,
		`published` tinyint(3) NOT NULL,
		PRIMARY KEY  (`id`)
		)";
		$db->setQuery($query);
		
		if (!$db->query()) {
			$mainframe->enqueueMessage(JText::_('CREATE TABLE EXTENSIONS ERROR').' : '.$db->stdErr(), 'error');
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Create the Groups table
	 * @return boolean
	 */
	function createGroupsTable()
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		$query = "CREATE TABLE IF NOT EXISTS `#__jce_groups` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL,
		`description` varchar(255) NOT NULL,
		`users` text NOT NULL,
		`types` varchar(255) NOT NULL,
		`components` text NOT NULL,
		`rows` text NOT NULL,
		`plugins` varchar(255) NOT NULL,
		`published` tinyint(3) NOT NULL,
		`ordering` int(11) NOT NULL,
		`checked_out` tinyint(3) NOT NULL,
		`checked_out_time` datetime NOT NULL,
		`params` text NOT NULL,
		PRIMARY KEY (`id`)
		);";
		$db->setQuery($query);
		
		if (!$db->query()) {
			$mainframe->enqueueMessage(JText::_('CREATE TABLE GROUPS ERROR').' : '.$db->stdErr(), 'error');
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Install Groups
	 * @return boolean
	 * @param object $install[optional]
	 */
	function installGroups($install = false)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		$ret = false;
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'groups');

		if ($this->createGroupsTable()) {
			$ret = true;
			
			$query = 'SELECT count(id) FROM #__jce_groups';
			$db->setQuery($query);
			
			$groups = array(
				'Default'	=>	false,
				'Front End'	=>	false
			);
			
			// No Groups table data
			if (!$db->loadResult()) {
				// Exclude these. Will be removed in 1.6
				$exclude = array('layer', 'image', 'link', 'html');
				
				$query = 'SELECT id FROM #__jce_plugins'
				. ' WHERE type = '. $db->Quote('plugin')
				. ' AND name NOT IN ('. $this->dbList($exclude) .')'
				. ' AND published = 1';
				$db->setQuery($query);
				$plugins = $db->loadResultArray();

				$rows = array();
				
				$query = 'SELECT DISTINCT row FROM #__jce_plugins WHERE row > 0';
				$db->setQuery($query);
				$num = $db->loadResultArray();
				
				foreach ($num as $n) {
					$query = 'SELECT id FROM #__jce_plugins WHERE row = '. $n .' AND name NOT IN ('. $this->dbList($exclude) .')';
					$db->setQuery($query);				
					$rows[] = implode(',', $db->loadResultArray());
				}
				
				$row =& JTable::getInstance('groups', 'JCETable');
				
				$row->name 			= 'Default';
				$row->description 	= 'Default group for all users with edit access';
				$row->types			= '19,20,21,23,24,25';
				$row->rows			= implode(';', $rows);
				$row->plugins		= implode(',', $plugins);
				$row->published		= 1;
				$row->ordering		= 1;
				
				$groups['Default'] 	= $row->store();
				
				// TODO : Move this out to an xml file or something
				
				$row =& JTable::getInstance('groups', 'JCETable');
				
				$tmpl = array(
					'help,newdocument,undo,redo,bold,italic,underline,strikethrough,left,center,full,right,styleselect,formatselect',
					'sub,sup,numlist,bullist,indent,outdent,hr,charmap,visualchars,nonbreaking,searchreplace,clipboard,paste,removeformat,cleanup',
					'fullscreen,preview,print,visualaid,style,xhtmlxtras,anchor,unlink,advlink,imgmanager,spellchecker,advcode,article,contextmenu,safari,inlinepopups'
				);
				
				$plugins 	= array();
				$rows 		= array();
				
				foreach ($tmpl as $item) {
					$query = 'SELECT id FROM #__jce_plugins WHERE name IN ('. $this->dbList(explode(',', $item)) .') AND row > 0';
					$db->setQuery($query);
								
					$rows[] = implode(',', $db->loadResultArray());
					
					$query = 'SELECT id FROM #__jce_plugins WHERE name IN ('. $this->dbList(explode(',', $item)) .') AND type = '. $db->Quote('plugin');
					$db->setQuery($query);				
					$plugins[] = implode(',', $db->loadResultArray());
				}
				
				$row->name 			= 'Front End';
				$row->description 	= 'Sample Group for Authors, Editors, Publishers';
				$row->types			= '19,20,21';
				$row->rows			= implode(';', $rows);
				$row->plugins		= implode(',', $plugins);
				$row->published		= 0;
				$row->ordering		= 2;
				
				$groups['Front End'] = $row->store();
				
				// Print message
				foreach ($groups as $k => $v) {
					if (!$v) {
						$mainframe->enqueueMessage(JText::_('GROUP INSTALL ERROR '). ' - '. $k, 'error');
					}
				}
			}
		}
		if (!$install) {	
			$this->redirect();
		}
		return $ret;
	}
	/**
	 * Update Groups table
	 * @return boolean
	 */
	function updateGroups()
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if ($this->installGroups(true)) {
			// Check for tmp tables
			if ($this->checkTable('groups_tmp')) {
				// empty Groups table
				$query = 'TRUNCATE TABLE #__jce_groups';
				$db->setQuery($query);
				$db->query() or die($db->stdErr());
				
				JTable::addIncludePath(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'groups');
				// Get old Groups list
				$query = 'SELECT * FROM #__jce_groups_tmp';
				$db->setQuery($query);
				$groups = $db->loadObjectList();
				
				foreach ($groups as $group) {
					$rows		= explode(';', $group->rows);
					$items		= array();
					// tmp Plugins table must exist for proper upgrade
					if ($this->checkTable('plugins_tmp')) {
						$query = 'SELECT a.id from #__jce_plugins AS a'
						. ' INNER JOIN #__jce_plugins_tmp AS b ON a.name = b.name'
						. ' WHERE b.id IN ('. $group->plugins .')'
						;
						$db->setQuery($query);
						$plugins = $db->loadResultArray();
					} else {
						$plugins = $group->plugins;
					}
					
					$row =& JTable::getInstance('groups', 'JCETable');
					$row->plugins = implode(',', $plugins);
					// tmp Plugins table must exist for proper upgrade
					if ($this->checkTable('plugins_tmp')) {
						foreach ($rows as $item) {
							$query = 'SELECT a.id from #__jce_plugins AS a'
							. ' INNER JOIN #__jce_plugins_tmp AS b ON a.name = b.name'
							. ' WHERE b.id IN ('. $item .')'
							;
							$db->setQuery($query);
							$items[] = implode(',', $db->loadResultArray());
						}
					} else {
						$items = $rows;
					}
					$row->rows = implode(';', $items);
					
					// Add additional properties
					$row->name 			= $group->name;
					$row->description 	= $group->description;
					$row->components	= $group->components;
					$row->users			= $group->users;
					$row->types			= $group->types;
					$row->published		= $group->published;
					$row->ordering		= $group->ordering;
					$row->params		= $group->params;
					
					if (!$row->store()) {
						$mainframe->enqueueMessage(JText::_('GROUP UPDATE ERROR ') . $group->name, 'error');
					}
				}
				$this->cleanupDB();
			}
		}
	}
	/**
	 * Install Plugins
	 * @return boolean
	 * @param object $install[optional]
	 */
	function installPlugins($install = false)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		$ret = false;
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'plugins');
		
		if ($this->createPluginsTable()) {
			$ret = true;

			$query = 'SELECT count(id) FROM #__jce_plugins';
			$db->setQuery($query);
			
			if (!$db->loadResult()) {
				// Load table class 
				require_once(dirname(__FILE__) .DS. 'plugins' .DS. 'plugin.php');
				
				// Get list from editor if installed
				$plugins = array('contextmenu','directionality','emotions','fullscreen','paste','preview','table','print','searchreplace','style','layer','nonbreaking','visualchars','xhtmlxtras','imgmanager','advlink','spellchecker','help','browser','inlinepopups','media','safari','advcode','article','code','media');
				
				$errors = array();
				
				$r = 1;
				$x = 0;
				
				$row =& JTable::getInstance('plugin', 'JCETable');
				
				$query = 'INSERT INTO #__jce_plugins (';
				
				foreach ($row as $k	=> $v) {
					if ($k{0} != '_') {
						$query .= '`'. $k . '`,';
					}
				}
				$query = substr($query, 0, -1) . ') VALUES ';
				// Get the buttons array
				$buttons = $this->getButtons();
					
				foreach ($buttons as $k => $v) {					
					$row 			=& JTable::getInstance('plugin', 'JCETable');
							
					$row->name 		= $k;
					$row->published = 1;
					$row->iscore 	= 1;
					$row->editable 	= 0;
					$row->type		= in_array($k, $plugins) ? 'plugin' : 'command';
					
					$row->bind($v);

					// Set Icon and Layout
					if ($row->row) {
						if (!isset($row->icon)) {
							$row->icon = $row->name;
						}
						if (!isset($row->layout)) {
							$row->layout = $row->name;
						}					
						// Set ordering
						if ($row->row == $r) {
							$x++;
						} else {
							$r = $row->row;
							$x = 1;
						}
						$row->ordering = $x;
					}
					$query .= '(';
					foreach ($row as $k	=>	$v) {
						if ($k{0} != '_') {
							$query .= "'". $v . "',";
						}
					}
					$query = substr($query, 0, -1) . '),';	
				}
				$query = substr($query, 0, -1);
				
				$db->setQuery($query);
				if (!$db->query()) {
					$mainframe->enqueueMessage(JText::_('PLUGINS INSTALL ERROR'), 'error');
				}
			}
		}
		
		/*if ($this->createExtensionsTable()) {		
			// Check for JoomlaLinks	
			$query = 'SELECT id FROM #__jce_extensions WHERE extension = '. $db->Quote('joomlalinks');
			$db->setQuery($query);
			// JoomlaLinks is not installed
			if (!$db->loadResult()) {		
				// Get AdvLink id
				$query = 'SELECT id FROM #__jce_plugins WHERE name = '. $db->Quote('advlink');
				$db->setQuery($query);
				$id = $db->loadResult();
				// If Advlink installed
				if ($id) {
					$row 			=& JTable::getInstance('extension', 'JCETable');
					
					$row->pid 		= $id;
					$row->name 		= 'Joomla Links for Advanced Link';
					$row->extension = 'joomlalinks';
					$row->folder 	= 'links';
					$row->published = 1;
					
					if (!$row->store()) {
						$mainframe->enqueueMessage(JText::_('EXTENSIONS INSTALL ERROR'), 'error');
					}
				}
			}
		}*/

		if (!$install) {	
			$this->redirect();
		}
		return $ret;
	}
	/**
	 * Update Plugins table
	 * @return boolean 
	 */
	function updatePlugins()
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'plugins');		
		// Create and install Plugins tables
		if ($this->installPlugins(true)) {
			if ($this->checkTable('plugins_tmp')) {
				// Get installed plugins
				$query = 'SELECT * FROM #__jce_plugins_tmp WHERE iscore = 0';
				$db->setQuery($query);
				$plugins = $db->loadObjectList();
				
				foreach ($plugins as $plugin) {
					$row =& JTable::getInstance('plugin', 'JCETable');						
					// Pass properties to $row object
					$row->title 	= $plugin->title;
					$row->name		= $plugin->name;
					$row->icon		= $plugin->icon;
					$row->layout	= $plugin->layout;
					$row->row		= $plugin->row;
					$row->ordering	= $plugin->ordering;
					// Store
					if (!$row->store()) {
						$mainframe->enqueueMessage(JText::_('PLUGIN INSTALL ERROR '. $plugin->title), 'error');
					}
				}
				// Additional Extensions
				/*$query = 'SELECT * FROM #__jce_extensions_tmp WHERE extension != '. $db->Quote('joomlalinks');
				$db->setQuery($query);
				$extensions = $db->loadObjectList();
				if (!empty($extensions)) {
					foreach ($extensions as $extension) {
						$row =& JTable::getInstance('extension', 'JCETable');						
						// Pass properties to $row object
						$row->pid 		= $extension->pid;
						$row->name		= $extension->name;
						$row->extension	= $extension->extension;
						$row->folder	= $extension->folder;
						// Store
						if (!$row->store()) {
							$mainframe->enqueueMessage(JText::_('EXTENSION INSTALL ERROR '. $extension->name), 'error');
						}
					}
				}*/
			}
		}
	}
	/**
	 * Install the Editor Plugin
	 * @return boolean
	 * @param object $install[optional]
	 */
	function installEditor($install = false)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		$path 	= JPATH_PLUGINS .DS. 'editors';
		$ret 	= true;
		if ($this->checkEditorFiles()) {	
			// Sourced from various Joomla! core files including the installer plugin adapter			
			$xml =& JFactory::getXMLParser('Simple');
			$name = 'JCE Editor 1.5.x';
					
			if ($xml->loadFile($path .DS. 'jce.xml')) {
				$root =& $xml->document;	
				// Get the element of the tag names
				$name = $root->getElementByPath('name');
				$name = JFilterInput::clean($name->data(), 'string');
			}
			JTable::addIncludePath(JPATH_LIBRARIES .DS. 'joomla' .DS. 'database' .DS. 'table');
			// Get Editor id if installed
			$id = $this->checkEditor();
			$row =& JTable::getInstance('plugin');
			// Load editor if valid id
			if($id){
				$row->load($id);
			}
			$row->name 		= $name;
			$row->ordering 	= 0;
			$row->folder 	= 'editors';
			$row->iscore 	= 0;
			$row->access 	= 0;
			$row->published = 1;
			$row->client_id = 0;
			$row->element 	= 'jce';
			if (!$row->store()) {
				$mainframe->enqueueMessage(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
				$ret = false;
			}
		} else {
			$mainframe->enqueueMessage(JText::_('EDITOR FILES MISSING'), 'error');
			$ret = false;
		}
		$mainframe->enqueueMessage(JText::_('EDITOR INSTALL SUCCESS'));	
		$ret = true;
		if(!$install){
			$this->redirect();
		}else{
			return $ret;
		}
	}
	/**
	 * Uninstall the editor
	 * @return boolean
	 */	
    function removeEditor()
    {
        global $mainframe;
        $db = & JFactory::getDBO();
    
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
    
        $query = 'DELETE FROM #__plugins'
        .' WHERE folder = '.$db->Quote('editors')
        .' AND element = '.$db->Quote('jce')
        ;
    
        $db->setQuery($query);
        if (!$db->query()) {
            $msg = JText::sprintf('UNINSTALLEXT', 'Editor', JText::_('Error'));
            $ret = false;
        } else {
            $path = JPATH_PLUGINS.DS.'editors';
    
            $files = array (
            	$path.DS.'jce.php',
           	 	$path.DS.'jce.xml',
           	 	JPATH_SITE.DS.'language'.DS.'en-GB'.DS.'en-GB.plg_editors_jce.ini'
            );
    
            foreach ($files as $file) {
                if (file_exists($file)) {
                    JFile::delete($file);
                }
            }
            JFolder::delete($path.DS.'jce');
			$msg = JText::sprintf('UNINSTALLEXT', 'Editor', JText::_('Success'));
			$ret = true;
        }
		$mainframe->enqueueMessage($msg);
		return $ret;
    }
}
?>