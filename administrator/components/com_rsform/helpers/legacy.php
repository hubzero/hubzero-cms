<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSAdapter
{
	var $jconfig = null;
	var $config = null;
	
	function RSAdapter()
	{
		// Get Joomla! Configuration
		$this->jconfig = JFactory::getConfig();

		// Define tables
		$prefix = $this->jconfig->getValue('config.dbprefix');
        $this->tbl_rsform_config                    = $prefix.'rsform_config';
        $this->tbl_rsform_components                = $prefix.'rsform_components';
        $this->tbl_rsform_component_types           = $prefix.'rsform_component_types';
        $this->tbl_rsform_component_type_fields     = $prefix.'rsform_component_type_fields';
        $this->tbl_rsform_forms                     = $prefix.'rsform_forms';
        $this->tbl_rsform_mappings                  = $prefix.'rsform_mappings';
        $this->tbl_rsform_properties                = $prefix.'rsform_properties';
        $this->tbl_rsform_submissions               = $prefix.'rsform_submissions';
        $this->tbl_rsform_submission_values         = $prefix.'rsform_submission_values';
        $this->tbl_users				         	= $prefix.'users';
	
		// Build old config
		$this->config = array();
		RSFormProHelper::readConfig();
		$config = RSFormProHelper::getConfig(null);
		foreach ($config as $item => $value)
			$this->config[$item] = $value;
		
		$this->config['list_limit'] 	= $this->jconfig->getValue('config.list_limit');
        $this->config['absolute_path']  = JPATH_SITE;
        $this->config['live_site'] 		= JURI :: root();
        $this->config['mail_from'] 		= $this->jconfig->getValue('config.mailfrom');
        $this->config['sitename'] 		= $this->jconfig->getValue('config.sitename');
        $this->config['dbprefix'] 		= $prefix;
        $this->config['db'] 			= $this->jconfig->getValue('config.db');
        $this->config['component_ids']  = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);
		$this->config['absolute_path']  = JPATH_SITE;
	}
	
	function calendar($value, $name, $id, $format)
	{
		JHTML::calendar($value, $name, $id, $format);
	}
	
	function user($id = null)
	{
		$user = JFactory::getUser($id);
		$return = array('id'=> $user->get('id'),'username'=> $user->get('username'),'fullname'=> $user->get('name'), 'email'=> $user->get('email'));
		return $return;
	}
	
	function getParam($array, $name, $default_value = null)
	{
		return isset($array[$name]) ? $array[$name] : $default_value;
	}
	
	function redirect($url, $msg=null)
	{
		$mainframe =& JFactory::getApplication();
    	$mainframe->redirect($url, $msg);
	}
	
	function mail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = null, $bcc = null, $attachment = null, $replyto='')
    {
		$replyto = $replyto != '' ? $replyto : $from;
    	JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto);
    }
	
	function registerEvent($event, $function)
    {
    	$mainframe =& JFactory::getApplication();
    	$mainframe->registerEvent( $event, $function );
    }
	
	function getMenuParam($var,$default)
    {
		if ($formId = JRequest::getInt('formId'))
			return $formId;
    	
		$config =& JComponentHelper::getParams( 'com_rsform' );
		return $config->get($var);
    }
	
	// Menu
    function menuStartTable()
    {
    	
    }
    
    function menuSpacer()
    {
    	JToolBarHelper::spacer();
    }
        
    function menuCustom($task,$icon,$iconOver,$alt,$listSelect=true, $onclick='')
    {
    	if($onclick=='')JToolBarHelper::custom($task,$icon,$iconOver,$alt,$listSelect);
    	else JToolBarHelper::custom($task."');".$onclick.";void('",$icon,$iconOver,$alt,$listSelect);
    }
    
    function menuCancel($task = 'cancel', $alt = 'Cancel')
    {
    	JToolBarHelper::cancel($task, $alt);
    }
    
    function menuBack($alt = 'Back', $href = '')
    {
    	JToolBarHelper::back($alt, $href);
    }
    
    function menuAddNewX($task = 'new', $alt = 'New')
    {
    	JToolBarHelper::addNewX($task, $alt);
    }
    
    function menuDeleteList($msg = '',$task = 'remove',$alt = 'Delete')
    {
    	JToolBarHelper::deleteList($msg, $task, $alt);
    }
    
    function menuSave($task='save',$alt='Save')
    {
    	JToolBarHelper::save($task, $alt);
    }
    
    function menuApply($task='apply',$alt='Apply')
    {
    	JToolBarHelper::apply($task, $alt);
    }
    
    function menuPublishList($task='publish',$alt='Publish')
    {
    	JToolBarHelper::publishList($task, $alt);
    }
    
    function menuUnpublishList($task='unpublish',$alt='Unpublish')
    {
    	JToolBarHelper::unpublishList($task, $alt);
    }
    
    function menuEndTable()
	{
    	
    }
    
	// Tabs
    function initTabs($number)
    {
    	jimport('joomla.html.pane');
    	$this->tabs = & JPane::getInstance('Tabs',array(),true);
    }
	
    function startPane($id)
    {
    	$this->tabs = & JPane::getInstance('Tabs',array(),true);
    	echo $this->tabs->startPane($id);
    }
	
    function startTab($title, $id)
    {
    	$this->tabs = & JPane::getInstance('Tabs',array(),true);
    	echo $this->tabs->startPanel($title, $id);
    }
	
    function endTab()
    {
    	$this->tabs = & JPane::getInstance('Tabs',array(),true);
    	echo $this->tabs->endPanel();
    }
	
    function endPane()
    {
    	$this->tabs = & JPane::getInstance('Tabs',array(),true);
    	echo $this->tabs->endPane();
    }
	
	// JS, CSS
	
	function addHeadTag($str, $type, $destination = 'head')
    {
    	$document =& JFactory::getDocument();
    	switch($type)
    	{
    		case 'css':
    			$document->addStyleSheet($str);	
    		break;
    		case 'js':
    			$document->addScript($str);
    		break;
    	}
    }
	
	// WYSIWYG
	function WYSIWYG($name, $content, $hiddenField, $width, $height, $col, $row)
	{
		return RSFormProHelper::WYSIWYG($name, $content, $hiddenField, $width, $height, $col, $row);
	}
}

