<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProRestore
{
	var $archive = '';
	var $filetype = 'rsformbackup';
	var $overwrite = 0;
	var $cleanup = 1;
	var $installDir = '';
	var $installFile = '';
	
	var $version = '';
	var $revision = '';
	
	var $dbprefix = 'jos_';
	
	var $removeColumns = array();
	
	function RSFormProRestore($options = array())
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$config = JFactory::getConfig();
		$this->dbprefix = $config->getValue('config.dbprefix');
		
		if (isset($options['filename']))
			$this->archive = $options['filename'];
		if (isset($options['filetype']))
			$this->filetype = $options['filetype'];
		if (isset($options['overwrite']))
			$this->overwrite = (int) $options['overwrite'];
		if (isset($options['cleanup']))
			$this->cleanup = (int) $options['cleanup'];
	}
	
	function process()
	{
		$this->installDir = JPATH_SITE.DS.'media'.DS.uniqid('rsinstall_');
		
		$adapter =& JArchive::getAdapter('zip');
		if (!$adapter->extract($this->archive, $this->installDir))
			return false;
		
		return true;
	}
	
	function restore()
	{
		$this->installFile = $this->installDir.DS.'install.xml';
		if (!JFile::exists($this->installFile))
		{	
			$this->installFile = '';
			$this->cleanUp();
			JError::raiseWarning(500, JText::_('RSFP_RESTORE_NOINSTALL'));
			return false;
		}
		
		$xml = JFactory::getXMLParser('Simple');
		if (!$xml->loadFile($this->installFile))
		{
			$this->cleanUp();
			JError::raiseWarning(500, JText::_('RSFP_RESTORE_BADFILE'));
			return false;
		}
		
		$root = $xml->document;
		$attr = $root->attributes();
		$name = $root->name();
		if ($name != 'rsinstall' || $attr['type'] != 'rsformbackup')
		{
			$this->cleanUp();
			JError::raiseWarning(500, JText::_('RSFP_RESTORE_BADFILE'));
			return false;
		}
		
		$version = $root->getElementByPath('version');
		$this->version = $version->data();
		
		$revision = $root->getElementByPath('revision');
		if ($revision)
			$this->revision = $revision->data();
		
		$tasks = $root->getElementByPath('tasks');
		$tasks = $tasks->children();
		if (!empty($tasks))
		{
			if ($this->overwrite)
			{
				$db = JFactory::getDBO();
				
				$db->setQuery("TRUNCATE TABLE #__rsform_forms");
				$db->query();
				
				$db->setQuery("TRUNCATE TABLE #__rsform_components");
				$db->query();
				
				$db->setQuery("TRUNCATE TABLE #__rsform_properties");
				$db->query();
				
				$db->setQuery("TRUNCATE TABLE #__rsform_submissions");
				$db->query();
				
				$db->setQuery("TRUNCATE TABLE #__rsform_submission_values");
				$db->query();
			}
			foreach ($tasks as $task)
				$this->processTask($task);
		}
		
		$this->cleanUp();
		return true;
	}
	
	function _addColumnToRemove($name)
	{
		if (in_array($name, $this->removeColumns))
			return true;
		
		$this->removeColumns[] = $name;
		return;
	}
	
	function cleanUp()
	{
		$db = JFactory::getDBO();
		if (count($this->removeColumns))
			foreach ($this->removeColumns as $removeColumn)
			{
				$db->setQuery("ALTER TABLE `#__rsform_forms` DROP `".$db->getEscaped($removeColumn)."`");
				$db->query();
			}
		
		if ($this->cleanup)
			JFolder::delete($this->installDir);
	}
	
	function processTask($task)
	{
		$db = JFactory::getDBO();
		
		$attr = $task->attributes();
		$type = 'query';
		if (isset($attr['type']))
			$type = $attr['type'];
		
		$value = $task->data();
		
		switch ($type)
		{
			case 'query':
				$replace = array();
				$with = array();
				
				$replace[] = '{PREFIX}';
				$with[] = $this->dbprefix;
				
				if (isset($GLOBALS['q_FormId']))
				{
					$replace[] = '{FormId}';
					$with[] = $GLOBALS['q_FormId'];
				}
				
				if (isset($GLOBALS['q_ComponentId']))
				{
					$replace[] = '{ComponentId}';
					$with[] = $GLOBALS['q_ComponentId'];
				}
				
				if (isset($GLOBALS['q_SubmissionId']))
				{
					$replace[] = '{SubmissionId}';
					$with[] = $GLOBALS['q_SubmissionId'];
				}
				
				$value = str_replace($replace, $with, $value);
				
				// Little hack to rename all uppercase tables to new lowercase format
				if (preg_match('/INSERT INTO `'.$this->dbprefix.'(\w+)`/', $value, $matches))
					$value = str_replace($matches[1], strtolower($matches[1]), $value);
				// End of hack
				
				if ($this->version != '1.2.0')
					$value = html_entity_decode($value, ENT_COMPAT, 'UTF-8');
				
				// Old version hacks
				if ($this->version == '1.2.0')
				{
					if (strpos($value, '=\\\\\\"') !== false)
						$value = str_replace('\\\\', '', $value);
				}
				
				$db->setQuery($value);
				if (!$db->query())
				{
					// Compatibility with an older version
					$pattern = "#Unknown column '(.*?)'#is";
					if (preg_match($pattern, $db->getErrorMsg(), $match) && $match[1] == 'UserEmailConfirmation')
					{
						$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `UserEmailConfirmation` TINYINT(1) NOT NULL");
						$db->query();
						
						$this->_addColumnToRemove('UserEmailConfirmation');
						
						$db->setQuery($value);
						if (!$db->query())
						{
							JError::raiseWarning(500, $db->getErrorMsg());
							return false;
						}
						return true;
					}
					
					JError::raiseWarning(500, $db->getErrorMsg());
					return false;
				}
			break;
			
			case 'eval':
				if (strpos($value, '$GLOBALS[\'q_ComponentId\'] = mysql_insert_id();') !== false
				|| strpos($value, '$GLOBALS[\'q_SubmissionId\'] = mysql_insert_id();') !== false
				|| strpos($value, '$GLOBALS[\'q_FormId\'] = mysql_insert_id();') !== false)
					$value = str_replace('mysql_insert_id','$db->insertid',$value);
				
				eval($value);
			break;
		}
		
		return true;
	}
}
?>