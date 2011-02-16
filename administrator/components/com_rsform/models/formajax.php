<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelFormAjax extends JModel
{	
	function __construct()
	{
		parent::__construct();
		
		$this->_db = JFactory::getDBO();
	}
	
	function getComponentFields()
	{
		$lang = JFactory::getLanguage();
		$return = array();
		$data = $this->getComponentData();
		
		$componentId = $this->getComponentId();
		$componentType = $this->getComponentType();
		$results = $this->_getList("SELECT * FROM #__rsform_component_type_fields WHERE ComponentTypeId='".$componentType."' ORDER BY Ordering");
		foreach ($results as $result)
		{
			$field = new stdClass();
			$field->name = $result->FieldName;
			$field->body = '';
			
			switch ($result->FieldType)
			{
				case 'textbox':
				{
					if ($lang->hasKey('RSFP_COMP_FIELD_'.$field->name))
						$field->body = JText::_('RSFP_COMP_FIELD_'.$field->name);						
					else
						$field->body = $field->name;
						
					$field->body .= '<br/>';
					
					if ($componentId > 0)
						$value = isset($data[$field->name]) ? $data[$field->name] : '';
					else
					{
						$values = RSFormProHelper::isCode($result->FieldValues);
						
						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$values))
							$value = JText::_('RSFP_COMP_FVALUE_'.$values);							
						else
							$value = $values;
					}
					
					$field->body .= '<input type="text" id="'.$field->name.'" name="param['.$field->name.']" value="'.RSFormProHelper::htmlEscape($value).'" class="wide" />';
				}
				break;

				case 'textarea':
				{
					if ($lang->hasKey('RSFP_COMP_FIELD_'.$field->name))
						$field->body = JText::_('RSFP_COMP_FIELD_'.$field->name);						
					else
						$field->body = $field->name;
					
					$field->body .= '<br />';
					
					if ($componentId > 0)
					{
						if (!isset($data[$field->name]))
							$data[$field->name] = '';
							
						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$data[$field->name]))
							$value = JText::_('RSFP_COMP_FVALUE_'.$data[$field->name]);
						else
							$value = $data[$field->name];
					}
					else
					{
						$values = RSFormProHelper::isCode($result->FieldValues);
						
						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$values))
							$value = JText::_('RSFP_COMP_FVALUE_'.$values);							
						else
							$value = $values;
					}
					
					$field->body .= '<textarea id="'.$field->name.'" name="param['.$field->name.']" rows="5" cols="20" class="wide">'.RSFormProHelper::htmlEscape($value).'</textarea></td>';
				}
				break;
				
				case 'select':
				{
					if ($lang->hasKey('RSFP_COMP_FIELD_'.$field->name))
						$field->body = JText::_('RSFP_COMP_FIELD_'.$field->name);						
					else
						$field->body = $field->name;
					
					$field->body .= '<br />';
					
					$field->body .= '<select name="param['.$field->name.']" id="'.$field->name.'" onchange="changeValidation(this);">';
					
					if (!isset($data[$field->name]))
						$data[$field->name] = '';
					
					$result->FieldValues = str_replace("\r", '', $result->FieldValues);
					$items = RSFormProHelper::isCode($result->FieldValues);
					$items = explode("\n",$items);
					foreach($items as $item)
					{
						$buf = explode('|',$item);
						
						$option_value = $buf[0];
						$option_shown = count($buf) == 1 ? $buf[0] : $buf[1];
						
						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$option_shown))
							$label = JText::_('RSFP_COMP_FVALUE_'.$option_shown);
						else
							$label = $option_shown;

						$field->body .= '<option '.($componentId > 0 && $data[$field->name] == $option_value ? 'selected="selected"' : '').' value="'.$option_value.'">'.RSFormProHelper::htmlEscape($label).'</option>';
					}
					$field->body .= '</select>';	
				}
				break;
				
				case 'hidden':
				{
					$values = $result->FieldValues;
					if (defined('RSFP_COMP_FVALUE_'.$values))
						$value = constant('RSFP_COMP_FVALUE_'.$values);
					else
						$value = $values;
						
					$field->body = '<input type="hidden" id="'.$field->name.'" name="'.$field->name.'" value="'.RSFormProHelper::htmlEscape($value).'" />';
				}
				break;
				
				case 'hiddenparam':
					$field->body = '<input type="hidden" id="'.$field->name.'" name="param['.$field->name.']" value="'.RSFormProHelper::htmlEscape($result->FieldValues).'" />';
				break;
			}
			
			$return[] = $field;
		}
		
		return $return;
	}
	
	function getComponentData()
	{
		$componentId = $this->getComponentId();
		
		$data = array();
		if ($componentId > 0)
			$data = RSFormProHelper::getComponentProperties($componentId);
		
		return $data;
	}
	
	function getComponentType()
	{
		return JRequest::getInt('componentType');
	}
	
	function getComponentId()
	{
		$cid = JRequest::getInt('componentId');
		
		$cids = JRequest::getVar('cid', array());
		if (is_array($cids) && count($cids))
			$cid = $cids;
		
		return $cid;
	}
	
	function getI()
	{
		return JRequest::getInt('i');
	}
	
	function getComponent()
	{
		$componentId = $this->getComponentId();
		
		$return = new stdClass();
		$this->_db->setQuery("SELECT Published FROM #__rsform_components WHERE ComponentId='".$componentId."'");
		$return->published = $this->_db->loadResult();
		
		return $return;
	}
	
	function componentsChangeStatus()
	{
		$componentId = $this->getComponentId();
		
		$task = strtolower(JRequest::getWord('task'));
		$published = 0;
		if ($task == 'componentspublish')
			$published = 1;
		
		if (is_array($componentId))
			$this->_db->setQuery("UPDATE #__rsform_components SET Published='".$published."' WHERE ComponentId IN (".implode(',', $componentId).")");
		else
			$this->_db->setQuery("UPDATE #__rsform_components SET Published='".$published."' WHERE ComponentId='".$componentId."'");
		
		$this->_db->query();
	}
}