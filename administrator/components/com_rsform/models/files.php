<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

class RSFormModelFiles extends JModel
{
	var $_folder = null;
	var $_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->_db = JFactory::getDBO();
		
		if (is_dir(JRequest::getVar('folder')))
		{
			$this->_folder = JRequest::getVar('folder');
			if (substr($this->_folder, -1) == DS)
				$this->_folder = substr($this->_folder, 0, -1);
		}
		else
			$this->_folder = JPATH_SITE;
	}
	
	function getFolders()
	{
		$folders = array();
		
		$all_folders = JFolder::folders($this->_folder);
		foreach ($all_folders as $folder)
		{
			$element = new stdClass();
			$element->name = $folder;
			$element->fullpath = $this->_folder.DS.$folder;
			$folders[] = $element;
		}
		
		return $folders;
	}
	
	function getFiles()
	{
		$files = array();
		
		$all_files = JFolder::files($this->_folder);
		foreach ($all_files as $file)
		{
			$element = new stdClass();
			$element->name = $file;
			$element->fullpath = $this->_folder.DS.$file;
			$element->published = 1;
			$files[] = $element;
		}
		
		return $files;
	}
	
	function getElements()
	{		
		$elements = explode(DS, $this->_folder);
		$navigation_path = '';
		
		if(!empty($elements))
			foreach($elements as $i=>$element)
			{
				$navigation_path .= $element;
				$newelement = new stdClass();
				$newelement->name = $element;
				$newelement->fullpath = $navigation_path;
				$elements[$i] = $newelement;
				$navigation_path .= DS;
			}
		
		return $elements;
	}
	
	function getCurrent()
	{
		return $this->_folder;
	}
	
	function getPrevious()
	{
		$elements = explode(DS, $this->_folder);
		if (count($elements) > 1)
			array_pop($elements);
		return implode(DS, $elements);
	}
	
	function upload()
	{
		$files = JRequest::get('files');
		$upload = $files['upload'];
		if (!$files['error'])
			return JFile::upload($upload['tmp_name'], $this->getCurrent().DS.JFile::getName($upload['name']));
		else
			return false;
	}
	
	function getCanUpload()
	{
		return is_writable($this->_folder);
	}
	
	function getUploadFile()
	{
		$files = JRequest::get('files');
		$upload = $files['upload'];
		
		return JFile::getName($upload['name']);
	}
}
?>