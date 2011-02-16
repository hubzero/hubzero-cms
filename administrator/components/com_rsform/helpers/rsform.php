<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

//PRODUCT INFO - DO NOT CHANGE
DEFINE('_RSFORM_PRODUCT','RSform!Pro');
DEFINE('_RSFORM_VERSION','1.3.0');
DEFINE('_RSFORM_KEY','2XKJ3KS7JO');
DEFINE('_RSFORM_COPYRIGHT','&copy;2007-2011 www.rsjoomla.com');
DEFINE('_RSFORM_LICENSE','GPL Commercial License');
DEFINE('_RSFORM_AUTHOR','<a href="http://www.rsjoomla.com" target="_blank">www.rsjoomla.com</a>');
DEFINE('_RSFORM_WEBSITE','http://www.rsjoomla.com/');
if(!defined('_RSFORM_REVISION'))
	DEFINE('_RSFORM_REVISION','35');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'tables');

$cache =& JFactory::getCache('com_rsform');
$cache->clean();

// Create the Legacy adapter
$GLOBALS['RSadapter'] = RSFormProHelper::getLegacyAdapter();

// Legacy function -- RSgetValidationRules()
function RSgetValidationRules()
{
	return RSFormProHelper::getValidationRules();
}

class RSFormProHelper
{
	function isJ16()
	{
		jimport('joomla.version');
		$version = new JVersion();
		return $version->isCompatible('1.6.0');
	}
	
