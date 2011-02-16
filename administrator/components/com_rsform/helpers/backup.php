<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProBackup
{
	function create($formIds, $submissions, $filename)
	{
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();

		$config = JFactory::getConfig();
		
		$xml  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$xml .= '<RSinstall type="rsformbackup">'."\n";
		$xml .= '<name>RSform backup</name>'."\n";
		$xml .= '<creationDate>'.date('Y-m-d').'</creationDate>'."\n";
		$xml .= '<author>'.$user->username.'</author>'."\n";
		$xml .= '<copyright>(C) '.date('Y').' '.JURI::root().'</copyright>'."\n";
		$xml .= '<authorEmail>'.$config->getValue('config.mailfrom').'</authorEmail>'."\n";
		$xml .= '<authorUrl>'.JURI::root().'</authorUrl>'."\n";
		$xml .= '<version>'._RSFORM_VERSION.'</version>'."\n";
		$xml .= '<revision>'._RSFORM_REVISION.'</revision>'."\n";
		$xml .= '<description>RSForm! Pro Backup</description>'."\n";
		$xml .= '<tasks>'."\n";

		//LOAD FORMS
		$db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId IN ('".implode("','",$formIds)."') ORDER BY FormId");
		$form_rows = $db->loadObjectList();
		foreach($form_rows as $form_row)
		{
			$xml .= RSFormProBackup::createXMLEntry('#__rsform_forms',$form_row,'FormId')."\n";
			$xml .= "\t".'<task type="eval" source="">$GLOBALS[\'q_FormId\'] = $db->insertid();</task>'."\n";
			 
			 //LOAD COMPONENTS
			$db->setQuery("SELECT * FROM #__rsform_components WHERE FormId = '".$form_row->FormId."'");
			$component_rows = $db->loadObjectList();
			foreach($component_rows as $component_row)
			{
				$xml .= RSFormProBackup::createXMLEntry('#__rsform_components',$component_row,'ComponentId','FormId')."\n";
				$xml .= "\t".'<task type="eval" source="">$GLOBALS[\'q_ComponentId\'] = $db->insertid();</task>'."\n";
				 
				//LOAD PROPERTIES
				$db->setQuery("SELECT * FROM #__rsform_properties WHERE ComponentId = '".$component_row->ComponentId."'");
				$property_rows = $db->loadObjectList();
				foreach($property_rows as $property_row)
					$xml .= RSFormProBackup::createXMLEntry('#__rsform_properties',$property_row,'PropertyId','ComponentId')."\n";
			}
			
			if($submissions)
			{
				//LOAD SUBMISSIONS
				$db->setQuery("SELECT * FROM #__rsform_submissions WHERE FormId = '".$form_row->FormId."'");
				$submission_rows = $db->loadObjectList();
				foreach($submission_rows as $submission_row)
				{
					$xml .= RSFormProBackup::createXMLEntry('#__rsform_submissions',$submission_row,'SubmissionId','FormId')."\n";
					$xml .= "\t".'<task type="eval" source="">$GLOBALS[\'q_SubmissionId\'] = $db->insertid();</task>'."\n";
	 
					//LOAD SUBMISSION_VALUES
					$db->setQuery("SELECT * FROM #__rsform_submission_values WHERE SubmissionId = '".$submission_row->SubmissionId."'");
					$submission_value_rows = $db->loadObjectList();
					foreach($submission_value_rows as $submission_value_row)
						$xml .= RSFormProBackup::createXMLEntry('#__rsform_submission_values',$submission_value_row,'SubmissionValueId',array('SubmissionId', 'FormId'))."\n";
				}
			}
		}
		
		$xml .= '</tasks>'."\n";
		$xml .= '</RSinstall>';
		
		jimport('joomla.filesystem.file');
		return JFile::write($filename, $xml);
	}
	
	function createXMLEntry($tb_name, $row, $exclude = null, $dynamic = null)
	{
		$fields = array();
		$values = array();

		$db = JFactory::getDBO();
		
		foreach($row as $k=>$v)
		{
			$fields[] = '`' . $k . '`';
			if($k == $exclude) $v = "";
			if (is_array($dynamic))
			{
				if (in_array($k, $dynamic))
					$v = "{".$dynamic[array_search($k, $dynamic)]."}";
			}
			else
				if($k == $dynamic) $v = "{".$dynamic."}";
			$values[] = "'" . $db->getEscaped($v) . "'";
		}

		$xml = 'INSERT INTO `' . $tb_name . '` (' . implode(',',$fields) . ') VALUES (' . implode(',',$values) . ' )';

		return "\t".'<task type="query"><![CDATA['.$xml.']]></task>';
	}
}
?>