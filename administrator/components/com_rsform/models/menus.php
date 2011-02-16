<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelMenus extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		$this->_query = $this->_buildQuery();
		
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($option.'.menus.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.menus.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.menus.limit', $limit);
		$this->setState($option.'.menus.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$query  = "SELECT * FROM #__menu_types ORDER BY `menutype` ASC";
		
		return $query;
	}
	
	function getMenus()
	{
		$option = JRequest::getVar('option', 'com_rsform');
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.menus.limitstart'), $this->getState($option.'.menus.limit'));
		
		return $this->_data;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getListCount($this->_query); 
		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = JRequest::getVar('option', 'com_rsform');
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.menus.limitstart'), $this->getState($option.'.menus.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getFormTitle()
	{
		$formId = JRequest::getInt('formId');
		
		$this->_db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId='".$formId."'");
		return $this->_db->loadResult();
	}
}