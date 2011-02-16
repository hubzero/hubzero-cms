<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelBackupRestore extends JModel
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
	}
	
	function _buildQuery()
	{
		$query  = "SELECT FormId, FormTitle, FormName FROM #__rsform_forms WHERE 1";
		$query .= " ORDER BY `".$this->getSortColumn()."` ".$this->getSortOrder();
		
		return $query;
	}
	
	function getForms()
	{
		$option = JRequest::getVar('option', 'com_rsform');
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query);
		
		foreach ($this->_data as $i => $row)
		{
			$this->_db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE FormId='".$row->FormId."'");
			$row->_allSubmissions = $this->_db->loadResult();
		}
		
		return $this->_data;
	}
	
	function getSortColumn()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		return $mainframe->getUserStateFromRequest($option.'.forms.filter_order', 'filter_order', 'FormId', 'word');
	}
	
	function getSortOrder()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		return $mainframe->getUserStateFromRequest($option.'.forms.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
	}
	
	function getIsWritable()
	{
		return is_writable(JPATH_SITE.DS.'media');
	}
}
?>