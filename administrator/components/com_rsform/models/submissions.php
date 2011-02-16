<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelSubmissions extends JModel
{
	var $_data = array();
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	var $firstFormId = 0;
	
	var $export = false;
	var $rows = 0;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
		$this->_query = $this->_buildQuery();
		
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($option.'.submissions.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.submissions.limitstart', 'limitstart', 0, 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState($option.'.submissions.limit', $limit);
		$this->setState($option.'.submissions.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$sortColumn = $this->getSortColumn();
		$sortOrder = $this->getSortOrder();
		$formId = $this->getFormId();
		$filter = $this->_db->getEscaped($this->getFilter());
		
		// Order by static headers
		if (in_array($sortColumn, $this->getStaticHeaders()))
		{
			$query  = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(sv.SubmissionId), s.* FROM #__rsform_submissions s";
			$query .= " LEFT JOIN #__rsform_submission_values sv ON (s.SubmissionId=sv.SubmissionId)";
			$query .= " WHERE s.FormId='".$formId."'";
			
			// Only for export - export selected rows
			if ($this->export && !empty($this->rows))
				$query .= " AND s.SubmissionId IN (".implode(",", $this->rows).")";
			
			// Check if there's a filter (search) set
			if (!$this->export)
			{
				if ($filter)
				{
					$query .= " AND (sv.FieldValue LIKE '%".$filter."%'";
					$query .= " OR s.DateSubmitted LIKE '%".$filter."%'";
					$query .= " OR s.Username LIKE '%".$filter."%'";
					$query .= " OR s.UserIp LIKE '%".$filter."%')";
				}
				
				$from = $this->getDateFrom();				
				if ($from)
					$query .= " AND s.DateSubmitted >= '".$this->_db->getEscaped($from)."'";
				
				$to = $this->getDateTo();
				if ($to)
					$query .= " AND s.DateSubmitted <= '".$this->_db->getEscaped($to)."'";
			}
			$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
		}
		// Order by dynamic headers (form fields)
		else
		{
			$query  = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(sv.SubmissionId), s.* FROM #__rsform_submissions s";
			$query .= " LEFT JOIN #__rsform_submission_values sv ON (s.SubmissionId=sv.SubmissionId)";
			$query .= " WHERE s.FormId='".$formId."'";
			
			// Only for export - export selected rows
			if ($this->export && !empty($this->rows))
				$query .= " AND s.SubmissionId IN (".implode(",", $this->rows).")";
			
			// Check if there's a filter (search) set
			if (!$this->export)
			{
				if ($filter)
				{
					$query .= " AND (s.DateSubmitted LIKE '%".$filter."%'";
					$query .= " OR s.Username LIKE '%".$filter."%'";
					$query .= " OR s.UserIp LIKE '%".$filter."%'";
					$query .= " OR s.SubmissionId IN (SELECT DISTINCT(SubmissionId) FROM #__rsform_submission_values WHERE FieldValue LIKE '%".$filter."%'))";
				}
				
				$from = $this->getDateFrom();				
				if ($from)
					$query .= " AND s.DateSubmitted >= '".$this->_db->getEscaped($from)."'";
				
				$to = $this->getDateTo();
				if ($to)
					$query .= " AND s.DateSubmitted <= '".$this->_db->getEscaped($to)."'";
			}
			
			if ($this->checkOrderingPossible($sortColumn))
				$query .= " AND sv.FieldName='".$sortColumn."'";
				
			$query .= " ORDER BY `FieldValue` ".$sortOrder;
		}
		
		return $query;
	}
	
	function checkOrderingPossible($field)
	{
		$formId = $this->getFormId();
		$this->_db->setQuery("SELECT SubmissionValueId FROM #__rsform_submission_values WHERE FieldName='".$this->_db->getEscaped($field)."' AND FormId='".$formId."'");
		return $this->_db->loadResult();
	}
	
	function getDateFrom()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		return $mainframe->getUserStateFromRequest($option.'.submissions.dateFrom', 'dateFrom');
	}
	
	function getDateTo()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		return $mainframe->getUserStateFromRequest($option.'.submissions.dateTo', 'dateTo');
	}
	
	function getSubmissions()
	{
		$option = JRequest::getVar('option', 'com_rsform');
		
		if (empty($this->_data))
		{
			$formId = $this->getFormId();
			
			$this->_db->setQuery("SELECT MultipleSeparator, TextareaNewLines FROM #__rsform_forms WHERE FormId='".$formId."'");
			$form = $this->_db->loadObject();
			if (empty($form))
				return $this->_data;
			
			$this->_db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyName, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$formId."' AND c.Published='1' AND p.PropertyName IN ('NAME', 'WYSIWYG')");
			$components = $this->_db->loadObjectList();			
			$uploadFields 	= array();
			$multipleFields = array();
			$textareaFields = array();
			
			foreach ($components as $component)
			{
				// Upload fields
				if ($component->ComponentTypeId == 9)
				{
					$uploadFields[] = $component->PropertyValue;
				}
				// Multiple fields
				elseif (in_array($component->ComponentTypeId, array(3, 4)))
				{
					$multipleFields[] = $component->PropertyValue;
				}
				// Textarea fields
				elseif ($component->ComponentTypeId == 2)
				{
					if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
						$textareaFields[] = $component->ComponentId;
				}
			}
			if (!empty($textareaFields))
			{
				$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentId IN (".implode(',', $textareaFields).")");
				$textareaFields = $this->_db->loadResultArray();
			}
			
			$this->_db->setQuery("SET SQL_BIG_SELECTS=1");
			$this->_db->query();
			
			$submissionIds = array();
			
			$results = $this->_getList($this->_query, $this->getState($option.'.submissions.limitstart'), $this->getState($option.'.submissions.limit'));
			$this->_db->setQuery("SELECT FOUND_ROWS();");
			$this->_total = $this->_db->loadResult();
			foreach ($results as $result)
			{
				$submissionIds[] = $result->SubmissionId;
				
				$this->_data[$result->SubmissionId]['FormId'] = $result->FormId;
				$this->_data[$result->SubmissionId]['DateSubmitted'] = $result->DateSubmitted;
				$this->_data[$result->SubmissionId]['UserIp'] = $result->UserIp;
				$this->_data[$result->SubmissionId]['Username'] = $result->Username;
				$this->_data[$result->SubmissionId]['UserId'] = $result->UserId;
				$this->_data[$result->SubmissionId]['SubmissionValues'] = array();
			}
			
			if (!empty($submissionIds))
			{
				$layout = JRequest::getVar('layout');
				$view = JRequest::getVar('view');
				$must_escape = $view == 'submissions' && $layout == 'default';
				
				$results = $this->_getList("SELECT * FROM `#__rsform_submission_values` WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
				foreach ($results as $result)
				{
					// Check if this is an upload field
					if (in_array($result->FieldName, $uploadFields) && !empty($result->FieldValue) && !$this->export)
						$result->FieldValue = '<a href="index.php?option=com_rsform&amp;task=submissions.view.file&amp;id='.$result->SubmissionValueId.'">'.basename($result->FieldValue).'</a>';
					else
					{
						// Check if this is a multiple field
						if (in_array($result->FieldName, $multipleFields))
							$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
						// Transform new lines
						elseif ($form->TextareaNewLines && in_array($result->FieldName, $textareaFields))
						{
							if ($must_escape)
								$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
							$result->FieldValue = nl2br($result->FieldValue);
						}
						// PayPal status
						elseif ($result->FieldName == '_STATUS')
							$result->FieldValue = JText::_('RSFP_PAYPAL_STATUS_'.$result->FieldValue);
						else
						{
							if ($must_escape)
								$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
						}
					}
						
					$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName] = array('Value' => $result->FieldValue, 'Id' => $result->SubmissionValueId);
				}
			}
			unset($results);
		}
		
		return $this->_data;
	}
	
	function getHeaders()
	{
		$query  = "SELECT p.PropertyValue FROM #__rsform_components c";
		$query .= " LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId AND p.PropertyName='NAME')";
		$query .= " LEFT JOIN #__rsform_component_types ct ON (c.ComponentTypeId=ct.ComponentTypeId)";
		$query .= " WHERE c.FormId='".$this->getFormId()."' AND c.Published='1'";
		
		$task = strtolower(JRequest::getWord('task'));
		if (strpos('submissionsexport', $task) !== false)
			$query .= " AND ct.ComponentTypeName NOT IN ('button', 'captcha', 'freeText', 'imageButton', 'submitButton')";
			
		$query .= " ORDER BY c.Order";
		
		$this->_db->setQuery($query);
		$headers = $this->_db->loadResultArray();
		
		// PayPal
		$this->_db->setQuery("SELECT SubmissionValueId FROM #__rsform_submission_values WHERE FormId='".$this->getFormId()."' AND FieldName='_STATUS' LIMIT 1");
		if ($this->_db->loadResult())
			$headers[] = '_STATUS';
		
		return $headers;
	}
	
	function getHeadersEnabled()
	{
		$return = new stdClass();
		
		$formId = $this->getFormId();
		
		$this->_db->setQuery("SELECT ColumnName FROM #__rsform_submission_columns WHERE FormId='".$formId."' AND ColumnStatic='1'");
		$return->staticHeaders = $this->_db->loadResultArray();
		
		$this->_db->setQuery("SELECT ColumnName FROM #__rsform_submission_columns WHERE FormId='".$formId."' AND ColumnStatic='0'");
		$return->headers = $this->_db->loadResultArray();
		
		return $return;
	}
	
	function getStaticHeaders()
	{
		$return = array('DateSubmitted', 'UserIp', 'Username', 'UserId');
		
		return $return;
	}
	
	function getTotal()
	{		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			$option = JRequest::getVar('option', 'com_rsform');
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState($option.'.submissions.limitstart'), $this->getState($option.'.submissions.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getFormTitle()
	{
		$formId = $this->getFormId();
		
		$this->_db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId='".$formId."'");
		return $this->_db->loadResult();
	}
	
	function getForms()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		$return = array();
		$sortColumn = $mainframe->getUserState($option.'.forms.filter_order');
		if (empty($sortColumn))
			$sortColumn = 'FormId';
		$sortOrder  = $mainframe->getUserState($option.'.forms.filter_order_Dir');
		if (empty($sortOrder))
			$sortOrder = 'DESC';
		
		$query  = "SELECT FormId, FormTitle FROM #__rsform_forms WHERE 1";
		$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
		
		$results = $this->_getList($query);
		foreach ($results as $result)
			$return[] = JHTML::_('select.option', $result->FormId, $result->FormTitle);
		
		if (!empty($results[0]->FormId))
			$this->firstFormId = $results[0]->FormId;
		
		return $return;
	}
	
	function getSortColumn()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		return $mainframe->getUserStateFromRequest($option.'.submissions.filter_order', 'filter_order', 'DateSubmitted', 'string');
	}
	
	function getSortOrder()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		return $mainframe->getUserStateFromRequest($option.'.submissions.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
	}
	
	function getFilter()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		return $mainframe->getUserStateFromRequest($option.'.submissions.filter', 'search', '');
	}
	
	function getFormId()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		if (empty($this->firstFormId))
			$this->getForms();
		
		$formId = $mainframe->getUserStateFromRequest($option.'.submissions.formId', 'formId', $this->firstFormId, 'int');
		if ($formId)
		{
			$this->_db->setQuery("SELECT FormId FROM #__rsform_forms WHERE FormId='".$formId."'");
			if (!$this->_db->loadResult())
			{
				$formId = $this->firstFormId;
				$mainframe->setUserState($option.'.submissions.formId', $formId);
			}
		}
		
		return $formId;
	}
	
	// If $cid is array, it will treat it as a collection of SubmissionIds
	// If $cid is not an array, it will treat it as the FormId on which to clear all submission files
	function deleteSubmissions($cid)
	{
		if (is_array($cid) && count($cid))
		{
			$this->_db->setQuery("DELETE FROM #__rsform_submissions WHERE SubmissionId IN (".implode(',', $cid).")");
			$this->_db->query();
			$total = $this->_db->getAffectedRows();
			
			$this->_db->setQuery("DELETE FROM #__rsform_submission_values WHERE SubmissionId IN (".implode(',', $cid).")");
			$this->_db->query();
		}
		else
		{
			$this->_db->setQuery("DELETE FROM #__rsform_submissions WHERE FormId='".$cid."'");
			$this->_db->query();
			$total = $this->_db->getAffectedRows();
		
			$this->_db->setQuery("DELETE FROM #__rsform_submission_values WHERE FormId='".$cid."'");
			$this->_db->query();
		}
		
		return $total;
	}
	
	// If $cid is array, it will treat it as a collection of SubmissionIds
	// If $cid is not an array, it will treat it as the FormId on which to clear all submission files
	function deleteSubmissionFiles($cid)
	{
		jimport('joomla.filesystem.file');
		
		// If it's an array, we need to delete the submission files based on the SubmissionIds provided
		if (is_array($cid) && count($cid))
		{
			$this->_db->setQuery("SELECT DISTINCT(FormId) FROM #__rsform_submissions WHERE SubmissionId IN (".implode(',', $cid).")");
			$formIds = $this->_db->loadResultArray();
			
			$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId = p.ComponentId) WHERE c.FormId IN (".implode(',', $formIds).") AND c.ComponentTypeId='9' AND p.PropertyName='NAME'");
			$fields = $this->_db->loadResultArray();
			
			foreach ($fields as $field)
			{
				$this->_db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE SubmissionId IN (".implode(',', $cid).") AND FieldName='".$this->_db->getEscaped($field)."'");
				$files = $this->_db->loadResultArray();
				if (!empty($files))
					JFile::delete($files);
			}
		}
		// We've provided a form Id and need to delete all its submissions
		elseif (is_numeric($cid))
		{
			jimport('joomla.filesystem.file');
			$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId = p.ComponentId) WHERE c.FormId='".$cid."' AND c.ComponentTypeId='9' AND p.PropertyName='NAME'");
			$fields = $this->_db->loadResultArray();
			foreach ($fields as $field)
			{
				$this->_db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE FormId='".$cid."' AND FieldName='".$this->_db->getEscaped($field)."'");
				$files = $this->_db->loadResultArray();
				if (!empty($files))
					JFile::delete($files);
			}
		}
	}
	
	function getSubmissionId()
	{
		$cid = JRequest::getVar('cid', array());
		if (is_array($cid))
			$cid = (int) @$cid[0];
		
		return $cid;
	}
	
	function getEditFields()
	{
		$isPDF = JRequest::getVar('format') == 'pdf';
		
		$cid = $this->getSubmissionId();
		
		$return = array();
		
		$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$cid."'");
		$submission = $this->_db->loadObject();
		
		if (empty($submission))
		{
			$mainframe =& JFactory::getApplication();
			$mainframe->redirect('index.php?option=com_rsform&task=submissions.manage');
			return $return;
		}
		
		if ($isPDF)
		{
			$this->_db->setQuery("SELECT MultipleSeparator, TextareaNewLines FROM #__rsform_forms WHERE FormId='".$submission->FormId."'");
			$form = $this->_db->loadObject();
			$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);
		}
		
		$this->_db->setQuery("SELECT FieldName, FieldValue FROM #__rsform_submission_values WHERE SubmissionId='".$cid."'");
		$fields = $this->_db->loadObjectList();
		foreach ($fields as $field)
			$submission->values[$field->FieldName] = $field->FieldValue;
		unset($fields);
		
		$this->_db->setQuery("SELECT p.PropertyValue, ct.ComponentTypeName, c.ComponentId FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (p.ComponentId=c.ComponentId) LEFT JOIN #__rsform_component_types ct ON (c.ComponentTypeId=ct.ComponentTypeId) WHERE c.FormId='".$submission->FormId."' AND c.Published='1' AND p.PropertyName='NAME' ORDER BY `Order`");
		$fields = $this->_db->loadObjectList();
		if (empty($fields))
			return $return;
		
		$componentIds = array();
		foreach ($fields as $field)
			$componentIds[] = $field->ComponentId;
		
		$properties = RSFormProHelper::getComponentProperties($componentIds);
		
		foreach ($fields as $field)
		{
			$data = $properties[$field->ComponentId];
			
			$new_field = array();
			$new_field[0] = $field->PropertyValue;
			
			$name = $field->PropertyValue;
			$value = isset($submission->values[$field->PropertyValue]) ? $submission->values[$field->PropertyValue] : '';
			
			switch ($field->ComponentTypeName)
			{
				// skip this field for now, no need to edit it
				case 'freeText':
					continue 2;
				break;
				
				default:
					if ($isPDF)
						$new_field[1] = RSFormProHelper::htmlEscape($value);
					else
						$new_field[1] = '<input class="inputbox" size="105" type="text" name="form['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
				break;
				
				case 'textArea':
					if ($isPDF)
					{
						if (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
							$value = $value;
						elseif ($form->TextareaNewLines)
							$value = nl2br(RSFormProHelper::htmlEscape($value));
						$new_field[1] = $value;
					}
					elseif (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
						$new_field[1] = RSFormProHelper::WYSIWYG('form['.$name.']', RSFormProHelper::htmlEscape($value), '', 600, 100, 60, 10);
					else
						$new_field[1] = '<textarea rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
				break;
				
				case 'selectList':
					if ($isPDF)
					{
						$new_field[1] = str_replace("\n", $form->MultipleSeparator, $value);
						break;
					}
					$value = RSFormProHelper::explode($value);
					
					$items = RSFormProHelper::isCode($data['ITEMS']);
					$items = RSFormProHelper::explode($items);
					
					$options = array();
					foreach($items as $item)
					{
						// <OPTGROUP>
						if(preg_match('/\[g\]/',$item))
						{
							$item = str_replace('[g]', '', $item);
							$options[] = JHTML::_('select.optgroup', $item);
							continue;
						}
						
						// </OPTGROUP>
						if(preg_match('/\[\/g\]/',$item))
						{
							$optgroup = new stdClass();
							$optgroup->value = '</OPTGROUP>';
							$optgroup->text = '';
							$options[] = $optgroup;
							continue;
						}
						
						$buf = explode('|',$item);
						
						$val = str_replace('[c]', '', $buf[0]);
						$item = str_replace('[c]', '', count($buf) == 1 ? $buf[0] : $buf[1]);
						$options[] = JHTML::_('select.option', $val, $item);
					}
					
					$attribs = array();
					if ((int) $data['SIZE'] > 0)
						$attribs[] = 'size="'.(int) $data['SIZE'].'"';
					if ($data['MULTIPLE'] == 'YES')
						$attribs[] = 'multiple="multiple"';
					$attribs = implode(' ', $attribs);
					
					$new_field[1] = JHTML::_('select.genericlist', $options, 'form['.$name.'][]', $attribs, 'value', 'text', $value);
				break;
				
				case 'checkboxGroup':
					if ($isPDF)
					{
						$new_field[1] = str_replace("\n", $form->MultipleSeparator, $value);
						break;
					}
					$value = RSFormProHelper::explode($value);
					
					$items = RSFormProHelper::isCode($data['ITEMS']);
					$items = RSFormProHelper::explode($items);
					
					$new_field[1] = '';
					
					$i=0;
					foreach($items as $item)
					{
						$buf = explode('|',$item);
						
						$val = str_replace('[c]', '', $buf[0]);
						$item = str_replace('[c]', '', count($buf) == 1 ? $buf[0] : $buf[1]);
						
						$checked = '';
						if (in_array($val, $value))
							$checked = 'checked="checked"';
						
						$new_field[1] .= '<input '.$checked.' name="form['.$name.'][]" type="checkbox" value="'.RSFormProHelper::htmlEscape($val).'" id="'.$name.$i.'" /><label for="'.$name.$i.'">'.$item.'</label>';
						
						if ($data['FLOW'] == 'VERTICAL')
							$new_field[1] .='<br/>';
						$i++;
					}
				break;
				
				case 'radioGroup':
					if ($isPDF)
					{
						$new_field[1] = str_replace("\n", $form->MultipleSeparator, $value);
						break;
					}
					$value = RSFormProHelper::explode($value);
					
					$items = RSFormProHelper::isCode($data['ITEMS']);
					$items = RSFormProHelper::explode($items);
					
					$new_field[1] = '';
					
					$i=0;
					foreach($items as $item)
					{
						$buf = explode('|',$item);
						
						$val = str_replace('[c]', '', $buf[0]);
						$item = str_replace('[c]', '', count($buf) == 1 ? $buf[0] : $buf[1]);
						
						$checked = '';
						if (in_array($val, $value))
							$checked = 'checked="checked"';
						
						$new_field[1] .= '<input '.$checked.' name="form['.$name.']" type="radio" value="'.RSFormProHelper::htmlEscape($val).'" id="'.$name.$i.'" /><label for="'.$name.$i.'">'.$item.'</label>';
						
						if ($data['FLOW'] == 'VERTICAL')
							$new_field[1] .='<br/>';
						$i++;
					}
				break;
				
				case 'fileUpload':
					if ($isPDF)
					{
						$new_field[1] = $value;
						break;
					}
					$new_field[1]  = '<input class="inputbox" size="105" type="text" name="form['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
					$new_field[1] .= '<br /><input size="45" type="file" name="upload['.$name.']" />';
				break;
			}
			
			$return[] = $new_field;
		}
		
		// PayPal
		if (isset($submission->values['_STATUS']))
		{
			$name = '_STATUS';
			$value = $submission->values['_STATUS'];
			
			$new_field[0] = JText::_('RSFP_PAYPAL_STATUS');
			
			$options = array(
				JHTML::_('select.option', -1, JText::_('RSFP_PAYPAL_STATUS_-1')),
				JHTML::_('select.option', 0, JText::_('RSFP_PAYPAL_STATUS_0')),
				JHTML::_('select.option', 1, JText::_('RSFP_PAYPAL_STATUS_1'))
			);
			$new_field[1] = JHTML::_('select.genericlist', $options, 'form['.$name.'][]', null, 'value', 'text', $value);
			
			$return[] = $new_field;
		}
		
		return $return;
	}
	
	function save()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$cid    = $this->getSubmissionId();
		$form   = JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$static  = JRequest::getVar('formStatic', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$formId = JRequest::getInt('formId');
		$files  = JRequest::getVar('upload', array(), 'files', 'none', JREQUEST_ALLOWRAW);
		
		// Handle file uploads first
		if (!empty($files['error']))
		foreach ($files['error'] as $field => $error)
		{
			if ($error)
				continue;
				
			$this->_db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE FieldName='".$this->_db->getEscaped($field)."' AND SubmissionId='".$cid."' LIMIT 1");
			$original = $this->_db->loadResult();
			
			// already uploaded
			if (!empty($form[$field]))
			{
				// Path has changed, remove the original file to save up space
				if ($original != $form[$field] && JFile::exists($original))
					JFile::delete($original);
			
				if (JFolder::exists(dirname($form[$field])))
					JFile::upload($files['tmp_name'][$field], $form[$field]);
			}
			// first upload
			else
			{
				$this->_db->setQuery("SELECT c.ComponentId FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' AND p.PropertyValue='".$this->_db->getEscaped($field)."'");
				$componentId = $this->_db->loadResult();
				if ($componentId)
				{
					$data = RSFormProHelper::getComponentProperties($componentId);
					// Prefix
					$prefix = uniqid('').'-';
					if (isset($data['PREFIX']) && strlen(trim($data['PREFIX'])) > 0)
						$prefix = RSFormProHelper::isCode($data['PREFIX']);
						
					// Filename
					$file = $realpath.$prefix.$files['form']['name'][$fieldName];
					
					if (JFolder::exists($data['DESTINATION']))
					{
						// Path
						$realpath = realpath($data['DESTINATION'].DS);
						if (substr($realpath, -1) != DS)
							$realpath .= DS;
						$path = $realpath.$prefix.'-'.$files['name'][$field];
						$form[$field] = $path;
						JFile::upload($files['tmp_name'][$field], $path);
					}
				}
			}
		}
		
		$update = array();
		foreach ($static as $field => $value)
			$update[] = "`".$this->_db->getEscaped($field)."`='".$this->_db->getEscaped($value)."'";
		
		if (!empty($update))
		{
			$this->_db->setQuery("UPDATE #__rsform_submissions SET ".implode(',', $update)." WHERE SubmissionId='".$cid."'");
			$this->_db->query();
		}
		
		// Update fields
		foreach ($form as $field => $value)
		{
			if (is_array($value))
				$value = implode("\n", $value);
				
			$this->_db->setQuery("SELECT SubmissionValueId, FieldValue FROM #__rsform_submission_values WHERE FieldName='".$this->_db->getEscaped($field)."' AND SubmissionId='".$cid."' LIMIT 1");
			$original = $this->_db->loadObject();
			if (!$original)
			{
				$this->_db->setQuery("INSERT INTO #__rsform_submission_values SET FormId='".$formId."', SubmissionId='".$cid."', FieldName='".$this->_db->getEscaped($field)."', FieldValue='".$this->_db->getEscaped($value)."'");
				$this->_db->query();
			}
			else
			{
				// Update only if we've changed something
				if ($original->FieldValue != $value)
				{
					// Check if this is an upload field
					if (isset($files['error'][$field]) && JFile::exists($original->FieldValue))
					{
						// Move the file to the new location
						if (!empty($value) && JFolder::exists(dirname($value)))
							JFile::move($original->FieldValue, $value);
						// Delete the file if we've chosen to delete it
						elseif (empty($value))
							JFile::delete($original->FieldValue);
					}
						
					$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->getEscaped($value)."' WHERE SubmissionValueId='".$original->SubmissionValueId."' LIMIT 1");
					$this->_db->query();
				}
			}
		}
		
		// Checkboxes don't send a value if nothing is checked
		$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentTypeId='4' AND p.PropertyName='NAME' AND c.FormId='".$formId."'");
		$checkboxes = $this->_db->loadResultArray();
		foreach ($checkboxes as $checkbox)
		{
			$value = isset($form[$checkbox]) ? $form[$checkbox] : '';
			if (is_array($value))
				$value = implode("\n", $value);
				
			$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->getEscaped($value)."' WHERE FieldName='".$this->_db->getEscaped($checkbox)."' AND FormId='".$formId."' AND SubmissionId='".$cid."' LIMIT 1");			
			$this->_db->query();
		}
	}
	
	function getSubmissionFormId()
	{
		$cid = $this->getSubmissionId();
		$this->_db->setQuery("SELECT FormId FROM #__rsform_submissions WHERE SubmissionId='".$cid."' LIMIT 1");
		return $this->_db->loadResult();
	}
	
	function getExportSelected()
	{
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		
		return $cid;
	}
	
	function getExportFile()
	{
		return uniqid('');
	}
	
	function getStaticFields()
	{
		$submissionid = $this->getSubmissionId();
		
		$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$submissionid."'");
		return $this->_db->loadObject();
	}
	
	function getExportType()
	{
		$task = JRequest::getCmd('task');
		$task = explode('.', $task);
		return end($task);
	}
	
	function getExportTotal()
	{
		$formId = $this->getFormId();
		
		$ExportRows = JRequest::getVar('ExportRows');
		if (empty($ExportRows))
		{
			$this->_db->setQuery("SELECT COUNT(SubmissionId) FROM #__rsform_submissions WHERE FormId='".$formId."'");
			return $this->_db->loadResult();
		}
		
		$ExportRows = explode(',', $ExportRows);
		return count($ExportRows);
	}
}