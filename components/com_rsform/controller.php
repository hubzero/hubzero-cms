<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSFormController extends JController
{
	var $_db;
	
	function __construct()
	{
		parent::__construct();
		
		$this->_db = JFactory::getDBO();
		
		$view = JRequest::getVar('view', 'rsform');
		if ($view == 'rsform')
			$this->registerDefaultTask('showForm');
		
		$doc =& JFactory::getDocument();
		$doc->addScript(JURI::root(true).'/components/com_rsform/assets/js/script.js');
	}
	
	function captcha()
	{
		require_once(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'captcha.php');
		
		$componentId = JRequest::getInt('componentId');
		$captcha = new RSFormProCaptcha($componentId);

		$session = JFactory::getSession();
		$session->set('com_rsform.captcha.'.$componentId, $captcha->getCaptcha());
		exit();
	}
	
	function plugin()
	{
		$mainframe =& JFactory::getApplication();
		$mainframe->triggerEvent('rsfp_f_onSwitchTasks');
	}
	
	function showForm()
	{
		$mainframe =& JFactory::getApplication();
		
		if (!$formId = JRequest::getInt('formId', 0, 'get'))
		{
			$params = clone($mainframe->getParams('com_rsform'));
			$formId = $params->get('formId');
		}
		
		echo RSFormProHelper::displayForm($formId);
	}
	
	function submissionsViewFile()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_rsform', JPATH_ADMINISTRATOR);
		
		$hash = JRequest::getCmd('hash');
		if (strlen($hash) != 32)
			return $this->setRedirect('index.php');
		
		$config = JFactory::getConfig();
		$secret = $config->getValue('config.secret');
			
		$this->_db->setQuery("SELECT * FROM #__rsform_submission_values WHERE MD5(CONCAT(SubmissionId,'".$this->_db->getEscaped($secret)."',FieldName)) = '".$hash."'");
		$result = $this->_db->loadObject();
		
		// Not found
		if (empty($result))
			return $this->setRedirect('index.php');
		
		// Not an upload field
		$this->_db->setQuery("SELECT c.ComponentTypeId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId=c.ComponentId) WHERE p.PropertyName='NAME' AND p.PropertyValue='".$this->_db->getEscaped($result->FieldName)."'");
		$type = $this->_db->loadResult();
		if ($type != 9)
			return $this->setRedirect('index.php', JText::_('RSFP_VIEW_FILE_NOT_UPLOAD'));
		
		jimport('joomla.filesystem.file');
		if (JFile::exists($result->FieldValue))
			RSFormProHelper::readFile($result->FieldValue);
		
		$this->setRedirect('index.php', JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
	}
	
	function ajaxValidate()
	{
		$form = JRequest::getVar('form');
		$formId = (int) @$form['formId'];
		
		$this->_db->setQuery("SELECT ComponentId, ComponentTypeId FROM #__rsform_components WHERE `FormId`='".$formId."' AND `Published`='1' ORDER BY `Order`");
		$components = $this->_db->loadObjectList();
		
		$page = JRequest::getInt('page');
		if ($page)
		{
			$current_page = 1;
			foreach ($components as $i => $component)
			{
				if ($current_page != $page)
					unset($components[$i]);
				if ($component->ComponentTypeId == 41)
					$current_page++;
			}
		}
		
		$removeUploads   = array();
		$removeRecaptcha = array();
		$formComponents  = array();
		foreach ($components as $component)
		{
			$formComponents[] = $component->ComponentId;
			if ($component->ComponentTypeId == 9)
				$removeUploads[] = $component->ComponentId;
			if ($component->ComponentTypeId == 24)
				$removeRecaptcha[] = $component->ComponentId;
		}
		
		echo implode(',', $formComponents);
		
		echo "\n";
		
		$invalid = RSFormProHelper::validateForm($formId);
		if (count($invalid))
		{
			foreach ($invalid as $i => $componentId)
				if (in_array($componentId, $removeUploads) || in_array($componentId, $removeRecaptcha))
					unset($invalid[$i]);
			
			$invalidComponents = array_intersect($formComponents, $invalid);
			
			echo implode(',', $invalidComponents);
		}
		
		if (isset($invalidComponents))
		{
			echo "\n";
			
			$pages = RSFormProHelper::componentExists($formId, 41);
			$pages = count($pages);
			
			if ($pages && !$page)
			{
				$first = reset($invalidComponents);
				$current_page = 1;
				foreach ($components as $i => $component)
				{
					if ($component->ComponentId == $first)
						break;
					if ($component->ComponentTypeId == 41)
						$current_page++;
				}
				echo $current_page;
				
				echo "\n";
				
				echo $pages;
			}
		}
		
		jexit();
	}
}
?>