// Global product constants                        
define('_RSFORM_BACKEND_ABS_PATH', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform');
define('_RSFORM_BACKEND_REL_PATH', JURI::root(true).'/administrator/components/com_rsform');

define('_RSFORM_FRONTEND_ABS_PATH', JPATH_SITE.DS.'components'.DS.'com_rsform');
define('_RSFORM_FRONTEND_REL_PATH', JURI::root(true).'/components/com_rsform');

// Global script constants
define('_RSFORM_BACKEND_SCRIPT_PATH', JURI::root().'administrator/index.php');
define('_RSFORM_FRONTEND_SCRIPT_PATH', JURI::root(true).'/index.php');

// Other paths
define('_RSFORM_JOOMLA_XML_PATH', JPATH_SITE.DS.'libraries'.DS.'domit'.DS.'xml_domit_lite_include.php');

// Joomla! 1.5 Language
$lg = &JFactory::getLanguage();
// Not used anymore
$backwardLang = 'default';
	
DEFINE('_RSFORM_BACKEND_LANGUAGE', $backwardLang);
DEFINE('_RSFORM_FRONTEND_LANGUAGE', $backwardLang);

function RSshowComponentName($componentId)
{
	$db = JFactory::getDBO();
	$db->setQuery("SELECT PropertyValue FROM #__rsform_properties WHERE ComponentId='".(int) $componentId."' AND PropertyName='NAME'");
	return '<td>'.$db->loadResult().'</td>';
}

function RSgetComponentProperties($componentId)
{
	return RSFormProHelper::getComponentProperties($componentId);
}

function RSpreviewComponent($formId, $componentId)
{
	$data = RSFormProHelper::getComponentProperties($componentId);
	return RSFormProHelper::showPreview($formId, $componentId, $data);
}

function RSresolveComponentName($componentName, $formId)
{
	$db = JFactory::getDBO();
	$db->setQuery("SELECT p.ComponentId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON c.ComponentId = p.ComponentId WHERE p.PropertyValue='".$db->getEscaped($componentName)."' AND p.PropertyName='NAME' and c.FormId='".(int) $formId."'");
	
	return $db->loadResult();
}

function RSfrontComponentCaption($componentId, $data=null)
{		
	if (isset($data['SHOW']) && $data['SHOW'] == 'NO') return '';
	
	$db = JFactory::getDBO();
	$db->setQuery("SELECT `PropertyValue` FROM #__rsform_properties WHERE `ComponentId`='".(int) $componentId."' AND PropertyName='CAPTION'");
	return $db->loadResult();
}

function RSfrontComponentDescription($componentId, $data)
{
	if(isset($data['SHOW']) && $data['SHOW'] == 'NO') return '';
	
	$db = JFactory::getDBO();
	$db->setQuery("SELECT `PropertyValue` FROM #__rsform_properties WHERE `ComponentId`='".(int) $componentId."' AND PropertyName='DESCRIPTION'");
	return $db->loadResult();
}

function RSfrontComponentValidationMessage($componentId, $data, $value='')
{
	if(isset($data['SHOW']) && $data['SHOW'] == 'NO') return '';
	
	$db = JFactory::getDBO();
	$db->setQuery("SELECT `PropertyValue` FROM #__rsform_properties WHERE ComponentId='".(int) $componentId."' AND PropertyName='VALIDATIONMESSAGE'");
	
	$msg = $db->loadResult();
	
	if(!empty($value) && in_array($componentId,$value))
		return '<span id="component'.$componentId.'" class="formError">'.$msg.'</span>';
	else
		return '<span id="component'.$componentId.'" class="formNoError">'.$msg.'</span>';
}

function RSfrontComponentBody($formId, $componentId, $data, $value='')
{
	return RSFormProHelper::getFrontComponentBody($formId, $componentId, $data, $value);
}

function RSshowForm($formId,$val='',$validation='')
{
	return RSFormProHelper::showForm($formId, $val, $validation);
}

function RSfrontLayout($formId, $formLayout)
{
	
}

function RSshowThankyouMessage($formId)
{
	
}

function RSprocessForm($formId)
{
	return RSFormProHelper::processForm($formId);
}

function RSgetSubmissionValue($SubmissionId, $ComponentId)
{
	$db = JFactory::getDBO();
	$data = RSFormProHelper::getComponentProperties($ComponentId);
	
	$db->setQuery("SELECT `FieldValue` FROM #__rsform_submission_values WHERE FieldName = '".$data['NAME']."' AND SubmissionId = '".(int) $SubmissionId."'");
	return $db->loadResult();
}

function RScleanVar($string,$html=false)
{
	$db = JFactory::getDBO();
	$string = $html ? htmlentities($string,ENT_COMPAT,'UTF-8') : $string;
	$string = get_magic_quotes_gpc() ? $db->getEscaped(stripslashes($string)) : $db->getEscaped($string);
	return $string;
}

function RSshowVar($string)
{
	return htmlspecialchars($string,ENT_COMPAT,'UTF-8');
}

function RSstripVar($string)
{
	$string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
	return $string;
}

function RSstripjavaVar($val)
{
	return RSFormProHelper::stripJava($val);
}

function RSgetValidationRule($componentId)
{
	$db = JFactory::getDBO();
	$db->setQuery("SELECT PropertyValue FROM #__rsform_properties WHERE PropertyName='VALIDATIONRULE' and ComponentId='".(int) $componentId."'");
	return $db->loadResult();
}

function RSgetRequired($value,$formId)
{
	$componentId = RSresolveComponentName($value,$formId);
	
	$db = JFactory::getDBO();
	$db->setQuery("SELECT PropertyValue FROM #__rsform_properties WHERE PropertyName='REQUIRED' AND ComponentId='".$componentId."'");
	return $db->loadResult();
}

function RSvalidateForm($formId)
{
	return RSFormProHelper::validateForm($formId);
}

function RSgetComponentTypeId($componentId)
{
	$db = JFactory::getDBO();
	$db->setQuery("SELECT ComponentTypeId FROM #__rsform_components WHERE ComponentId='".(int) $componentId."'");
	return $db->loadResult();
}

function RSresolveComponentTypeId($componentTypeId)
{
	$db = JFactory::getDBO();
	$db->setQuery("SELECT ComponentTypeName FROM #__rsform_component_types WHERE ComponentTypeId='".(int) $componentTypeId."'");
	return $db->loadResult();
}

function RSgetComponentTypeIdByName($componentName,$formId)
{
	$db = JFactory::getDBO();	
	$db->setQuery("SELECT #__rsform_components.ComponentTypeId FROM #__rsform_components LEFT JOIN #__rsform_properties ON #__rsform_properties.ComponentId = #__rsform_components.ComponentId WHERE #__rsform_properties.PropertyName='NAME' AND #__rsform_properties.PropertyValue='".$db->getEscaped($componentName)."' AND #__rsform_components.FormId='".(int) $formId."'");
	return $db->loadResult();
}

function RSgetFileDestination($componentName,$formId)
{
	$componentId = RSresolveComponentName($componentName, $formId);
		
	$db = JFactory::getDBO();
	$db->setQuery("SELECT PropertyValue FROM #__rsform_properties WHERE PropertyName='DESTINATION' AND ComponentId='".$componentId."'");
	return $db->loadResult();
}

function RScomponentExists($formId,$componentTypeId)
{
	return RSFormProHelper::componentExists($formId, $componentTypeId);
}

function RSgenerateString($length, $characters, $type='Random')
{
	return RSFormProHelper::generateString($length, $characters, $type);
}

function RSprocessField($result,$submissionId)
{
	
}

function RSgetSubmission($SubmissionId)
{
	$db = JFactory::getDBO();
	
	// Get submission 
	$db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId = '".(int) $SubmissionId."'");
	$Submission = $db->loadObject();
	
	// Get submission details
	$db->setQuery("SELECT * FROM #__rsform_submission_values WHERE SubmissionId = '".(int) $SubmissionId."'");
	$Submission->values = $db->loadObjectList();
	
	return $Submission;
}

// 1.5 ready
function RSgetFormLayoutName($formId)
{
	$db = JFactory::getDBO();
	$db->setQuery("SELECT FormLayoutName FROM #__rsform_forms WHERE FormId='".(int) $formId."'");
	return $db->loadResult();
}
?>