	function getLegacyAdapter()
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'legacy.php');
		$adapter = new RSAdapter();
		return $adapter;
	}
	
	function displayForm($formId, $is_module=false)
	{
		$mainframe =& JFactory::getApplication();
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT Published, FormTitle, MetaTitle, MetaDesc, MetaKeywords, ShowThankyou FROM #__rsform_forms WHERE FormId='".(int) $formId."'");
		$form = $db->loadObject();
		
		if (empty($form) || !$form->Published)
		{
			JError::raiseWarning(500, JText::_('_NOT_EXIST'));
			return;
		}
		
		$doc =& JFactory::getDocument();
		if (!$is_module)
		{
			if ($form->MetaDesc)
				$doc->setMetaData('description', $form->MetaDesc);
			if ($form->MetaKeywords)
				$doc->setMetaData('keywords', $form->MetaKeywords);
			if ($form->MetaTitle)
				$doc->setTitle($form->FormTitle);
		}
		
		$session =& JFactory::getSession();
		$formparams = $session->get('com_rsform.formparams.'.$formId);
		
		// Form has been processed ?
		if ($formparams && $formparams->formProcessed)
		{
			// Must show Thank You Message
			if ($form->ShowThankyou)
			{
				return RSFormProHelper::showThankYouMessage($formId);
			}
			
			// Clear
			$session->clear('com_rsform.formparams.'.$formId);
			
			// Must show small message
			$mainframe->enqueueMessage(JText::_('RSFP_THANKYOU_SMALL'));
		}
		
		// Must process form
		$post = JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		if (isset($post['formId']) && $post['formId'] == $formId)
		{
			$invalid = RSFormProHelper::processForm($formId);
			// Did not pass validation - show the form
			if ($invalid)
			{
				$mainframe->triggerEvent('rsfp_f_onBeforeShowForm');
				return RSFormProHelper::showForm($formId, $post, $invalid);
			}
		}
		
		// Default - show the form
		$mainframe->triggerEvent('rsfp_f_onBeforeShowForm');
		return RSFormProHelper::showForm($formId);
	}
	
	function WYSIWYG($name, $content, $hiddenField, $width, $height, $col, $row)
    {
    	$editor =& JFactory::getEditor();		
		$params = array('relative_urls' => '0', 'cleanup_save' => '0', 'cleanup_startup' => '0', 'cleanup_entities' => '0');
		
    	$content = $editor->display($name, $content , $width, $height, $col, $row, true, $params);
		
		if (RSFormProHelper::getConfig('global.editor'))
		{
			$doc =& JFactory::getDocument();
			$head = $doc->getHeadData();
		
			if (!empty($editor->_editor->_name))
				switch ($editor->_editor->_name)
				{
					// Hack to remove the save_callback function from TinyMCE
					// save_callback strips the current site URL from any href/src it finds
					case 'tinymce':
					$data['custom'] = str_replace('save_callback : "TinyMCE_Save",', '', $head['custom']);
					break;
					
					// Hack to automatically set relative_urls and remove_script_host to false from JCE
					case 'jce':
					if (strpos($head['script']['text/javascript'], 'relative_urls: false,') === false && strpos($head['script']['text/javascript'], 'remove_script_host: false,') === false)
					{
						preg_match('#inlinepopups_skin: "(\w+)",#i', $head['script']['text/javascript'], $matches);
						$head['script']['text/javascript'] = str_replace($matches[0], $matches[0]."\r\n\t\t\t".'relative_urls: false,'."\r\n\t\t\t".'remove_script_host: false,', $head['script']['text/javascript']);
						$data['script'] = $head['script'];
					}
					break;
				}
			
			if (!empty($data))
				$doc->setHeadData($data);
		}
		
		return $content;
    }
	
	function getValidationRules()
	{
		require_once(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'validation.php');
		$results = get_class_methods('RSFormProValidations');
		return implode("\n",$results);
	}
	
	function readConfig($force=false)
	{
		static $rsformpro_config;
		
		if (!is_object($rsformpro_config) || $force)
		{
			$rsformpro_config = new stdClass();
			
			$db =& JFactory::getDBO();
			$db->setQuery("SELECT * FROM `#__rsform_config`");
			$config = $db->loadObjectList();
			if (!empty($config))
				foreach ($config as $config_item)
					$rsformpro_config->{$config_item->SettingName} = $config_item->SettingValue;
		}
		
		return $rsformpro_config;
	}
	
	function getConfig($name = null)
	{
		$config = RSFormProHelper::readConfig();
		if ($name != null)
		{
			if (isset($config->$name))
				return $config->$name;
			else
				return false;
		}
		else
			return $config;
	}
	
	function genKeyCode()
	{
		$code = RSFormProHelper::getConfig('global.register.code');
		return md5($code._RSFORM_KEY);
	}
	
	function componentNameExists($componentName, $formId, $currentComponentId=0)
	{
		$db = JFactory::getDBO();
		
		if ($componentName == 'formId')
			return true;
		
		$componentName = $db->getEscaped($componentName);
		$formId = (int) $formId;
		$currentComponentId = (int) $currentComponentId;
		
		$query  = "SELECT c.ComponentId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId)";
		$query .= "WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' AND p.PropertyValue='".$componentName."'";
		if ($currentComponentId)
			$query .= " AND c.ComponentId != '".$currentComponentId."'";
		
		$db->setQuery($query);
		$exists = $db->loadResult();
		
		return $exists;
	}
	
	function copyComponent($sourceComponentId, $toFormId)
	{		
		$sourceComponentId = (int) $sourceComponentId;
		$toFormId = (int) $toFormId;
	
		$db = JFactory::getDBO();
		$db->setQuery("SELECT ComponentTypeId, Published FROM #__rsform_components WHERE ComponentId='".$sourceComponentId."'");
		$component = $db->loadObject();
		if (!$component)
			return false;
	
		//get max ordering
		$db->setQuery("SELECT MAX(`Order`)+1 FROM #__rsform_components WHERE FormId = '".$toFormId."'");
		$component->Order = $db->loadResult();
		
		$db->setQuery("INSERT INTO #__rsform_components SET `FormId`='".$toFormId."', `ComponentTypeId`='".$component->ComponentTypeId."', `Order`='".$component->Order."',`Published`='".$component->Published."'");
		$db->query();
		$newComponentId = $db->insertid();
		
		$db->setQuery("SELECT * FROM #__rsform_properties WHERE ComponentId='".$sourceComponentId."'");
		$properties = $db->loadObjectList();
		
		foreach ($properties as $property)
		{
			if ($property->PropertyName == 'NAME')
			{
				$property->PropertyValue .= ' copy';
				while(RSFormProHelper::componentNameExists($property->PropertyValue, $toFormId))
					$property->PropertyValue .= mt_rand(0,9);
			}
			
			$db->setQuery("INSERT INTO #__rsform_properties SET ComponentId='".$newComponentId."', PropertyName='".$db->getEscaped($property->PropertyName)."', PropertyValue='".$db->getEscaped($property->PropertyValue)."'");
			$db->query();
		}
	}
	
	function getComponentProperties($components)
	{
		$db = JFactory::getDBO();
		
		if (is_numeric($components))
		{
			$componentId = (int) $components;
		
			//load component properties
			$db->setQuery("SELECT `PropertyName`, `PropertyValue` FROM #__rsform_properties WHERE `ComponentId`='".$componentId."'");
			$properties = $db->loadObjectList();
		
			//set up data array with component properties
			$data = array();
			foreach($properties as $property)
				$data[$property->PropertyName] = $property->PropertyValue;
			$data['componentId'] = $componentId;
		
			unset($properties);
		
			return $data;
		}
		elseif (is_array($components))
		{
			$componentIds = array();
			foreach ($components as $componentId)
			{
				if (is_object($componentId) && !empty($componentId->ComponentId))
					$componentIds[] = (int) $componentId->ComponentId;
				elseif (is_array($componentId) && !empty($componentId['ComponentId']))
					$componentIds[] = (int) $componentId['ComponentId'];
				else
					$componentIds[] = (int) $componentId;
			}
			if (!empty($componentIds))
			{
				$db->setQuery("SELECT `PropertyName`, `PropertyValue`, `ComponentId` FROM #__rsform_properties WHERE `ComponentId` IN (".implode(',', $componentIds).")");
				$results = $db->loadObjectList();
				
				$all_data = array();
				foreach ($results as $result)
					$all_data[$result->ComponentId][$result->PropertyName] = $result->PropertyValue;
				
				foreach ($all_data as $componentId => $properties)
					$all_data[$componentId]['componentId'] = $componentId;
				
				return $all_data;
			}
		}
		
		return false;
	}
	
	function isCode($value)
	{
		$RSadapter = RSFormProHelper::getLegacyAdapter();
		
		if (preg_match('/<code>/',$value))
			return eval($value);
		
		return $value;
	}
	
	function showPreview($formId, $componentId, $data)
	{
		$mainframe =& JFactory::getApplication();
		
		$formId = (int) $formId;
		$componentId = (int) $componentId;
		
		// Legacy
		$r = array();
		$r['ComponentTypeName'] = $data['ComponentTypeName'];
		
		$out ='';
		
		//Trigger Event - rsfp_bk_onBeforeCreateComponentPreview
		$mainframe->triggerEvent('rsfp_bk_onBeforeCreateComponentPreview',array(array('out'=>&$out,'formId'=>$formId,'componentId'=>$componentId,'ComponentTypeName'=>$r['ComponentTypeName'],'data'=>$data)));
		
		switch($r['ComponentTypeName'])
		{
			case 'textBox':
			{
				$defaultValue = RSFormProHelper::isCode($data['DEFAULTVALUE']);
				
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><input type="text" value="'.RSFormProHelper::htmlEscape($defaultValue).'" size="'.$data['SIZE'].'" /></td>';
			}
			break;
			
			case 'textArea':
			{
				$defaultValue = RSFormProHelper::isCode($data['DEFAULTVALUE']);	
				
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><textarea cols="'.$data['COLS'].'" rows="'.$data['ROWS'].'">'.RSFormProHelper::htmlEscape($defaultValue).'</textarea></td>';
			}
			break;
			
			case 'selectList':
			{
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><select '.($data['MULTIPLE']=='YES' ? 'multiple="multiple"' : '').' size="'.$data['SIZE'].'">';
				
				$items = RSFormProHelper::isCode($data['ITEMS']);
				$items = str_replace("\r",'',$items);
				$items = explode("\n",$items);
				
				foreach($items as $item)
				{
					$buf=explode('|',$item);
					
					if(preg_match('/\[g\]/',$item))
					{
						$out.='<optgroup label="'.RSFormProHelper::htmlEscape(str_replace('[g]', '', $item)).'">';
						continue;
					}
					if(preg_match('/\[\/g\]/',$item))
					{
						$out.='</optgroup>';
						continue;
					}
					
					if(count($buf)==1)
					{
						if(preg_match('/\[c\]/',$buf[0]))
							$out.='<option selected="selected">'.str_replace('[c]','',$buf[0]).'</option>';
						else
							$out.='<option value="'.RSFormProHelper::htmlEscape($buf[0]).'">'.$buf[0].'</option>';
					}
					if(count($buf)==2)
					{
						if(preg_match('/\[c\]/',$buf[1]))
							$out.='<option selected="selected" value="'.RSFormProHelper::htmlEscape($buf[0]).'">'.str_replace('[c]','',$buf[1]).'</option>';
						else
							$out.='<option value="'.RSFormProHelper::htmlEscape($buf[0]).'">'.$buf[1].'</option>';
					}
				}
				$out.='</select></td>';
			}
			break;
			
			case 'checkboxGroup':
			{
				$i=0;
				
				$out.='<td>'.$data['CAPTION'].'</td>';
				
				$items = RSFormProHelper::isCode($data['ITEMS']);
				$items = str_replace("\r",'',$items);
				$items = explode("\n",$items);
				
				$out.='<td>';
				foreach($items as $item)
				{
					$buf=explode("|",$item);
					if(count($buf)==1)
					{
						if(preg_match('/\[c\]/',$buf[0]))
						{
							$v=str_replace('[c]','',$buf[0]);
							$out.='<input checked="checked" type="checkbox" value="'.$v.'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$v.'</label>';
						}
						else
							$out.='<input type="checkbox" value="'.RSFormProHelper::htmlEscape($buf[0]).'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$buf[0].'</label>';
					}
					if(count($buf)==2)
					{
						if(preg_match('/\[c\]/',$buf[1]))
						{
							$v=str_replace('[c]','',$buf[1]);
							$out.='<input checked="checked" type="checkbox" value="'.RSFormProHelper::htmlEscape($buf[0]).'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$v.'</label>';
						}
						else
							$out.='<input type="checkbox" value="'.RSFormProHelper::htmlEscape($buf[0]).'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$buf[1].'</label>';

					}
					if($data['FLOW']=='VERTICAL') $out.='<br/>';
					$i++;
				}
				$out.='</td>';

			}
			break;
			
			case 'radioGroup':
			{
				$i=0;
				
				$out.='<td>'.$data['CAPTION'].'</td>';
				
				$items = RSFormProHelper::isCode($data['ITEMS']);
				$items = str_replace("\r",'',$items);
				$items = explode("\n",$items);
				
				$out.='<td>';
				foreach($items as $item)
				{
					$buf=explode("|",$item);
					if(count($buf)==1)
					{
						if(preg_match('/\[c\]/',$buf[0]))
						{
							$v=str_replace('[c]','',$buf[0]);
							$out.='<input checked="checked" type="radio" value="'.$v.'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$v.'</label>';
						}
						else
							$out.='<input type="radio" value="'.RSFormProHelper::htmlEscape($buf[0]).'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$buf[0].'</label>';
					}
					if(count($buf)==2)
					{
						if(preg_match('/\[c\]/',$buf[1]))
						{
							$v=str_replace('[c]','',$buf[1]);
							$out.='<input checked="checked" type="radio" value="'.$buf[0].'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$v.'</label>';
						}
						else
							$out.='<input type="radio" value="'.RSFormProHelper::htmlEscape($buf[0]).'" id="'.$data['NAME'].$i.'"/><label for="'.$data['NAME'].$i.'">'.$buf[1].'</label>';

					}
					if($data['FLOW']=='VERTICAL') $out.='<br/>';
					$i++;
				}
				$out.='</td>';

			}
			break;
			
			case 'calendar':
			{
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><img src="'.JURI::root(true).'/administrator/components/com_rsform/assets/images/icons/calendar.gif" /> '.JText::_('RSFP_COMP_FVALUE_'.$data['CALENDARLAYOUT']).'</td>';
			}
			break;
			
			case 'button':
			{
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><input type="button" value="'.RSFormProHelper::htmlEscape($data['LABEL']).'"/>';
				if ($data['RESET']=='YES')
					$out.='&nbsp;&nbsp;<input type="reset" value="'.RSFormProHelper::htmlEscape($data['RESETLABEL']).'"/>';
				$out.='</td>';
			}
			break;
			
			case 'captcha':
			{
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td>';
				switch (@$data['IMAGETYPE'])
				{
					default:
					case 'FREETYPE':
					case 'NOFREETYPE':
						$out.='<img src="'.JURI::root(true).'/index.php?option=com_rsform&amp;task=captcha&amp;componentId='.$componentId.'&amp;tmpl=component&amp;sid='.mt_rand().'" id="captcha'.$componentId.'" alt="'.$data['CAPTION'].'"/>';
						$out.=($data['FLOW']=='HORIZONTAL') ? '':'<br/>';
						$out.='<input type="text" value="" id="captchaTxt'.$componentId.'" '.$data['ADDITIONALATTRIBUTES'].' />';
						$out.=($data['SHOWREFRESH']=='YES') ? '&nbsp;&nbsp;<a href="" onclick="refreshCaptcha('.$componentId.',\''.JURI::root(true).'/index.php?option=com_rsform&amp;task=captcha&amp;componentId='.$componentId.'&amp;tmpl=component\'); return false;">'.$data['REFRESHTEXT'].'</a>':'';
					break;
					
					case 'INVISIBLE':
						$out.='{hidden captcha}';
					break;
				}
				$out.='</td>';
			}
			break;
			
			case 'fileUpload':
			{
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><input type="file" /></td>';
			}
			break;
			
			case 'freeText':
			{
				$out.='<td>&nbsp;</td>';
				$out.='<td>'.$data['TEXT'].'</td>';
			}
			break;
			
			case 'hidden':
			{
				$out.='<td>&nbsp;</td>';
				$out.='<td>{hidden field}</td>';
			}
			break;
			
			case 'imageButton':
			{			
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td>';
				$out.='<input type="image" src="'.RSFormProHelper::htmlEscape($data['IMAGEBUTTON']).'"/>';
				if($data['RESET']=='YES')
					$out.='&nbsp;&nbsp;<input type="image" src="'.RSFormProHelper::htmlEscape($data['IMAGERESET']).'"/>';
				$out.='</td>';
			}
			break;
			
			case 'submitButton':
			{				
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><input type="button" value="'.RSFormProHelper::htmlEscape($data['LABEL']).'" />';
				if($data['RESET']=='YES')
					$out.='&nbsp;&nbsp;<input type="reset" value="'.RSFormProHelper::htmlEscape($data['RESETLABEL']).'"/>';
				$out.='</td>';
			}
			break;
			
			case 'password':
			{				
				$out.='<td>'.$data['CAPTION'].'</td>';
				$out.='<td><input type="password" value="'.RSFormProHelper::htmlEscape($data['DEFAULTVALUE']).'" size="'.$data['SIZE'].'"/></td>';
			}
			break;
			
			case 'ticket':
			{				
				$out.='<td>&nbsp;</td>';
				$out.='<td>'.RSFormProHelper::generateString($data['LENGTH'],$data['CHARACTERS']).'</td>';
			}
			break;
			
			case 'pageBreak':
				$out.='<td>&nbsp;</td>';
				$out.='<td><input type="button" value="'.RSFormProHelper::htmlEscape($data['PREVBUTTON']).'" /> <input type="button" value="'.RSFormProHelper::htmlEscape($data['NEXTBUTTON']).'" /></td>';
			break;
			
			default:
				$out = '<td colspan="2" style="color:#333333"><em>'.JText::_('RSFP_COMP_PREVIEW_NOT_AVAILABLE').'</em></td>';
			break;
		}
		
		//Trigger Event - rsfp_bk_onAfterCreateComponentPreview
		$mainframe->triggerEvent('rsfp_bk_onAfterCreateComponentPreview',array(array('out'=>&$out, 'formId'=>$formId, 'componentId'=>$componentId, 'ComponentTypeName'=>$r['ComponentTypeName'],'data'=>$data)));
		
		return $out;
	}
	
	function htmlEscape($val)
	{
		return htmlentities($val, ENT_COMPAT, 'UTF-8');
	}
	
	function explode($value)
	{
		$value = str_replace("\r", '', $value);
		$value = explode("\n", $value);
		
		return $value;
	}
	
	function readFile($file, $download_name=null)
	{
		if (empty($download_name))
			$download_name = basename($file);
			
		$fsize = filesize($file);
		
		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header("Pragma: no-cache");
		header("Expires: 0"); 
		header("Content-Description: File Transfer");
		header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
		header("Content-Type: application/octet-stream");
		header("Content-Length: ".(string) ($fsize));
		header('Content-Disposition: attachment; filename="'.$download_name.'"');
		header("Content-Transfer-Encoding: binary\n");
		ob_end_flush();
		RSFormProHelper::readFileChunked($file);
		exit();
	}
	
	function readFileChunked($filename, $retbytes=true)
	{
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
	
	function getReplacements($SubmissionId, $skip_globals=false)
	{
		// Small hack
		return RSFormProHelper::sendSubmissionEmails($SubmissionId, true, $skip_globals);
	}
	
	function sendSubmissionEmails($SubmissionId, $only_return_replacements=false, $skip_globals=false)
	{
		$db = JFactory::getDBO();
		$config = JFactory::getConfig();
		$SubmissionId = (int) $SubmissionId;
		
		$db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$SubmissionId."'");
		$submission = $db->loadObject();
		
		$submission->values = array();
		$db->setQuery("SELECT FieldName, FieldValue FROM #__rsform_submission_values WHERE SubmissionId='".$SubmissionId."'");
		$fields = $db->loadObjectList();
		foreach ($fields as $field)
			$submission->values[$field->FieldName] = $field->FieldValue;
		unset($fields);
		
		$formId = $submission->FormId;
		$db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId='".$formId."'");
		$form = $db->loadObject();
		$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);

		$placeholders = array();
		$values = array();
		
		$db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyName, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$formId."' AND c.Published='1' AND p.PropertyName IN ('NAME', 'CAPTION', 'ATTACHUSEREMAIL', 'ATTACHADMINEMAIL', 'WYSIWYG')");
		$components = $db->loadObjectList();
		$properties 	   = array();
		$uploadFields 	   = array();
		$multipleFields    = array();
		$textareaFields    = array();
		$userEmailUploads  = array();
		$adminEmailUploads = array();
		foreach ($components as $component)
		{
			// Upload fields - grab by NAME so that we can use it later on when checking $_FILES
			if ($component->ComponentTypeId == 9)
			{
				if ($component->PropertyName == 'NAME')
					$uploadFields[] = $component->PropertyValue;
				
				if ($component->PropertyName == 'ATTACHUSEREMAIL' && $component->PropertyValue == 'YES')
				{
					$userEmailUploads[] = $component->ComponentId;
					continue;
				}
				elseif ($component->PropertyName == 'ATTACHADMINEMAIL' && $component->PropertyValue == 'YES')
				{
					$adminEmailUploads[] = $component->ComponentId;
					continue;
				}
			}
			// Multiple fields - grab by ComponentId for performance
			elseif (in_array($component->ComponentTypeId, array(3, 4)))
			{
				if ($component->PropertyName == 'NAME')
					$multipleFields[] = $component->ComponentId;
			}
			// Textarea fields - grab by ComponentId for performance
			elseif ($component->ComponentTypeId == 2)
			{
				if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
					$textareaFields[] = $component->ComponentId;
			}
			
			$properties[$component->ComponentId][$component->PropertyName] = $component->PropertyValue;
		}
		
		$secret = $config->getValue('config.secret');
		foreach ($properties as $ComponentId => $property)
		{
			// {component:caption}
			$placeholders[] = '{'.$property['NAME'].':caption}';
			$values[] = isset($property['CAPTION']) ? $property['CAPTION'] : '';
			
			// {component:name}
			$placeholders[] = '{'.$property['NAME'].':name}';
			$values[] = $property['NAME'];
			
			// {component:value}
			$placeholders[] = '{'.$property['NAME'].':value}';
			$value = '';
			if (isset($submission->values[$property['NAME']]))
			{
				$value = $submission->values[$property['NAME']];
				
				// Check if this is an upload field
				if (in_array($property['NAME'], $uploadFields))
					$value = '<a href="'.JURI::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($submission->SubmissionId.$secret.$property['NAME']).'">'.basename($submission->values[$property['NAME']]).'</a>';
				// Check if this is a multiple field
				elseif (in_array($ComponentId, $multipleFields))
					$value = str_replace("\n", $form->MultipleSeparator, $value);
				elseif ($form->TextareaNewLines && in_array($ComponentId, $textareaFields))
					$value = nl2br($value);
			}
			$values[] = $value;
			
			// {component:path}
			if (in_array($property['NAME'], $uploadFields) && isset($submission->values[$property['NAME']]))
			{
				$placeholders[] = '{'.$property['NAME'].':path}';
				$filepath = $submission->values[$property['NAME']];
				$filepath = str_replace(JPATH_SITE.DS, JURI::root(), $filepath);
				$filepath = str_replace(array('\\', '\\/', '//\\'), '/', $filepath);
				$values[] = $filepath;
			}
		}
		$placeholders[] = '{_STATUS:value}';
		$values[] = isset($submission->values['_STATUS']) ? JText::_('RSFP_PAYPAL_STATUS_'.$submission->values['_STATUS']) : '';
		
		$user = JFactory::getUser($submission->UserId);
		if (empty($user->id))
		{
			$user = new stdClass();
			$user->id = 0;
			$user->username = '';
			$user->email = '';
			$user->name = '';
		}
		
		if (!$skip_globals)
		{
			array_push($placeholders, '{global:username}', '{global:userid}', '{global:useremail}', '{global:fullname}', '{global:userip}', '{global:date_added}', '{global:sitename}', '{global:siteurl}');
			array_push($values, $user->username, $user->id, $user->email, $user->name, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', $submission->DateSubmitted, $config->getValue('config.sitename'), JURI::root());
		}
		
		if ($only_return_replacements)
			return array($placeholders, $values);
		
		$userEmail = array(
			'to' => str_replace($placeholders, $values, $form->UserEmailTo),
			'cc' => str_replace($placeholders, $values, $form->UserEmailCC),
			'bcc' => str_replace($placeholders, $values, $form->UserEmailBCC),
			'from' => str_replace($placeholders, $values, $form->UserEmailFrom),
			'replyto' => str_replace($placeholders, $values, $form->UserEmailReplyTo),
			'fromName' => str_replace($placeholders, $values, $form->UserEmailFromName),
			'text' => str_replace($placeholders, $values, $form->UserEmailText),
			'subject' => str_replace($placeholders, $values, $form->UserEmailSubject),
			'mode' => $form->UserEmailMode,
			'files' => array()
		);

		// user cc
		if (strpos($userEmail['cc'], ',') !== false)
			$userEmail['cc'] = explode(',', $userEmail['cc']);
		// user bcc
		if (strpos($userEmail['bcc'], ',') !== false)
			$userEmail['bcc'] = explode(',', $userEmail['bcc']);
		
		jimport('joomla.filesystem.file');
		
		$file = str_replace($placeholders, $values, $form->UserEmailAttachFile);
		if ($form->UserEmailAttach && JFile::exists($file))
			$userEmail['files'][] = $file;
		
		// Need to attach files
		// User Email
		foreach ($userEmailUploads as $componentId)
		{
			$name = $properties[$componentId]['NAME'];
			$userEmail['files'][] = $submission->values[$name];
		}
		
		$adminEmail = array(
			'to' => str_replace($placeholders, $values, $form->AdminEmailTo),
			'cc' => str_replace($placeholders, $values, $form->AdminEmailCC),
			'bcc' => str_replace($placeholders, $values, $form->AdminEmailBCC),
			'from' => str_replace($placeholders, $values, $form->AdminEmailFrom),
			'replyto' => str_replace($placeholders, $values, $form->AdminEmailReplyTo),
			'fromName' => str_replace($placeholders, $values, $form->AdminEmailFromName),
			'text' => str_replace($placeholders, $values, $form->AdminEmailText),
			'subject' => str_replace($placeholders, $values, $form->AdminEmailSubject),
			'mode' => $form->AdminEmailMode,
			'files' => array()
		);
		
		// admin cc
		if (strpos($adminEmail['cc'], ',') !== false)
			$adminEmail['cc'] = explode(',', $adminEmail['cc']);
		// admin bcc
		if (strpos($adminEmail['bcc'], ',') !== false)
			$adminEmail['bcc'] = explode(',', $adminEmail['bcc']);
		
		// Admin Email
		foreach ($adminEmailUploads as $componentId)
		{
			$name = $properties[$componentId]['NAME'];
			$adminEmail['files'][] = $submission->values[$name];
		}
		
		// Script called before the User Email is sent.
		eval($form->UserEmailScript);
		
		// mail users
		$recipients = explode(',',$userEmail['to']);
		if(!empty($recipients))
			foreach($recipients as $recipient)
				if(!empty($recipient))
					JUtility::sendMail($userEmail['from'], $userEmail['fromName'], $recipient, $userEmail['subject'], $userEmail['text'], $userEmail['mode'], !empty($userEmail['cc']) ? $userEmail['cc'] : null, !empty($userEmail['bcc']) ? $userEmail['bcc'] : null, $userEmail['files'], !empty($userEmail['replyto']) ? $userEmail['replyto'] : '');
		
		// Script called before the Admin Email is sent.
		eval($form->AdminEmailScript);
		
		//mail admins
		$recipients = explode(',',$adminEmail['to']);
		if(!empty($recipients))
			foreach($recipients as $recipient)
				if(!empty($recipient))
					JUtility::sendMail($adminEmail['from'], $adminEmail['fromName'], $recipient, $adminEmail['subject'], $adminEmail['text'], $adminEmail['mode'], !empty($adminEmail['cc']) ? $adminEmail['cc'] : null, !empty($adminEmail['bcc']) ? $adminEmail['bcc'] : null, $adminEmail['files'], !empty($adminEmail['replyto']) ? $adminEmail['replyto'] : '');
		
		return array($placeholders, $values);
	}
	
	function escapeArray(&$val, &$key)
	{
		$db = JFactory::getDBO();
		$val = $db->getEscaped($val);
		$key = $db->getEscaped($key);
	}
	
	function componentExists($formId, $componentTypeId)
	{
		$formId = (int) $formId;
		$db = JFactory::getDBO();
		
		if (is_array($componentTypeId))
		{
			JArrayHelper::toInteger($componentTypeId);
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE ComponentTypeId IN (".implode(',', $componentTypeId).") AND FormId='".$formId."' AND Published='1'");
		}
		else
		{
			$componentTypeId = (int) $componentTypeId;
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE ComponentTypeId='".$componentTypeId."' AND FormId='".$formId."' AND Published='1'");
		}
		
		return $db->loadResultArray();
	}
	
	function cleanCache()
	{
		jimport('joomla.html.parameter');
		
		$config = JFactory::getConfig();
		$plugin = JPluginHelper::getPlugin('system', 'cache');
		
		$params = new JParameter($plugin->params);
		$options = array(
			'cachebase' 	=> JPATH_BASE.DS.'cache',
			'defaultgroup' 	=> 'page',
			'lifetime' 		=> $params->get('cachetime', 15) * 60,
			'browsercache'	=> $params->get('browsercache', false),
			'caching'		=> false,
			'language'		=> $config->getValue('config.language', 'en-GB')
		);
		$cache =& JCache::getInstance('page', $options);
		
		$id = $cache->_makeId();
		$handler =& $cache->_getStorage();
		if (!JError::isError($handler))
			$handler->remove($id, 'page');
		
		// Test this
		// $cache->clean();
	}
	
	function loadTheme($form)
	{
		jimport('joomla.html.parameter');
		
		$doc =& JFactory::getDocument();
		$form->ThemeParams = new JParameter($form->ThemeParams);
			
		if ($form->ThemeParams->get('num_css', 0) > 0)
			for ($i=0; $i<$form->ThemeParams->get('num_css'); $i++)
			{
				$css = $form->ThemeParams->get('css'.$i);
				$doc->addStyleSheet(JURI::root(true).'/components/com_rsform/assets/themes/'.$form->ThemeParams->get('name').'/'.$css);
			}
		if ($form->ThemeParams->get('num_js', 0) > 0)
			for ($i=0; $i<$form->ThemeParams->get('num_js'); $i++)
			{
				$js = $form->ThemeParams->get('js'.$i);
				$doc->addScript(JURI::root(true).'/components/com_rsform/assets/themes/'.$form->ThemeParams->get('name').'/'.$js);
			}
	}
	
	function showForm($formId, $val='', $validation=array())
	{
		$mainframe =& JFactory::getApplication();
		
		$formId = (int) $formId;
		
		$db = JFactory::getDBO();
		$doc =& JFactory::getDocument();
		
		$db->setQuery("SELECT `FormId`, `FormLayout`, `ScriptDisplay`, `ErrorMessage`, `FormTitle`, `CSS`, `JS`, `CSSClass`, `CSSId`, `CSSName`, `CSSAction`, `CSSAdditionalAttributes`, `AjaxValidation`, `ThemeParams` FROM #__rsform_forms WHERE FormId='".$formId."' AND `Published`='1'");
		$form = $db->loadObject();
		
		if ($form->JS)
			$doc->addCustomTag($form->JS);
		if ($form->CSS)
			$doc->addCustomTag($form->CSS);
		if ($form->ThemeParams)
			RSFormProHelper::loadTheme($form);
		
		$doc->addStyleSheet(JURI::root(true).'/components/com_rsform/assets/css/front.css');
		$doc->addScript(JURI::root(true).'/components/com_rsform/assets/js/script.js');
		
		$calendars = RSFormProHelper::componentExists($formId, 6); //6 is the componentTypeId for calendar
		if(!empty($calendars))
		{
			$doc->addStyleSheet(JURI::root(true).'/components/com_rsform/assets/calendar/calendar.css');
			
			$hidden = JRequest::getVar('hidden');
			$all_data = RSFormProHelper::getComponentProperties($calendars);
			foreach($calendars as $i => $calendarComponentId)
			{
				$data = $all_data[$calendarComponentId];
				
				$calendars['CALENDARLAYOUT'][$i] = $data['CALENDARLAYOUT'];
				$calendars['DATEFORMAT'][$i] = $data['DATEFORMAT'];
				$calendars['VALUES'][$i] = '';
				$calendars['EXTRA'][$i] = array();
				if (!empty($hidden[$data['NAME']]))
					$calendars['VALUES'][$i] = preg_replace('#[^0-9\/]+#i', '', $hidden[$data['NAME']]);
				
				if (!empty($data['MINDATE']))
					$calendars['EXTRA'][$i][] = "'mindate': '".$data['MINDATE']."'";
				if (!empty($data['MAXDATE']))
					$calendars['EXTRA'][$i][] = "'maxdate': '".$data['MAXDATE']."'";
				
				$calendars['EXTRA'][$i] = '{'.implode(', ', $calendars['EXTRA'][$i]).'}';
			}
			unset($all_data);
			
			$calendarsLayout = "'".implode("','", $calendars['CALENDARLAYOUT'])."'";
			$calendarsFormat = "'".implode("','", $calendars['DATEFORMAT'])."'";
			$calendarsValues = "'".implode("','", $calendars['VALUES'])."'";
			$calendarsExtra  = implode(',', $calendars['EXTRA']);
		}
		
		$formLayout = $form->FormLayout;
		unset($form->FormLayout);
		$errorMessage = $form->ErrorMessage;
		unset($form->ErrorMessage);
		
		$db->setQuery("SELECT p.PropertyValue AS name, c.ComponentId, c.ComponentTypeId, ct.ComponentTypeName, c.Order FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (c.ComponentId=p.ComponentId) LEFT JOIN #__rsform_component_types ct ON (ct.ComponentTypeId=c.ComponentTypeId) WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' AND c.Published='1' ORDER BY c.Order");
		$components = $db->loadObjectList();
		
		$pages   = array();
		$submits = array();
		foreach ($components as $component)
		{
			if ($component->ComponentTypeId == 41)
				$pages[] = $component->ComponentId;
			elseif ($component->ComponentTypeId == 13)
				$submits[] = $component->ComponentId;
		}
		
		$start_page = 0;
		if (!empty($validation))
			foreach ($components as $component)
			{
				if (in_array($component->ComponentId, $validation))
					break;
				if ($component->ComponentTypeId == 41)
					$start_page++;
			}
		
		$find 	  = array();
		$replace  = array();
		$all_data = RSFormProHelper::getComponentProperties($components);
		foreach ($components as $component)
		{
			$data = $all_data[$component->ComponentId];
			$data['componentTypeId'] = $component->ComponentTypeId;
			$data['ComponentTypeName'] = $component->ComponentTypeName;
			$data['Order'] = $component->Order;
			
			// Pagination
			if ($component->ComponentTypeId == 41)
				$data['PAGES'] = $pages;
			elseif ($component->ComponentTypeId == 13)
				$data['SUBMITS'] = $submits;
			
			// Caption
			$find[] = '{'.$component->name.':caption}';
			$caption = '';
			if (isset($data['SHOW']) && $data['SHOW'] == 'NO')
				$caption = '';
			elseif (isset($data['CAPTION']))
				$caption = $data['CAPTION'];
			$replace[] = $caption;
			
			// Body	
			$find[] = '{'.$component->name.':body}';
			$replace[] = RSFormProHelper::getFrontComponentBody($formId, $component->ComponentId, $data, $val, in_array($component->ComponentId,$validation));
			
			// Description
			$find[] = '{'.$component->name.':description}';
			$description = '';
			if (isset($data['SHOW']) && $data['SHOW'] == 'NO')
				$description = '';
			elseif (isset($data['DESCRIPTION']))
				$description = $data['DESCRIPTION'];
			$replace[] = $description;
			
			// Validation message
			$find[] = '{'.$component->name.':validation}';
			$validationMessage = '';
			if (isset($data['SHOW']) && $data['SHOW'] == 'NO')
				$validationMessage = '';
			elseif (isset($data['VALIDATIONMESSAGE']))
			{
				if(!empty($validation) && in_array($component->ComponentId,$validation))
					$validationMessage = '<span id="component'.$component->ComponentId.'" class="formError">'.$data['VALIDATIONMESSAGE'].'</span>';
				else
					$validationMessage = '<span id="component'.$component->ComponentId.'" class="formNoError">'.$data['VALIDATIONMESSAGE'].'</span>';
			}
			$replace[] = $validationMessage;
		}
		unset($all_data);
		
		$u = RSFormProHelper::getURL();
		
		//Trigger Event - onInitFormDisplay
		$mainframe->triggerEvent('rsfp_f_onInitFormDisplay',array(array('find'=>&$find,'replace'=>&$replace,'formLayout'=>&$formLayout)));
		
		$user = JFactory::getUser();
		$jconfig = JFactory::getConfig();
		array_push($find, '{global:formtitle}', '{global:username}', '{global:userip}', '{global:userid}', '{global:useremail}', '{global:fullname}', '{global:sitename}', '{global:siteurl}');
		array_push($replace, $form->FormTitle, $user->get('username'), isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', $user->get('id'), $user->get('email'), $user->get('name'), $jconfig->getValue('config.sitename'), JURI::root());
		
		$formLayout = str_replace($find,$replace,$formLayout);
		
		if (strpos($formLayout, 'class="formError"') !== false)
			$formLayout = str_replace('{error}', $errorMessage, $formLayout);
		else
			$formLayout = str_replace('{error}', '', $formLayout);
		
		$formLayout.= '<input type="hidden" name="form[formId]" value="'.$formId.'"/>';
		
		$CSSClass = $form->CSSClass ? ' class="'.RSFormProHelper::htmlEscape($form->CSSClass).'"' : '';
		$CSSId = $form->CSSId ? ' id="'.RSFormProHelper::htmlEscape($form->CSSId).'"' : '';
		$CSSName = $form->CSSName ? ' name="'.RSFormProHelper::htmlEscape($form->CSSName).'"' : '';
		$u = $form->CSSAction ? RSFormProHelper::htmlEscape($form->CSSAction) : $u;
		$CSSAdditionalAttributes = $form->CSSAdditionalAttributes ? ' '.trim($form->CSSAdditionalAttributes) : '';
		
		$formLayout = '<form method="post" '.$CSSId.$CSSClass.$CSSName.$CSSAdditionalAttributes.' enctype="multipart/form-data" action="'.RSFormProHelper::htmlEscape($u).'">'.$formLayout.'</form>';
		if(!empty($calendars))
		{
			$formLayout .= "\n".'<script type="text/javascript" src="'.JURI::root(true).'/components/com_rsform/assets/calendar/cal.js"></script>'."\n";
			$formLayout .= '<script type="text/javascript">'.RSFormProHelper::getCalendarJS().'</script>'."\n";
			$formLayout .= '<script type="text/javascript" defer="defer">rsf_CALENDAR.util.Event.addListener(window, "load", rsfp_init('.$formId.',{ layouts: Array('.$calendarsLayout.'), formats: Array('.$calendarsFormat.'), values: Array('.$calendarsValues.'), extra: Array('.$calendarsExtra.') }));</script>'."\n";
		}
		if (!empty($pages))
		{
			$formLayout .= '<script type="text/javascript" src="'.JURI::root(true).'/components/com_rsform/assets/js/pages.js"></script>'."\n";
			$formLayout .= '<script type="text/javascript">rsfp_changePage('.$formId.', '.$start_page.', '.count($pages).')</script>'."\n";
		}
		
		if ($form->AjaxValidation)
			$formLayout .= '<script type="text/javascript">rsfp_addEvent(window, \'load\', function(){var form = rsfp_getForm('.$formId.'); form.onsubmit = ajaxValidation;});</script>';
		
		$RSadapter = RSFormProHelper::getLegacyAdapter();
		eval($form->ScriptDisplay);
		
		//Trigger Event - onBeforeFormDisplay
		$mainframe->triggerEvent('rsfp_f_onBeforeFormDisplay', array(array('formLayout'=>&$formLayout,'formId'=>$formId)));
		return $formLayout;
	}
	
	function showThankYouMessage($formId)
	{
		$mainframe =& JFactory::getApplication();
		
		$output = '';
		$formId = (int) $formId;		
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT ThemeParams FROM #__rsform_forms WHERE FormId='".$formId."'");
		$form = $db->loadObject();
		if ($form->ThemeParams)
			RSFormProHelper::loadTheme($form);
		
		$session =& JFactory::getSession();
		$formparams = $session->get('com_rsform.formparams.'.$formId);
		$output = base64_decode($formparams->thankYouMessage);
		
		// Clear
		$session->clear('com_rsform.formparams.'.$formId);

		//Trigger Event - onAfterShowThankyouMessage
		$mainframe->triggerEvent('rsfp_f_onAfterShowThankyouMessage', array(array('output'=>&$output,'formId'=>&$formId)));
		
		// Cache enabled ?
		jimport('joomla.plugin.helper');
		$cache_enabled = JPluginHelper::isEnabled('system', 'cache');
		if ($cache_enabled)
			RSFormProHelper::cleanCache();
		
		return $output;
	}
	
	function processForm($formId)
	{
		$mainframe =& JFactory::getApplication();
		
		$formId = (int) $formId;
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `ScriptProcess`, `ScriptProcess2`, `UserEmailScript`, `AdminEmailScript`, `ReturnUrl`, `ShowThankyou`, `Thankyou`, `ShowContinue` FROM #__rsform_forms WHERE `FormId`='".$formId."'");
		$form = $db->loadObject();
		
		$invalid = RSFormProHelper::validateForm($formId);
		
		//Trigger Event - onBeforeFormValidation
		$mainframe->triggerEvent('rsfp_f_onBeforeFormValidation', array(array('invalid'=>&$invalid)));
		
		if (!empty($invalid))
			return $invalid;
		
		$userEmail=array(
			'to'=>'',
			'cc'=>'',
			'bcc'=>'',
			'from'=>'',
			'replyto'=>'',
			'fromName'=>'',
			'text'=>'',
			'subject'=>'',
			'files' =>array()
			);
		$adminEmail=array(
			'to'=>'',
			'cc'=>'',
			'bcc'=>'',
			'from'=>'',
			'replyto'=>'',
			'fromName'=>'',
			'text'=>'',
			'subject'=>'',
			'files' =>array()
			);
		
		$post = JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$_POST['form'] = $post;
		
		$RSadapter = RSFormProHelper::getLegacyAdapter();
		eval($form->ScriptProcess);
		
		$post = $_POST['form'];
		
		//Trigger Event - onBeforeFormProcess
		$mainframe->triggerEvent('rsfp_f_onBeforeFormProcess');
		
		if (empty($invalid))
		{
			// Cache enabled ?
			jimport('joomla.plugin.helper');
			$cache_enabled = JPluginHelper::isEnabled('system', 'cache');
			if ($cache_enabled)
				RSFormProHelper::cleanCache();
			
			$user = JFactory::getUser();
			
			// Add to db (submission)
			$db->setQuery("INSERT INTO #__rsform_submissions SET `FormId`='".$formId."', `DateSubmitted`=NOW(), `UserIp`='".(isset($_SERVER['REMOTE_ADDR']) ? $db->getEscaped($_SERVER['REMOTE_ADDR']) : '')."', `Username`='".$db->getEscaped($user->get('username'))."', `UserId`='".(int) $user->get('id')."'");
			$db->query();
			
			$SubmissionId = $db->insertid();
			
			$files = JRequest::get('files');
			if (isset($files['form']['tmp_name']) && is_array($files['form']['tmp_name']))
			{
				$names = array();
				foreach ($files['form']['tmp_name'] as $fieldName => $val)
				{
					if ($files['form']['error'][$fieldName]) continue;
						$names[] = $db->getEscaped($fieldName);
				}
				$componentIds = array();
				if (!empty($names))
				{
					$db->setQuery("SELECT c.ComponentId, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId AND p.PropertyName='NAME') WHERE c.FormId='".$formId."' AND p.PropertyValue IN ('".implode("','", $names)."')");
					$results = $db->loadObjectList();
					
					foreach ($results as $result)
						$componentIds[$result->PropertyValue] = $result->ComponentId;
				}
				
				$all_data = RSFormProHelper::getComponentProperties($componentIds);
				
				jimport('joomla.filesystem.file');				
				foreach ($files['form']['tmp_name'] as $fieldName => $val)
				{
					if ($files['form']['error'][$fieldName]) continue;
					
					$data = @$all_data[$componentIds[$fieldName]];
					if (empty($data)) continue;
					
					// Prefix
					$prefix = uniqid('').'-';
					if (isset($data['PREFIX']) && strlen(trim($data['PREFIX'])) > 0)
						$prefix = RSFormProHelper::isCode($data['PREFIX']);
					
					// Path
					$realpath = realpath($data['DESTINATION'].DS);
					if (substr($realpath, -1) != DS)
						$realpath .= DS;
					
					// Filename
					$file = $realpath.$prefix.$files['form']['name'][$fieldName];
					
					// Upload File
					JFile::upload($files['form']['tmp_name'][$fieldName], $file);
					
					// Add to db (submission value)
					$db->setQuery("INSERT INTO #__rsform_submission_values SET `SubmissionId`='".$SubmissionId."', `FormId`='".$formId."', `FieldName`='".$db->getEscaped($fieldName)."', `FieldValue`='".$db->getEscaped($file)."'");
					$db->query();
					
					// Attach to user and admin email
					if ($data['ATTACHUSEREMAIL']=='YES')
						$userEmail['files'][] = $file;
					if ($data['ATTACHADMINEMAIL']=='YES')
						$adminEmail['files'][] = $file;
				}
			}
			
			//Trigger Event - onBeforeStoreSubmissions
			$mainframe->triggerEvent('rsfp_f_onBeforeStoreSubmissions', array(array('formId'=>$formId,'post'=>&$post)));
			
			// Add to db (values)
			foreach ($post as $key => $val)
			{
				$val = is_array($val) ? implode("\n", $val) : $val;
				$val = RSFormProHelper::stripJava($val);
				
				$db->setQuery("INSERT INTO #__rsform_submission_values SET `SubmissionId`='".$SubmissionId."', `FormId`='".$formId."', `FieldName`='".$db->getEscaped($key)."', `FieldValue`='".$db->getEscaped($val)."'");
				$db->query();
			}
			
			//Trigger Event - onAfterStoreSubmissions
			$mainframe->triggerEvent('rsfp_f_onAfterStoreSubmissions', array(array('SubmissionId'=>$SubmissionId, 'formId'=>$formId)));
			
			// Send emails
			list($replace, $with) = RSFormProHelper::sendSubmissionEmails($SubmissionId);
			
			// Thank You Message
			$thankYouMessage = str_replace($replace, $with, $form->Thankyou);
			$form->ReturnUrl = str_replace($replace, $with, $form->ReturnUrl);
			
			// Set redirect link
			$u = RSFormProHelper::getURL();
			
			// Create the Continue button
			$continueButton = '';
			if ($form->ShowContinue)
			{
				// Create goto link
				$goto = 'document.location.reload();';
				
				// Cache workaround #1
				if ($cache_enabled)
					$goto = "document.location='".addslashes($u)."';";
				
				if (!empty($form->ReturnUrl))
					$goto = "document.location='".addslashes($form->ReturnUrl)."';";
				
				// Continue button
				$continueButtonLabel = JText::_('RSFP_THANKYOU_BUTTON');
				if (strpos($continueButtonLabel, 'input'))
					$continueButton = JText::sprintf('RSFP_THANKYOU_BUTTON',$goto);
				else
					$continueButton = '<br/><input type="button" class="rsform-submit-button" name="continue" value="'.JText::_('RSFP_THANKYOU_BUTTON').'" onclick="'.$goto.'"/>';
			}
			
			$RSadapter = RSFormProHelper::getLegacyAdapter();
			eval($form->ScriptProcess2);
			
			$thankYouMessage .= $continueButton;
			
			//Trigger - After form process
			$mainframe->triggerEvent('rsfp_f_onAfterFormProcess', array(array('SubmissionId'=>$SubmissionId,'formId'=>$formId)));
			
			if (!$form->ShowThankyou && $form->ReturnUrl)
			{
				$mainframe->redirect($form->ReturnUrl);
				return;
			}
			
			// SESSION quick hack - we base64 encode it here and decode it when we show it
			$session =& JFactory::getSession();
			$formParams = new stdClass();
			$formParams->formProcessed = true;
			$formParams->submissionId = $SubmissionId;
			$formParams->thankYouMessage = base64_encode($thankYouMessage);
			$session->set('com_rsform.formparams.'.$formId, $formParams);
			
			// Cache workaround #2
			if ($cache_enabled)
			{
				$uniqid = uniqid('rsform');
				$u .= (strpos($u, '?') === false) ? '?skipcache='.$uniqid : '&skipcache='.$uniqid;
			}
			
			$mainframe->redirect($u);
		}

		return false;
	}
	
	function getURL()
	{
		// IIS hack
		if (RSFormProHelper::getConfig('global.iis') && !empty($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') !== false && !empty($_SERVER['QUERY_STRING']))
		{
			$u = JRoute::_('index.php?'.$_SERVER['QUERY_STRING'],false);
		}
		else
		{
			$u = JFactory::getURI();
			
			if (RSFormProHelper::isJ16())
			{
				// 1.6
				$u = JFactory::getURI($u->get('_uri'));
				$u = $u->toString($parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'));
			}
			else
			{
				// 1.5
				$u = $u->toString();
				
				// Joom!Fish workarounds...
				if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php'))
				{
					$u = JFactory::getURI();
					$u = $u->_uri;
				}
				// sh404SEF workarounds... as usual...
				if (class_exists('shRouter'))
				{
					$shConfig = shRouter::shGetConfig();
					if ($shConfig->Enabled)
					{
						$menus =& JApplication::getMenu('site', array());
						$active = $menus->getActive();
						if (!empty($active->home))
						{
							$db =& JFactory::getDBO();
							$db->setQuery("SELECT `link` FROM #__menu WHERE `home`='1' LIMIT 1");
							$u = JURI::root(true).'/'.$active->link.'&Itemid='.$active->id;
						}
					}
				}
			}
		}
		
		return $u;
	}
	
	function validateForm($formId)
	{
		$mainframe =& JFactory::getApplication();
		
		$invalid = array();
		$formId = (int) $formId;
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT ComponentId, ComponentTypeId FROM #__rsform_components WHERE FormId='".$formId."' AND Published=1 ORDER BY `Order`");
		$components = $db->loadObjectList();
		
		require_once(JPATH_SITE.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'validation.php');
		
		$componentIds = array();
		foreach ($components as $component)
			$componentIds[] = $component->ComponentId;		
		$all_data = RSFormProHelper::getComponentProperties($componentIds);
		if (empty($all_data))
			return $invalid;
		
		foreach ($components as $component)
		{
			$data 			= $all_data[$component->ComponentId];
			$required 		= isset($data['REQUIRED']) ? $data['REQUIRED'] : 'NO';
			$validationRule = isset($data['VALIDATIONRULE']) ? $data['VALIDATIONRULE'] : '';
			$typeId 		= $component->ComponentTypeId;
			
			// CAPTCHA
			if ($typeId == 8)
			{
				$session =& JFactory::getSession();
				$captchaCode = $session->get('com_rsform.captcha.'.$component->ComponentId);
				if ($data['IMAGETYPE'] == 'INVISIBLE')
				{
					$words = RSFormProHelper::getInvisibleCaptchaWords();
					if (!empty($post[$captchaCode]))
						$invalid[] = $data['componentId'];
					foreach ($words as $word)
						if (!empty($post[$word]))
							$invalid[] = $data['componentId'];
				}
				else
				{
					if (empty($post['form'][$data['NAME']]) || empty($captchaCode) || $post['form'][$data['NAME']] != $captchaCode)
						$invalid[] = $data['componentId'];
				}
			}
			
			// Password
			if ($typeId == 14 && $validationRule == 'password')
			{
				if ($post['form'][$data['NAME']] != $data['DEFAULTVALUE'])
					$invalid[] = $data['componentId'];
			}
			
			// Trigger Event - rsfp_bk_validate_onSubmitValidateRecaptcha
			if ($typeId == 24)
				$mainframe->triggerEvent('rsfp_bk_validate_onSubmitValidateRecaptcha',array(array('data'=> &$data,'invalid'=> &$invalid)));
			
			if ($typeId == 9)
			{
				$files = JRequest::getVar('form', null, 'files');
				
				// File has been *sent* to the server
				if (isset($files['tmp_name'][$data['NAME']]) && $files['error'][$data['NAME']] != 4)
				{
					// File has been uploaded correctly to the server
					if ($files['error'][$data['NAME']] == 0)
					{
						// Let's check if the extension is allowed
						$buf = explode('.', $files['name'][$data['NAME']]);
						$m = '#'.preg_quote($buf[count($buf)-1]).'#';
						if (!empty($data['ACCEPTEDFILES']) && !preg_match(strtolower($m),strtolower($data['ACCEPTEDFILES'])))
							$invalid[] = $data['componentId'];
						// Let's check if it's the correct size
						if ($files['size'][$data['NAME']] > 0 && $data['FILESIZE'] > 0 && $files['size'][$data['NAME']] > $data['FILESIZE']*1024)
							$invalid[] = $data['componentId'];
					}
					// File has not been uploaded correctly - next version we'll trigger some messages based on the error code
					else
						$invalid[] = $data['componentId'];
				}
				// File has not been sent but it's required
				elseif($required == 'YES')
					$invalid[] = $data['componentId'];
				
				continue;
			}
			
			if ($required == 'YES')
			{
				if (!isset($post['form'][$data['NAME']]))
				{
					$invalid[] = $data['componentId'];
					continue;
				}
				if (!is_array($post['form'][$data['NAME']]) && strlen(trim($post['form'][$data['NAME']])) == 0)
				{
					$invalid[] = $data['componentId'];
					continue;
				}
				if (!is_array($post['form'][$data['NAME']]) && strlen(trim($post['form'][$data['NAME']])) > 0 && is_callable(array('RSFormProValidations', $validationRule)) && call_user_func(array('RSFormProValidations', $validationRule),$post['form'][$data['NAME']],isset($data['VALIDATIONEXTRA']) ? $data['VALIDATIONEXTRA'] : '') == false)
				{
					$invalid[] = $data['componentId'];
					continue;
				}
				if (is_array($post['form'][$data['NAME']]))
				{
					$valid = implode('',$post['form'][$data['NAME']]);
					if(empty($valid))
					{
						$invalid[] = $data['componentId'];
						continue;
					}
				}
			}
			else
			{
				if (isset($post['form'][$data['NAME']]) && !is_array($post['form'][$data['NAME']]) && strlen(trim($post['form'][$data['NAME']])) > 0 && is_callable(array('RSFormProValidations', $validationRule)) && call_user_func(array('RSFormProValidations', $validationRule),$post['form'][$data['NAME']],isset($data['VALIDATIONEXTRA']) ? $data['VALIDATIONEXTRA'] : '' ) == false)
				{
					$invalid[] = $data['componentId'];
					continue;
				}
			}
		}
		return $invalid;
	}
	
	function getFrontComponentBody($formId, $componentId, $data, $value='', $invalid=false)
	{
		$mainframe =& JFactory::getApplication();
		
		$formId = (int) $formId;
		$componentId = (int) $componentId;
		
		$db = JFactory::getDBO();
		
		// Optimized, don't need this anymore
		//$db->setQuery("SELECT `ComponentTypeId`, `Order` FROM #__rsform_components WHERE ComponentId='".$componentId."' LIMIT 1");
		//$r = $db->loadAssoc();
		
		// For legacy reasons...
		$r = array();
		$r['ComponentTypeId'] = $data['componentTypeId'];
		$r['Order'] = @$data['Order'];
		
		$out = '';
		
		//Trigger Event - rsfp_bk_onBeforeCreateFrontComponentBody
		$mainframe->triggerEvent('rsfp_bk_onBeforeCreateFrontComponentBody',array(array('out'=>&$out, 'formId'=>$formId, 'componentId'=>$componentId,'data'=>$data,'value'=>$value)));
		
		switch($data['ComponentTypeName'])
		{
			case 1:
			case 'textBox':
				$defaultValue = RSFormProHelper::isCode($data['DEFAULTVALUE']);
				
				$className = 'rsform-input-box';
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$out .= '<input type="text" value="'.(isset($value[$data['NAME']]) ? RSFormProHelper::htmlEscape($value[$data['NAME']]) : RSFormProHelper::htmlEscape($defaultValue)).'" size="'.$data['SIZE'].'" '.((int) $data['MAXSIZE'] > 0 ? 'maxlength="'.(int) $data['MAXSIZE'].'"' : '').' name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].'/>';
			break;

			case 2:
			case 'textArea':
				$defaultValue = RSFormProHelper::isCode($data['DEFAULTVALUE']);
				
				$className = 'rsform-text-box';
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				if (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
				{
					$out .= RSFormProHelper::WYSIWYG('form['.$data['NAME'].']', (isset($value[$data['NAME']]) ? RSFormProHelper::htmlEscape($value[$data['NAME']]) : RSFormProHelper::htmlEscape($defaultValue)), 'id['.$data['NAME'].']', $data['COLS']*10, $data['ROWS']*10, $data['COLS'], $data['ROWS']);
				}
				else
					$out .= '<textarea cols="'.(int) $data['COLS'].'" rows="'.(int) $data['ROWS'].'" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].'>'.(isset($value[$data['NAME']]) ? RSFormProHelper::htmlEscape($value[$data['NAME']]) : RSFormProHelper::htmlEscape($defaultValue)).'</textarea>';
			break;

			case 3:
			case 'selectList':
				$className = 'rsform-select-box';
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$out .= '<select '.($data['MULTIPLE']=='YES' ? 'multiple="multiple"' : '').' name="form['.$data['NAME'].'][]" '.((int) $data['SIZE'] > 0 ? 'size="'.(int) $data['SIZE'].'"' : '').' id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].' >';
				
				$items = RSFormProHelper::isCode($data['ITEMS']);
				$items = str_replace("\r", "", $items);
				$items = explode("\n", $items);
				foreach ($items as $item)
				{
					$buf = explode('|',$item);
					
					if(preg_match('/\[g\]/',$item))
					{
						$out.='<optgroup label="'.RSFormProHelper::htmlEscape(str_replace('[g]', '', $item)).'">';
						continue;
					}
					if(preg_match('/\[\/g\]/',$item))
					{
						$out.='</optgroup>';
						continue;
					}
					
					$option_value = $buf[0];
					$option_value_trimmed = str_replace('[c]','',$option_value);
					$option_shown = count($buf) == 1 ? $buf[0] : $buf[1];
					$option_shown_trimmed = str_replace('[c]','',$option_shown);
					
					$option_checked = false;
					if (empty($value) && preg_match('/\[c\]/',$option_shown))
						$option_checked = true;
					if (isset($value[$data['NAME']]) && in_array($option_value_trimmed, $value[$data['NAME']]))
						$option_checked = true;
					
					$out .= '<option '.($option_checked ? 'selected="selected"' : '').' value="'.RSFormProHelper::htmlEscape($option_value_trimmed).'">'.RSFormProHelper::htmlEscape($option_shown_trimmed).'</option>';
				}
				$out .= '</select>';
			break;
			
			case 4:
			case 'checkboxGroup':
				$i = 0;
				
				$items = RSFormProHelper::isCode($data['ITEMS']);
				$items = str_replace("\r", "", $items);
				$items = explode("\n", $items);
				foreach ($items as $item)
				{
					$buf = explode('|',$item);
					
					$option_value = $buf[0];
					$option_value_trimmed = str_replace('[c]','',$option_value);
					$option_shown = count($buf) == 1 ? $buf[0] : $buf[1];
					$option_shown_trimmed = str_replace('[c]','',$option_shown);
					
					$option_checked = false;
					if (empty($value) && preg_match('/\[c\]/',$option_shown))
						$option_checked = true;
					if (isset($value[$data['NAME']]) && in_array($option_value_trimmed, $value[$data['NAME']]))
						$option_checked = true;
					
					$out .= '<input '.($option_checked ? 'checked="checked"' : '').' name="form['.$data['NAME'].'][]" type="checkbox" value="'.RSFormProHelper::htmlEscape($option_value_trimmed).'" id="'.$data['NAME'].$i.'" '.$data['ADDITIONALATTRIBUTES'].' /><label for="'.$data['NAME'].$i.'">'.$option_shown_trimmed.'</label>';
					
					if ($data['FLOW']=='VERTICAL')
						$out.='<br/>';
						
					$i++;
				}
			break;
			
			case 5:
			case 'radioGroup':
				$i = 0;
				
				$items = RSFormProHelper::isCode($data['ITEMS']);
				$items = str_replace("\r", "", $items);
				$items = explode("\n", $items);
				foreach ($items as $item)
				{
					$buf = explode('|',$item);
					
					$option_value = $buf[0];
					$option_value_trimmed = str_replace('[c]','',$option_value);
					$option_shown = count($buf) == 1 ? $buf[0] : $buf[1];
					$option_shown_trimmed = str_replace('[c]','',$option_shown);
					
					$option_checked = false;
					if (empty($value) && preg_match('/\[c\]/',$option_shown))
						$option_checked = true;
					if (isset($value[$data['NAME']]) && $value[$data['NAME']] == $option_value_trimmed)
						$option_checked = true;
					
					$out .= '<input '.($option_checked ? 'checked="checked"' : '').' name="form['.$data['NAME'].']" type="radio" value="'.RSFormProHelper::htmlEscape($option_value_trimmed).'" id="'.$data['NAME'].$i.'" '.$data['ADDITIONALATTRIBUTES'].' /><label for="'.$data['NAME'].$i.'">'.$option_shown_trimmed.'</label>';
					
					if ($data['FLOW']=='VERTICAL')
						$out.='<br/>';
					$i++;
				}
			break;
			
			case 6:
			case 'calendar':
				$calendars = RSFormProHelper::componentExists($formId, 6);
				$calendars = array_flip($calendars);
				
				$defaultValue = isset($value[$data['NAME']]) ? $value[$data['NAME']] : (isset($data['DEFAULTVALUE']) ? RSFormProHelper::isCode($data['DEFAULTVALUE']) : '');
				
				switch($data['CALENDARLAYOUT'])
				{
					case 'FLAT':						
						$className = 'rsform-calendar-box';
						if ($invalid)
							$className .= ' rsform-error';
				
						$out .= '<input id="txtcal'.$formId.'_'.$calendars[$componentId].'" name="form['.$data['NAME'].']" type="text" '.($data['READONLY'] == 'YES' ? 'readonly="readonly"' : '').' class="txtCal '.$className.'" value="'.RSFormProHelper::htmlEscape($defaultValue).'" '.$data['ADDITIONALATTRIBUTES'].'/><br />';
						$out .= '<div id="cal'.$formId.'_'.$calendars[$componentId].'Container" style="z-index:'.(9999-$data['Order']).'"></div>';
					break;

					case 'POPUP':
						$data['ADDITIONALATTRIBUTES2'] = $data['ADDITIONALATTRIBUTES'];
						
						$className = 'rsform-calendar-box';
						if ($invalid)
							$className .= ' rsform-error';
						RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
						
						$out .= '<input id="txtcal'.$formId.'_'.$calendars[$componentId].'" name="form['.$data['NAME'].']" type="text" '.($data['READONLY'] == 'YES' ? 'readonly="readonly"' : '').'  value="'.RSFormProHelper::htmlEscape($defaultValue).'" '.$data['ADDITIONALATTRIBUTES'].'/>';
						
						$className = 'rsform-calendar-button';
						if ($invalid)
							$className .= ' rsform-error';
						
						$out .= '<input id="btn'.$formId.'_'.$calendars[$componentId].'" type="button" value="'.RSFormProHelper::htmlEscape($data['POPUPLABEL']).'" onclick="showHideCalendar(\'cal'.$formId.'_'.$calendars[$componentId].'Container\');" class="btnCal '.$className.'" '.$data['ADDITIONALATTRIBUTES2'].' />';
						$out .= '<div id="cal'.$formId.'_'.$calendars[$componentId].'Container" style="clear:both;display:none;position:absolute;z-index:'.(9999-$data['Order']).'"></div>';
					break;
				}
				
				$out .= '<input id="hiddencal'.$formId.'_'.$calendars[$componentId].'" type="hidden" name="hidden['.$data['NAME'].']" />';
			break;
			
			case 7:
			case 'button':
				$data['ADDITIONALATTRIBUTES2'] = $data['ADDITIONALATTRIBUTES'];
				
				$className = 'rsform-button';
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$out .= '<input type="button" value="'.RSFormProHelper::htmlEscape($data['LABEL']).'" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].' />';
				if ($data['RESET']=='YES')
				{
					$className = 'rsform-reset-button';
					RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES2'], $className);
					
					$out .= '&nbsp;&nbsp;<input type="reset" value="'.RSFormProHelper::htmlEscape($data['RESETLABEL']).'" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES2'].' />';
				}
			break;
			
			case 8:
			case 'captcha':
				switch (@$data['IMAGETYPE'])
				{
					default:
					case 'FREETYPE':
					case 'NOFREETYPE':
						$className = 'rsform-captcha-box';
						if ($invalid)
							$className .= ' rsform-error';
						RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
						$out .= '<img src="'.JURI::root(true).'/index.php?option=com_rsform&amp;task=captcha&amp;componentId='.$componentId.'&amp;tmpl=component&amp;sid='.mt_rand().'" id="captcha'.$componentId.'" alt="'.RSFormProHelper::htmlEscape($data['CAPTION']).' "/>';
						if ($data['FLOW'] == 'VERTICAL')
							$out .= '<br />';
						$out .= '<input type="text" name="form['.$data['NAME'].']" value="" id="captchaTxt'.$componentId.'" '.$data['ADDITIONALATTRIBUTES'].' />';
						if ($data['SHOWREFRESH']=='YES')
							$out .= '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="refreshCaptcha('.$componentId.',\''.JURI::root(true).'/index.php?option=com_rsform&amp;task=captcha&amp;componentId='.$componentId.'&amp;tmpl=component\'); return false;">'.$data['REFRESHTEXT'].'</a>';
					break;
					
					case 'INVISIBLE':
						// a list of words that spam bots might auto-complete
						$words = RSFormProHelper::getInvisibleCaptchaWords();
						$word = $words[array_rand($words, 1)];
						
						// a list of styles so that the field is hidden
						$styles = array('display: none !important', 'position: absolute !important; left: -4000px !important; top: -4000px !important;', 'position: absolute !important; left: -4000px !important; top: -4000px !important; display: none !important', 'position: absolute !important; display: none !important', 'display: none !important; position: absolute !important; left: -4000px !important; top: -4000px !important;');
						$style = $styles[array_rand($styles, 1)];
						
						// now we're going to shuffle the properties of the html tag
						$properties = array('type="text"', 'name="'.$word.'"', 'value=""', 'style="'.$style.'"');
						shuffle($properties);
						
						$session =& JFactory::getSession();
						$session->set('com_rsform.captcha.'.$componentId, $word);
						
						$out .= '<input '.implode(' ', $properties).' />';
					break;
				}
			break;
			
			case 9:
			case 'fileUpload':
				$className = 'rsform-upload-box';
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$out .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.(int) $data['FILESIZE'].'000" />';
				$out .= '<input type="file" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].' />';
			break;
			
			case 10:
			case 'freeText':
				$out .= $data['TEXT'];
			break;
			
			case 11:
			case 'hidden':
				$defaultValue = RSFormProHelper::isCode($data['DEFAULTVALUE']);
				$out .= '<input type="hidden" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" value="'.RSFormProHelper::htmlEscape($defaultValue).'" '.$data['ADDITIONALATTRIBUTES'].' />';
			break;
			
			case 12:
			case 'imageButton':
				$data['ADDITIONALATTRIBUTES2'] = $data['ADDITIONALATTRIBUTES'];
				
				$className = 'rsform-image-button';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$data['ADDITIONALATTRIBUTES3'] = $data['ADDITIONALATTRIBUTES'];
				
				$pages = RSFormProHelper::componentExists($formId, 41);
				$pages = count($pages);
				if (!empty($pages))
				{
					if (empty($data['PREVBUTTON']))
						$data['PREVBUTTON'] = JText::_('PREV');
					
					$onclick = 'rsfp_changePage('.$formId.', '.($pages-1).', '.$pages.')';
					RSFormProHelper::addOnClick($data['ADDITIONALATTRIBUTES3'], $onclick);
					
					$out .= '<input type="button" value="'.RSFormProHelper::htmlEscape($data['PREVBUTTON']).'"  id="'.$data['NAME'].'Prev" '.$data['ADDITIONALATTRIBUTES3'].' />';
				}
				
				$out .= '<input type="image" src="'.RSFormProHelper::htmlEscape($data['IMAGEBUTTON']).'" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES2'].' />';
				if ($data['RESET']=='YES')
				{
					$className = 'rsform-reset-button';
					RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES2'], $className);
					
					$out .= '<input type="reset" name="" id="reset_'.$data['NAME'].'" style="display: none !important" />&nbsp;&nbsp;<input onclick="document.getElementById(\'reset_'.$data['NAME'].'\').click();return false;" type="image" src="'.RSFormProHelper::htmlEscape($data['IMAGERESET']).'" name="form['.$data['NAME'].']" '.$data['ADDITIONALATTRIBUTES2'].' />';
				}
			break;
			
			case 13:
			case 'submitButton':
				$data['ADDITIONALATTRIBUTES2'] = $data['ADDITIONALATTRIBUTES'];
				
				$className = 'rsform-submit-button';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$data['ADDITIONALATTRIBUTES3'] = $data['ADDITIONALATTRIBUTES'];
				
				$last_submit = $componentId == end($data['SUBMITS']);
				$pages = RSFormProHelper::componentExists($formId, 41);
				$pages = count($pages);
				if (!empty($pages) && $last_submit)
				{
					if (empty($data['PREVBUTTON']))
						$data['PREVBUTTON'] = JText::_('PREV');
					
					$onclick = 'rsfp_changePage('.$formId.', '.($pages-1).', '.$pages.')';
					RSFormProHelper::addOnClick($data['ADDITIONALATTRIBUTES3'], $onclick);
					
					$out .= '<input type="button" value="'.RSFormProHelper::htmlEscape($data['PREVBUTTON']).'"  id="'.$data['NAME'].'Prev" '.$data['ADDITIONALATTRIBUTES3'].' />';
				}
				
				$out .= '<input type="submit" value="'.RSFormProHelper::htmlEscape($data['LABEL']).'" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.$data['ADDITIONALATTRIBUTES'].' />';
				if ($data['RESET']=='YES')
				{
					$className = 'rsform-reset-button';
					RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES2'], $className);
					
					$out .= '&nbsp;&nbsp;<input type="reset" value="'.RSFormProHelper::htmlEscape($data['RESETLABEL']).'" name="form['.$data['NAME'].']" '.$data['ADDITIONALATTRIBUTES2'].' />';
				}
			break;
			
			case 14:
			case 'password':
				$defaultValue = '';
				if ($data['VALIDATIONRULE'] != 'password')
					$defaultValue = $data['DEFAULTVALUE'];
				
				$className = 'rsform-password-box';
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$out .= '<input type="password" value="'.RSFormProHelper::htmlEscape($defaultValue).'" size="'.(int) $data['SIZE'].'" name="form['.$data['NAME'].']" id="'.$data['NAME'].'" '.((int) $data['MAXSIZE'] > 0 ? 'maxlength="'.(int) $data['MAXSIZE'].'"' : '').' '.$data['ADDITIONALATTRIBUTES'].' />';
			break;
			
			case 15:
			case 'ticket':
				$out .= '<input type="hidden" name="form['.$data['NAME'].']" value="'.RSFormProHelper::generateString($data['LENGTH'],$data['CHARACTERS']).'" '.$data['ADDITIONALATTRIBUTES'].' />';
			break;
			
			case 41:
			case 'pageBreak':
				$validate = 'false';
				if (isset($data['VALIDATENEXTPAGE']) && $data['VALIDATENEXTPAGE'] == 'YES')
					$validate = 'true';
				
				$className = 'rsform-button';
				if ($invalid)
					$className .= ' rsform-error';
				RSFormProHelper::addClass($data['ADDITIONALATTRIBUTES'], $className);
				
				$data['ADDITIONALATTRIBUTES2'] = $data['ADDITIONALATTRIBUTES'];
				
				$num = count($data['PAGES']);
				$pos = array_search($componentId, $data['PAGES']);
				if ($pos)
				{
					$onclick = 'rsfp_changePage('.$formId.', '.($pos-1).', '.$num.')';
					RSFormProHelper::addOnClick($data['ADDITIONALATTRIBUTES'], $onclick);
					
					$out .= '<input type="button" value="'.RSFormProHelper::htmlEscape($data['PREVBUTTON']).'" '.$data['ADDITIONALATTRIBUTES'].' id="'.$data['NAME'].'Prev" />';
				}
				
				if ($pos < count($data['PAGES']))
				{
					$onclick = 'rsfp_changePage('.$formId.', '.($pos+1).', '.$num.', '.$validate.')';
					RSFormProHelper::addOnClick($data['ADDITIONALATTRIBUTES2'], $onclick);
					
					$out .= '<input type="button" value="'.RSFormProHelper::htmlEscape($data['NEXTBUTTON']).'" '.$data['ADDITIONALATTRIBUTES2'].' id="'.$data['NAME'].'Next" />';
				}
			break;
		}
		
		//Trigger Event - rsfp_bk_onAfterCreateFrontComponentBody
		$mainframe->triggerEvent('rsfp_bk_onAfterCreateFrontComponentBody',array(array('out'=>&$out, 'formId'=>$formId, 'componentId'=>$componentId,'data'=>$data,'value'=>$value,'r'=>$r, 'invalid' => $invalid)));
		return $out;
	}
	
	function addClass(&$attributes, $className)
	{
		if (preg_match('#class="(.*?)"#is', $attributes, $matches))
			$attributes = str_replace($matches[0], str_replace($matches[1], $matches[1].' '.$className, $matches[0]), $attributes);
		else
			$attributes .= ' class="'.$className.'"';
		
		return $attributes;
	}
	
	function addOnClick(&$attributes, $onClick)
	{
		if (preg_match('#onclick="(.*?)"#is', $attributes, $matches))
			$attributes = str_replace($matches[0], str_replace($matches[1], $matches[1].'; '.$onClick, $matches[0]), $attributes);
		else
			$attributes .= ' onclick="'.$onClick.'"';
		
		return $attributes;
	}
	
	function getInvisibleCaptchaWords()
	{
		return array('Website', 'Email', 'Name', 'Address', 'User', 'Username', 'Comment', 'Message');
	}
	
	function generateString($length, $characters, $type='Random')
	{
		$length = (int) $length;
		if($type == 'Random')
		{
			switch($characters)
			{
				case 'ALPHANUMERIC':
				default:
					$possible = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
				case 'ALPHA':
					$possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
				case 'NUMERIC':
					$possible = '0123456789';
				break;
			}

			if($length<1||$length>255) $length = 8;
			  $key = '';
			  $i = 0;
			  while ($i < $length) {
				$key .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
				$i++;
			  }
		}
		if($type == 'Sequential')
		{
			$key = 0;
		}
		return $key;
	}
	
	// todo - use Joomla! string functions
	// optimize to ignore false alerts
	function stripJava($val)
	{
	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	   // this prevents some character re-spacing such as <java\0script>
	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	   $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

	   // straight replacements, the user should never need these since they're normal characters
	   // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
		  // ;? matches the ;, which is optional
		  // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

		  // &#x0040 @ search for the hex values
		  $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
		  // &#00064 @ 0{0,7} matches '0' zero to seven times
		  $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	   }

	   // now the only remaining whitespace attacks are \t, \n, and \r
	   // ([ \t\r\n]+)?
	   $ra1 = Array('\/([ \t\r\n]+)?javascript', '\/([ \t\r\n]+)?vbscript', ':([ \t\r\n]+)?expression', '<([ \t\r\n]+)?applet', '<([ \t\r\n]+)?meta', '<([ \t\r\n]+)?xml', '<([ \t\r\n]+)?blink', '<([ \t\r\n]+)?link', '<([ \t\r\n]+)?style', '<([ \t\r\n]+)?script', '<([ \t\r\n]+)?embed', '<([ \t\r\n]+)?object', '<([ \t\r\n]+)?iframe', '<([ \t\r\n]+)?frame', '<([ \t\r\n]+)?frameset', '<([ \t\r\n]+)?ilayer', '<([ \t\r\n]+)?layer', '<([ \t\r\n]+)?bgsound', '<([ \t\r\n]+)?title', '<([ \t\r\n]+)?base');
	   $ra2 = Array('onabort([ \t\r\n]+)?=', 'onactivate([ \t\r\n]+)?=', 'onafterprint([ \t\r\n]+)?=', 'onafterupdate([ \t\r\n]+)?=', 'onbeforeactivate([ \t\r\n]+)?=', 'onbeforecopy([ \t\r\n]+)?=', 'onbeforecut([ \t\r\n]+)?=', 'onbeforedeactivate([ \t\r\n]+)?=', 'onbeforeeditfocus([ \t\r\n]+)?=', 'onbeforepaste([ \t\r\n]+)?=', 'onbeforeprint([ \t\r\n]+)?=', 'onbeforeunload([ \t\r\n]+)?=', 'onbeforeupdate([ \t\r\n]+)?=', 'onblur([ \t\r\n]+)?=', 'onbounce([ \t\r\n]+)?=', 'oncellchange([ \t\r\n]+)?=', 'onchange([ \t\r\n]+)?=', 'onclick([ \t\r\n]+)?=', 'oncontextmenu([ \t\r\n]+)?=', 'oncontrolselect([ \t\r\n]+)?=', 'oncopy([ \t\r\n]+)?=', 'oncut([ \t\r\n]+)?=', 'ondataavailable([ \t\r\n]+)?=', 'ondatasetchanged([ \t\r\n]+)?=', 'ondatasetcomplete([ \t\r\n]+)?=', 'ondblclick([ \t\r\n]+)?=', 'ondeactivate([ \t\r\n]+)?=', 'ondrag([ \t\r\n]+)?=', 'ondragend([ \t\r\n]+)?=', 'ondragenter([ \t\r\n]+)?=', 'ondragleave([ \t\r\n]+)?=', 'ondragover([ \t\r\n]+)?=', 'ondragstart([ \t\r\n]+)?=', 'ondrop([ \t\r\n]+)?=', 'onerror([ \t\r\n]+)?=', 'onerrorupdate([ \t\r\n]+)?=', 'onfilterchange([ \t\r\n]+)?=', 'onfinish([ \t\r\n]+)?=', 'onfocus([ \t\r\n]+)?=', 'onfocusin([ \t\r\n]+)?=', 'onfocusout([ \t\r\n]+)?=', 'onhelp([ \t\r\n]+)?=', 'onkeydown([ \t\r\n]+)?=', 'onkeypress([ \t\r\n]+)?=', 'onkeyup([ \t\r\n]+)?=', 'onlayoutcomplete([ \t\r\n]+)?=', 'onload([ \t\r\n]+)?=', 'onlosecapture([ \t\r\n]+)?=', 'onmousedown([ \t\r\n]+)?=', 'onmouseenter([ \t\r\n]+)?=', 'onmouseleave([ \t\r\n]+)?=', 'onmousemove([ \t\r\n]+)?=', 'onmouseout([ \t\r\n]+)?=', 'onmouseover([ \t\r\n]+)?=', 'onmouseup([ \t\r\n]+)?=', 'onmousewheel([ \t\r\n]+)?=', 'onmove([ \t\r\n]+)?=', 'onmoveend([ \t\r\n]+)?=', 'onmovestart([ \t\r\n]+)?=', 'onpaste([ \t\r\n]+)?=', 'onpropertychange([ \t\r\n]+)?=', 'onreadystatechange([ \t\r\n]+)?=', 'onreset([ \t\r\n]+)?=', 'onresize([ \t\r\n]+)?=', 'onresizeend([ \t\r\n]+)?=', 'onresizestart([ \t\r\n]+)?=', 'onrowenter([ \t\r\n]+)?=', 'onrowexit([ \t\r\n]+)?=', 'onrowsdelete([ \t\r\n]+)?=', 'onrowsinserted([ \t\r\n]+)?=', 'onscroll([ \t\r\n]+)?=', 'onselect([ \t\r\n]+)?=', 'onselectionchange([ \t\r\n]+)?=', 'onselectstart([ \t\r\n]+)?=', 'onstart([ \t\r\n]+)?=', 'onstop([ \t\r\n]+)?=', 'onsubmit([ \t\r\n]+)?=', 'onunload([ \t\r\n]+)?=', 'style([ \t\r\n]+)?=');
	   $ra = array_merge($ra1, $ra2);
	   
		foreach ($ra as $tag)
		{
			$pattern = '#'.$tag.'#i';
			preg_match_all($pattern, $val, $matches);
			
			foreach ($matches[0] as $match)
				$val = str_replace($match, substr($match, 0, 2).'<x>'.substr($match, 2), $val);
		}
	   
	   return $val;
	}
	
	function getCalendarJS()
	{
		$out = '//CALENDAR SETUP'."\n";
		
		$m_short = $m_long = array();
		for ($i=1; $i<=12; $i++)
		{
			$m_short[] = '"'.JText::_('RSFP_CALENDAR_MONTHS_SHORT_'.$i, true).'"';
			$m_long[] = '"'.JText::_('RSFP_CALENDAR_MONTHS_LONG_'.$i, true).'"';
		}
		$w_1 = $w_short = $w_med = $w_long = array();
		for ($i=0; $i<=6; $i++)
		{
			$w_1[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_1CHAR_'.$i, true).'"';
			$w_short[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_SHORT_'.$i, true).'"';
			$w_med[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_MEDIUM_'.$i, true).'"';
			$w_long[] = '"'.JText::_('RSFP_CALENDAR_WEEKDAYS_LONG_'.$i, true).'"';
		}
		
		$out .= 'var MONTHS_SHORT 	 = Array('.implode(',', $m_short).');'."\n";
		$out .= 'var MONTHS_LONG 	 = Array('.implode(',', $m_long).');'."\n";
		$out .= 'var WEEKDAYS_1CHAR  = Array('.implode(',', $w_1).');'."\n";
		$out .= 'var WEEKDAYS_SHORT  = Array('.implode(',', $w_short).');'."\n";
		$out .= 'var WEEKDAYS_MEDIUM = Array('.implode(',', $w_med).');'."\n";
		$out .= 'var WEEKDAYS_LONG 	 = Array('.implode(',', $w_long).');'."\n";
		$out .= 'var START_WEEKDAY 	 = '.JText::_('RSFP_CALENDAR_START_WEEKDAY').';';
		
		return $out;
	}
}
?>