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
	var $_form = null;
	var $_data = array();
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	var $formId = 1;
	var $params;
	var $replacements;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		$this->_db = JFactory::getDBO();
		$this->params = $mainframe->getParams('com_rsform');
		$this->formId = $this->params->get('formId');
		
		if (!$this->params->get('enable_submissions', 0))
		{
			JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
			$mainframe->redirect(JURI::root());
			return;
		}
		
		// Get pagination request variables
		$limit		= JRequest::getVar('limit', $mainframe->getCfg('list_limit'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($option.'.submissions.'.$this->formId.'.limit', $limit);
		$this->setState($option.'.submissions.'.$this->formId.'.limitstart', $limitstart);
		
		$this->_query = $this->_buildQuery();
	}
	
	function getForm()
	{
		if (empty($this->_form))
		{
			$this->_db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId='".$this->formId."'");
			$this->_form = $this->_db->loadObject();
			
			$this->_form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $this->_form->MultipleSeparator);
		}
		
		return $this->_form;
	}
	
	function _buildQuery()
	{
		$query  = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(sv.SubmissionId), s.* FROM #__rsform_submissions s";
		$query .= " LEFT JOIN #__rsform_submission_values sv ON (s.SubmissionId=sv.SubmissionId)";
		$query .= " WHERE s.FormId='".$this->formId."'";
		
		$userId = $this->params->def('userId', 0);
		if ($userId == 'login')
		{
			$user =& JFactory::getUser();
			if ($user->get('guest'))
				$query .= " AND 1>2";
			
			$query .= " AND s.UserId='".(int) $user->get('id')."'";
		}
		elseif ($userId == 0)
		{
			// Show all submissions
		}
		else
		{
			$userId = explode(',', $userId);
			JArrayHelper::toInteger($userId);
			
			$query .= " AND s.UserId IN (".implode(',', $userId).")";
		}
		
		$query .= " ORDER BY s.DateSubmitted DESC";
		
		return $query;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsform.submissions.'.$this->formId.'.limitstart'), $this->getState('com_rsform.submissions.'.$this->formId.'.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getTotal()
	{
		return $this->_total;
	}
	
	function getSubmissions()
	{
		if (empty($this->_data))
		{
			$this->getComponents();

			$this->_db->setQuery("SET SQL_BIG_SELECTS=1");
			$this->_db->query();
			
			$submissionIds = array();
			
			$this->_db->setQuery($this->_query, $this->getState('com_rsform.submissions.'.$this->formId.'.limitstart'), $this->getState('com_rsform.submissions.'.$this->formId.'.limit'));
			$results = $this->_db->loadObjectList();
			$this->_db->setQuery("SELECT FOUND_ROWS()");
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
			
			$form = $this->getForm();
			
			if (!empty($submissionIds))
			{
				$this->_db->setQuery("SELECT * FROM `#__rsform_submission_values` WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
				$results = $this->_db->loadObjectList();
				
				$config = JFactory::getConfig();
				$secret = $config->getValue('config.secret');
				foreach ($results as $result)
				{
					// Check if this is an upload field
					if (in_array($result->FieldName, $this->uploadFields) && !empty($result->FieldValue))
					{
						$result->FilePath = $result->FieldValue;
						$result->FieldValue = '<a href="'.JURI::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($result->SubmissionId.$secret.$result->FieldName).'">'.basename($result->FieldValue).'</a>';
					}
					// Check if this is a multiple field
					elseif (in_array($result->FieldName, $this->multipleFields))
						$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
					elseif ($form->TextareaNewLines && in_array($result->FieldName, $this->textareaFields))
						$result->FieldValue = nl2br($result->FieldValue);
						
					$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName] = array('Value' => $result->FieldValue, 'Id' => $result->SubmissionValueId);
					if (in_array($result->FieldName, $this->uploadFields) && !empty($result->FieldValue))
					{
						$filepath = $result->FilePath;
						$filepath = str_replace(JPATH_SITE.DS, JURI::root(), $filepath);
						$filepath = str_replace(array('\\', '\\/', '//\\'), '/', $filepath);
						
						$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName]['Path'] = $filepath;
					}
				}
			}
			unset($results);
		}
		
		return $this->_data;
	}
	
	function getReplacements($user_id)
	{
		$config  = JFactory::getConfig();
		$user    = JFactory::getUser((int) $user_id);
		$replace = array('{global:sitename}', '{global:siteurl}', '{global:userip}', '{global:userid}', '{global:username}', '{global:email}', '{/details}', '{/detailspdf}');
		$with 	 = array($config->getValue('config.sitename'), JURI::root(), isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', $user->get('id'), $user->get('username'), $user->get('email'), '</a>', '</a>');
			
		$this->replacements = array($replace, $with);
		
		return $this->replacements;
	}
	
	function getComponents()
	{
		$this->_db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyName, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$this->formId."' AND c.Published='1' AND p.PropertyName IN ('NAME', 'WYSIWYG')");
		$components = $this->_db->loadObjectList();
		$this->uploadFields   = array();
		$this->multipleFields = array();
		$this->textareaFields = array();
		
		foreach ($components as $component)
		{
			// Upload fields
			if ($component->ComponentTypeId == 9)
			{
				$this->uploadFields[] = $component->PropertyValue;
			}
			// Multiple fields
			elseif (in_array($component->ComponentTypeId, array(3, 4)))
			{
				$this->multipleFields[] = $component->PropertyValue;
			}
			// Textarea fields
			elseif ($component->ComponentTypeId == 2)
			{
				if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
					$this->textareaFields[] = $component->ComponentId;
			}
		}
		
		if (!empty($this->textareaFields))
		{
			$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentId IN (".implode(',', $this->textareaFields).")");
			$this->textareaFields = $this->_db->loadResultArray();
		}
	}
	
	function getHeaders()
	{
		$query  = "SELECT p.PropertyValue FROM #__rsform_components c";
		$query .= " LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId AND p.PropertyName='NAME')";
		$query .= " LEFT JOIN #__rsform_component_types ct ON (c.ComponentTypeId=ct.ComponentTypeId)";
		$query .= " WHERE c.FormId='".$this->formId."' AND c.Published='1'";
		
		$this->_db->setQuery($query);
		$headers = $this->_db->loadResultArray();
		
		return $headers;
	}
	
	function getTemplate()
	{
		$mainframe =& JFactory::getApplication();
		
		$template_module      = $this->params->def('template_module', '');
		$template_formdatarow = $this->params->def('template_formdatarow', '');
		$template_formdetail  = $this->params->def('template_formdetail', '');
		$formdata = '';
		
		$has_suffix = $mainframe->getCfg('sef') && $mainframe->getCfg('sef_suffix');
		
		$layout = JRequest::getVar('layout', 'default');
		if ($layout == 'default')
		{
			$submissions = $this->getSubmissions();
			$headers = $this->getHeaders();
			$pagination = $this->getPagination();
			$i = 0;
			
			foreach ($submissions as $SubmissionId => $submission)
			{
				list($replace, $with) = $this->getReplacements($submission['UserId']);
				
				$pdf_link = JRoute::_('index.php?option=com_rsform&view=submissions&layout=view&cid='.$SubmissionId.'&format=pdf');
				if ($has_suffix)
				{
					$pdf_link .= strpos($pdf_link, '?') === false ? '?' : '&';
					$pdf_link .= 'format=pdf';
				}
				
				$replace = array_merge($replace, array('{global:date_added}', '{global:submission_id}', '{global:counter}', '{details}', '{detailspdf}'));
				$with 	 = array_merge($with, array($submission['DateSubmitted'], $SubmissionId, $pagination->getRowOffset($i), '<a href="'.JRoute::_('index.php?option=com_rsform&view=submissions&layout=view&cid='.$SubmissionId).'">', '<a href="'.$pdf_link.'">'));
				
				$replace[] = '{_STATUS:value}';
				$with[] = isset($submission['SubmissionValues']['_STATUS']) ? JText::_('RSFP_PAYPAL_STATUS_'.$submission['SubmissionValues']['_STATUS']['Value']) : '';
				
				foreach ($headers as $header)
				{
					if (!isset($submission['SubmissionValues'][$header]['Value']))
						$submission['SubmissionValues'][$header]['Value'] = '';
						
					$replace[] = '{'.$header.':value}';
					$with[] = $submission['SubmissionValues'][$header]['Value'];
					
					if (!empty($submission['SubmissionValues'][$header]['Path']))
					{
						$replace[] = '{'.$header.':path}';
						$with[] = $submission['SubmissionValues'][$header]['Path'];
					}
				}
				
				$formdata .= str_replace($replace, $with, $template_formdatarow);
				
				$i++;
			}
			
			$html  = str_replace('{formdata}', $formdata, $template_module);
		}
		else
		{
			$cid 	= JRequest::getInt('cid');
			$user   =& JFactory::getUser();
			$userId = $this->params->def('userId', 0);
			if ($userId != 'login' && $userId != 0)
			{
				$userId = explode(',', $userId);
				JArrayHelper::toInteger($userId);
			}
			
			$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$cid."'");
			$submission = $this->_db->loadObject();
			
			if (!$submission || ($submission->FormId != $this->params->get('formId')) || ($userId == 'login' && $submission->UserId != $user->get('id')) || (is_array($userId) && !in_array($user->get('id'), $userId)))
			{
				JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
				$mainframe->redirect(JURI::root());
				return;
			}
			
			$format = JRequest::getVar('format');
			
			$pdf_link = JRoute::_('index.php?option=com_rsform&view=submissions&layout=view&cid='.$cid.'&format=pdf');
			if ($has_suffix)
			{
				$pdf_link .= strpos($pdf_link, '?') === false ? '?' : '&';
				$pdf_link .= 'format=pdf';
			}
			
			list($replace, $with) = RSFormProHelper::getReplacements($cid, true);
			list($replace2, $with2) = $this->getReplacements($submission->UserId);
			$replace = array_merge($replace, $replace2, array('{global:date_added}', '{global:submission_id}', '{detailspdf}'));
			$with 	 = array_merge($with, $with2, array($submission->DateSubmitted, $cid, '<a href="'.$pdf_link.'">'));
			
			if ($format == 'pdf' && preg_match_all('#{detailspdf}(.*?){\/detailspdf}#is', $template_formdetail, $matches))
				foreach ($matches[0] as $fullmatch)
					$template_formdetail = str_replace($fullmatch, '', $template_formdetail);
			
			$html = str_replace($replace, $with, $template_formdetail);
		}
		
		return $html;
	}
}
?>