<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelForms extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	var $_form = null;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		$this->_query = $this->_buildQuery();
		
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($option.'.forms.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.forms.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.forms.limit', $limit);
		$this->setState($option.'.forms.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$query  = "SELECT * FROM #__rsform_forms WHERE 1";
		$query .= " ORDER BY `".$this->getSortColumn()."` ".$this->getSortOrder();
		
		return $query;
	}
	
	function getForms()
	{
		$option = JRequest::getVar('option', 'com_rsform');
		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState($option.'.forms.limitstart'), $this->getState($option.'.forms.limit'));
		
		foreach ($this->_data as $i => $row)
		{
			$this->_db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE date_format(DateSubmitted,'%Y-%m-%d') = '".date('Y-m-d')."' AND FormId='".$row->FormId."'");
			$row->_todaySubmissions = $this->_db->loadResult();

			$this->_db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE date_format(DateSubmitted,'%Y-%m') = '".date('Y-m')."' AND FormId='".$row->FormId."'");
			$row->_monthSubmissions = $this->_db->loadResult();
	
			$this->_db->setQuery("SELECT COUNT(`SubmissionId`) cnt FROM #__rsform_submissions WHERE FormId='".$row->FormId."'");
			$row->_allSubmissions = $this->_db->loadResult();
		}
		
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
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.forms.limitstart'), $this->getState($option.'.forms.limit'));
		}
		
		return $this->_pagination;
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
	
	function getHasSubmitButton()
	{
		$formId = JRequest::getInt('formId');
		
		$this->_db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' AND ComponentTypeId IN (13,12) LIMIT 1");
		return $this->_db->loadResult();
	}
	
	function getFields()
	{
		$formId = JRequest::getInt('formId');
		
		$return = array();
		
		$this->_db->setQuery("SELECT p.PropertyValue AS ComponentName, c.*, ct.ComponentTypeName FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId AND p.PropertyName='NAME') LEFT JOIN #__rsform_component_types ct ON (ct.ComponentTypeId = c.ComponentTypeId) WHERE c.FormId='".$formId."' ORDER BY c.Order");
		$components = $this->_db->loadObjectList();
		
		$properties = RSFormProHelper::getComponentProperties($components);
		
		foreach ($components as $component)
		{
			$data = $properties[$component->ComponentId];
			$data['componentId'] = $component->ComponentId;
			$data['componentTypeId'] = $component->ComponentTypeId;
			$data['ComponentTypeName'] = $component->ComponentTypeName;
			
			$field = new stdClass();
			$field->id = $component->ComponentId;
			$field->type_id = $component->ComponentTypeId;
			$field->name = $component->ComponentName;
			$field->published = $component->Published;
			$field->ordering = $component->Order;
			$field->preview = RSFormProHelper::showPreview($formId, $field->id, $data);
			$field->required = isset($data['REQUIRED']) && $data['REQUIRED'] == 'YES' ? '<b>'.JText::_($data['REQUIRED']).'</b>' : JText::_('NO');
			$field->validation = isset($data['VALIDATIONRULE']) && $data['VALIDATIONRULE'] != 'none' ? '<b>'.$data['VALIDATIONRULE'].'</b>' : 'none';
			
			$return[] = $field;
		}
		return $return;
	}
	
	function getFieldsTotal()
	{
		$formId = JRequest::getInt('formId');
		
		$this->_db->setQuery("SELECT COUNT(ComponentId) FROM #__rsform_components WHERE FormId='".$formId."'");
		return $this->_db->loadResult();
	}
	
	function getFieldsPagination()
	{
		jimport('joomla.html.pagination');
		
		$pagination	= new JPagination($this->getFieldsTotal(), 1, 0);
		// hack to show the order up icon for the first item
		$pagination->limitstart = 1;
		return $pagination;
	}
	
	function getForm()
	{
		$formId = JRequest::getInt('formId');
		
		if (empty($this->_form))
		{
			$this->_form = JTable::getInstance('RSForm_Forms', 'Table');
			$this->_form->load($formId);
		
			if ($this->_form->FormLayoutAutogenerate)
				$this->autoGenerateLayout();
			
			$this->_form->ThemeParams = new JParameter($this->_form->ThemeParams);
		}
		
		return $this->_form;
	}
	
	function autoGenerateLayout()
	{
		$formId = $this->_form->FormId;
		
		$layout = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'layouts'.DS.JFilterInput::clean($this->_form->FormLayoutName, 'path').'.php';
		if (!file_exists($layout))
			return false;
		
		$quickfields = $this->getQuickFields();
		$requiredfields = $this->getRequiredFields();
		$pagefields = $this->getPageFields();
			
		$this->_form->FormLayout = include($layout);
	}
	
	function getRequiredFields()
	{
		$formId = JRequest::getInt('formId');
		
		$names = array();
		
		$this->_db->setQuery("SELECT p.PropertyName, p.PropertyValue, c.ComponentId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId) WHERE c.FormId='".$formId."' AND (p.PropertyName='REQUIRED' OR p.PropertyName='NAME') AND c.Published='1' ORDER BY c.Order");
		$results = $this->_db->loadObjectList();
		foreach ($results as $result)
		{
			if ($result->PropertyName == 'REQUIRED' && $result->PropertyValue == 'YES')
				$names[$result->ComponentId] = $result->ComponentId;
		}
		
		$return = array();
		foreach ($results as $result)
		{
			if ($result->PropertyName == 'NAME' && isset($names[$result->ComponentId]))
			{
				$return[] = $result->PropertyValue;
				unset($names[$result->ComponentId]);
			}
		}
		
		return $return;
	}
	
	function getQuickFields()
	{
		$formId = JRequest::getInt('formId');
		
		$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId) WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' AND c.Published='1' ORDER BY c.Order");
		
		return $this->_db->loadResultArray();
	}
	
	function getPageFields()
	{
		$formId = JRequest::getInt('formId');
		
		$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId) WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' AND c.Published='1' AND c.ComponentTypeId='41' ORDER BY c.Order");
		
		return $this->_db->loadResultArray();
	}
	
	function getFormList()
	{
		$return = array();
		
		$formId = JRequest::getInt('formId');
		
		$this->_db->setQuery("SELECT FormId, FormTitle FROM #__rsform_forms ORDER BY `".$this->getSortColumn()."` ".$this->getSortOrder());
		$results = $this->_db->loadObjectList();
		foreach ($results as $result)
			$return[] = JHTML::_('select.option', $result->FormId, $result->FormTitle, 'value', 'text', $result->FormId == $formId);
		
		return $return;
	}
	
	function getAdminEmail()
	{
		$user = JFactory::getUser();
		return $user->get('email');
	}
	
	function getPredefinedForms()
	{
		$return = array();
		
		$return[] = JHTML::_('select.option', '', JText::_('RSFP_PREDEFINED_BLANK_FORM'));
		
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'assets'.DS.'forms');
		foreach ($folders as $folder)
			$return[] = JHTML::_('select.option', $folder, $folder);
		
		return $return;
	}
	
	function getEditorText()
	{
		$formId = JRequest::getInt('formId');
		$opener = JRequest::getCmd('opener');
		
		$this->_db->setQuery("SELECT `".$opener."` FROM #__rsform_forms WHERE FormId='".$formId."'");
		return $this->_db->loadResult();
	}
	
	function getThemes()
	{
		jimport('joomla.filesystem.folder');
		
		$return = array();
		
		$data = new stdClass();
		$data->name = JText::_('NONE');
		$data->directory = $data->img_path = $data->version = $data->creationdate = $data->authorEmail = $data->authorUrl = $data->author = '';
		
		$return[] = $data;
		
		$dirs = JFolder::folders(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'assets'.DS.'themes', '.', false, true);
		foreach ($dirs as $i => $dir)
		{
			$data = $this->_parseXML($dir);
			if ($data)
				$return[] = $data;
		}
		
		return $return;
	}
	
	function _parseXML($dir)
	{
		// Read the file to see if it's a valid component XML file
		$xml = & JFactory::getXMLParser('Simple');

		$files = JFolder::files($dir, '\.xml');
		if (!count($files))
			return false;
		
		$file = reset($files);
		$path = $dir.DS.$file;
		
		if (!$xml->loadFile($path)) {
			unset($xml);
			return false;
		}
		
		if (!is_object($xml->document)) {
			unset($xml);
			return false;
		}
		
		$data = new stdClass();
		
		$data->directory = basename($dir);
		
		$data->img_path = '';
		$files = JFolder::files($dir, '\.jpg|\.gif|\.png');
		if (count($files))
		{
			$file = reset($files);
			$data->img_path = JURI::root().'components/com_rsform/assets/themes/'.$data->directory.'/'.$file;
		}
		
		$element = & $xml->document->name[0];
		$data->name = $element ? $element->data() : '';
		
		$element = & $xml->document->creationDate[0];
		$data->creationdate = $element ? $element->data() : JText::_('Unknown');
		
		$element = & $xml->document->author[0];
		$data->author = $element ? $element->data() : JText::_('Unknown');
		
		$element = & $xml->document->copyright[0];
		$data->copyright = $element ? $element->data() : '';
		
		$element = & $xml->document->authorEmail[0];
		$data->authorEmail = $element ? $element->data() : '';

		$element = & $xml->document->authorUrl[0];
		$data->authorUrl = $element ? $element->data() : '';

		$element = & $xml->document->version[0];
		$data->version = $element ? $element->data() : '';

		$element = & $xml->document->description[0];
		$data->description = $element ? $element->data() : '';
		
		if (isset($xml->document->css))
			for ($i=0; $i<count($xml->document->css); $i++)
			{
				$element = & $xml->document->css[$i];
				$data->css[$i] = $element ? $element->data() : '';
			}
		if (isset($xml->document->js))
			for ($i=0; $i<count($xml->document->js); $i++)
			{
				$element = & $xml->document->js[$i];
				$data->js[$i] = $element ? $element->data() : '';
			}
		
		return $data;
	}
	
	function save()
	{
		$mainframe =& JFactory::getApplication();
		
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		$post['FormId'] = $post['formId'];
		
		$form =& JTable::getInstance('RSForm_Forms', 'Table');
		unset($form->Thankyou);
		unset($form->UserEmailText);
		unset($form->AdminEmailText);
		unset($form->ErrorMessage);
		
		$params = array();
		if (!empty($post['ThemeName']))
		{
			$stylesheets = @$post['ThemeCSS'][$post['ThemeName']];
			$javascripts = @$post['ThemeJS'][$post['ThemeName']];
			
			$params[] = 'name='.$post['ThemeName'];
			if (is_array($stylesheets))
			{
				$params[] = 'num_css='.count($stylesheets);
				foreach ($stylesheets as $i => $stylesheet)
					$params[] = 'css'.$i.'='.$stylesheet;
			}
			if (is_array($javascripts))
			{
				$params[] = 'num_js='.count($javascripts);
				foreach ($javascripts as $i => $javascript)
					$params[] = 'js'.$i.'='.$javascript;
			}
		}
		$form->ThemeParams = implode("\n", $params);
		
		if (!isset($post['FormLayoutAutogenerate']))
			$post['FormLayoutAutogenerate'] = 0;
		
		if (!$form->bind($post))
		{
			JError::raiseWarning(500, $form->getError());
			return false;
		}
		
		if ($form->store())
		{
			// Trigger event
			$mainframe->triggerEvent('rsfp_onFormSave', array(&$form));
			return true;
		}
		else
		{
			JError::raiseWarning(500, $form->getError());
			return false;
		}
	}
}
?>