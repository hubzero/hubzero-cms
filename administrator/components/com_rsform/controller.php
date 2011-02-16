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
		
		if (!headers_sent())
			header('Content-type: text/html; charset=utf-8');
		
		$this->_db = JFactory::getDBO();
		
		$doc =& JFactory::getDocument();
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/jquery.js');
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/tablednd.js');
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/jquery.scrollto.js');
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/script.js');
		
		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/style.css');
		if (RSFormProHelper::isJ16())
			$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/style16.css');
		
		$this->registerTask('formsApply', 'formsSave');
		
		$this->registerTask('formsPublish', 'formsChangeStatus');
		$this->registerTask('formsUnpublish', 'formsChangeStatus');
		
		$this->registerTask('componentsPublish', 'componentsChangeStatus');
		$this->registerTask('componentsUnpublish', 'componentsChangeStatus');
		
		$this->registerTask('configurationApply', 'configurationSave');
		
		$this->registerTask('submissionsExportCSV', 'submissionsExport');
		$this->registerTask('submissionsExportExcel', 'submissionsExport');
		$this->registerTask('submissionsExportXML', 'submissionsExport');
		
		$this->registerTask('submissionsApply', 'submissionsSave');
		$this->registerTask('submissionsSave', 'submissionsSave');
		
		$this->registerTask('richtextApply', 'richtextSave');
	}
	
	function display()
	{
		parent::display();
	}
	
	function richtextShow()
	{
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'richtext');
		
		parent::display();
	}
	
	function richtextSave()
	{
		$formId = JRequest::getInt('formId');
		$opener = JRequest::getCmd('opener');
		$value = JRequest::getVar($opener, '', 'post', 'none', JREQUEST_ALLOWRAW);
		
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE #__rsform_forms SET `".$opener."`='".$db->getEscaped($value)."' WHERE FormId='".$formId."'");
		$db->query();
		
		if ($this->getTask() == 'richtextapply')
			return $this->setRedirect('index.php?option=com_rsform&task=richtext.show&opener='.$opener.'&formId='.$formId.'&tmpl=component');
		
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration("window.close();");
	}
	
	function richtextPreview()
	{
		$formId = JRequest::getInt('formId');
		$opener = JRequest::getCmd('opener');
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `".$opener."` FROM #__rsform_forms WHERE FormId='".$formId."'");
		echo $db->loadResult();
	}
	
	/**
	* @desc Forms Manager Screen
	*/
	function formsManage()
	{
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}

	/**
	 * Forms Menu Add Screen
	 */
	function formsMenuaddScreen()
	{
		JRequest::setVar('view', 'menus');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}

	/**
	 * @desc Forms Publish/Unpublish Process
	 */
	function formsChangeStatus()
	{
		$task = JRequest::getWord('task');
		$task = strtolower($task);

		$db = JFactory::getDBO();
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$value = $task == 'formspublish' ? 1 : 0;
		
		$total = count($cid);
		if ($total > 0)
		{
			$formIds = implode(',', $cid);
			$db->setQuery("UPDATE #__rsform_forms SET Published = '".$value."' WHERE FormId IN (".$formIds.")");
			$db->query();
		}
		
		$msg = $value ? JText::sprintf('RSFP_FORMS_PUBLISHED', $total) : JText::sprintf('RSFP_FORMS_UNPUBLISHED', $total);

		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', $msg);
	}

	/**
	 * Forms Copy Process
	 */
	function formsCopy()
	{
		$db = JFactory::getDBO();
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$total = 0;
		foreach ($cid as $formId)
		{
			if (empty($formId))
				continue;
				
			$total++;
			
			$original =& JTable::getInstance('RSForm_Forms', 'Table');
			$original->load($formId);
			$original->FormName .= ' copy';
			$original->FormTitle .= ' copy';
			$original->FormId = null;
			
			$copy =& JTable::getInstance('RSForm_Forms', 'Table');
			$copy->bind($original);
			$copy->store();
			
			$copy->FormLayout = str_replace('rsform_'.$formId.'_page', 'rsform_'.$copy->FormId.'_page', $copy->FormLayout);
			if ($copy->FormLayout != $original->FormLayout)
				$copy->store();
			
			$newFormId = $copy->FormId;
			
			$db->setQuery("SELECT * FROM #__rsform_components WHERE FormId='".$formId."'");
			$components = $db->loadAssocList();
			foreach ($components as $r)
			{
				$db->setQuery("INSERT INTO #__rsform_components SET `FormId`='".$newFormId."', `ComponentTypeId`='".$r['ComponentTypeId']."', `Order`='".$r['Order']."'");
				$db->query();
				$newComponentId = $db->insertid();

				$db->setQuery("SELECT * FROM #__rsform_properties WHERE ComponentId='".$r['ComponentId']."'");
				$properties = $db->loadAssocList();
				foreach ($properties as $p)
				{
					$db->setQuery("INSERT INTO #__rsform_properties SET PropertyName='".$db->getEscaped($p['PropertyName'])."', PropertyValue='".$db->getEscaped($p['PropertyValue'])."', ComponentId='".$newComponentId."'");
					$db->query();
				}
			}
		}
		
		$msg = JText::sprintf('RSFP_FORMS_COPIED', $total);
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', $msg);
	}
	
	/**
	 * Forms Delete Process
	 */
	function formsDelete()
	{
		$db = JFactory::getDBO();
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$model = $this->getModel('submissions');
		
		$total = count($cid);
		foreach($cid as $formId)
		{
			$model->deleteSubmissionFiles($formId);
			$model->deleteSubmissions($formId);

			//Delete Components
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId = '".$formId."'");
			$componentIds = $db->loadResultArray();
			
			if (!empty($componentIds))
			{
				$components = implode(',',$componentIds);
				$db->setQuery("DELETE FROM #__rsform_properties WHERE ComponentId IN (".$components.")");
				$db->query();
				$db->setQuery("DELETE FROM #__rsform_components WHERE ComponentId IN (".$components.")");
				$db->query();
			}

			//Delete Forms
			$db->setQuery("DELETE FROM #__rsform_forms WHERE FormId = '".$formId."'");
			$db->query();
		}
		
		$msg = JText::sprintf('RSFP_FORMS_DELETED', $total);
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', $msg);
	}

	/**
	 * Forms Edit Screen
	 */
	function formsEdit()
	{
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'edit');
		
		parent::display();
	}
	
	function formsNew()
	{
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'new');
		
		parent::display();
	}
	
	function formsNewStepTwo()
	{
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'new2');
		
		parent::display();
	}
	
	function formsNewStepThree()
	{
		$session = JFactory::getSession();
		$session->set('com_rsform.wizard.FormTitle', JRequest::getVar('FormTitle', '', 'post', 'none', JREQUEST_ALLOWRAW));
		$session->set('com_rsform.wizard.FormLayout', JRequest::getVar('FormLayout', '', 'post', 'none', JREQUEST_ALLOWRAW));
		$session->set('com_rsform.wizard.AdminEmail', JRequest::getInt('AdminEmail'));
		$session->set('com_rsform.wizard.AdminEmailTo', JRequest::getVar('AdminEmailTo', '', 'post', 'none', JREQUEST_ALLOWRAW));
		$session->set('com_rsform.wizard.UserEmail', JRequest::getInt('UserEmail'));
		$session->set('com_rsform.wizard.SubmissionAction', JRequest::getVar('SubmissionAction', '', 'post', 'word'));
		$session->set('com_rsform.wizard.Thankyou', JRequest::getVar('Thankyou', '', 'post', 'none', JREQUEST_ALLOWRAW));
		$session->set('com_rsform.wizard.ReturnUrl', JRequest::getVar('ReturnUrl', '', 'post', 'none', JREQUEST_ALLOWRAW));
		
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'new3');
		
		parent::display();
	}
	
	function formsNewStepFinal()
	{
		$session = JFactory::getSession();
		$config = JFactory::getConfig();
		
		$row = JTable::getInstance('RSForm_Forms', 'Table');
		$row->FormTitle = $session->get('com_rsform.wizard.FormTitle');
		if (empty($row->FormTitle))
			$row->FormTitle = JText::_('RSFP_FORM_DEFAULT_TITLE');
		$row->FormName = JFilterOutput::stringURLSafe($row->FormTitle);
		$row->FormLayout = $session->get('com_rsform.wizard.FormLayout');
		if (empty($row->FormLayout))
			$row->FormLayout = 'inline';
		
		$AdminEmail = $session->get('com_rsform.wizard.AdminEmail');
		if ($AdminEmail)
		{
			$row->AdminEmailTo = $session->get('com_rsform.wizard.AdminEmailTo');
			$row->AdminEmailFrom = $config->getValue('config.mailfrom');
			$row->AdminEmailFromName = $config->getValue('config.fromname');
			$row->AdminEmailSubject = JText::sprintf('RSFP_ADMIN_EMAIL_DEFAULT_SUBJECT', $row->FormTitle);
			$row->AdminEmailText = JText::_('RSFP_ADMIN_EMAIL_DEFAULT_MESSAGE');
		}
		
		$UserEmail = $session->get('com_rsform.wizard.UserEmail');
		if ($UserEmail)
		{
			$row->UserEmailFrom = $config->getValue('config.mailfrom');
			$row->UserEmailFromName = $config->getValue('config.fromname');
			$row->UserEmailSubject = JText::_('RSFP_USER_EMAIL_DEFAULT_SUBJECT');
			$row->UserEmailText = JText::_('RSFP_USER_EMAIL_DEFAULT_MESSAGE');
		}
		
		$action = $session->get('com_rsform.wizard.SubmissionAction');
		if ($action == 'thankyou')
			$row->Thankyou = $session->get('com_rsform.wizard.Thankyou');
		elseif ($action == 'redirect')
			$row->ReturnUrl = $session->get('com_rsform.wizard.ReturnUrl');
		
		$layout = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'layouts'.DS.JFilterInput::clean($row->FormLayoutName, 'path').'.php';
		if (file_exists($layout))
		{
			$quickfields = array();
			$requiredfields = array();
			$row->FormLayout = include($layout);
		}
		
		if ($row->store())
		{
			$predefinedForm = JRequest::getVar('predefinedForm');
			if ($predefinedForm)
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'assets'.DS.'forms'.DS.JFilterInput::clean($predefinedForm);
				if (file_exists($path.DS.'install.xml'))
				{
					$GLOBALS['q_FormId'] = $row->FormId;
					JRequest::setVar('formId', $row->FormId);
					
					$options = array();
					$options['cleanup'] = 0;
					
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'restore.php');
					
					$restore = new RSFormProRestore($options);
					$restore->installDir = $path;
					
					if ($restore->restore())
					{
						$model = $this->getModel('forms');
						$quickfields = $model->getQuickFields();
						
						if ($AdminEmail && !empty($quickfields))
							foreach ($quickfields as $quickfield)
								$row->AdminEmailText .= "\n".'<p>{'.$quickfield.':caption}: {'.$quickfield.':value}</p>';
						
						if ($UserEmail)
						{
							$row->UserEmailTo = '{Email:value}';
							
							if (!empty($quickfields))
								foreach ($quickfields as $quickfield)
									$row->UserEmailText .= "\n".'<p>{'.$quickfield.':caption}: {'.$quickfield.':value}</p>';
						}
						
						$row->store();
					}
				}
			}
		}
		
		$session->clear('com_rsform.wizard.FormTitle');
		$session->clear('com_rsform.wizard.FormLayout');
		$session->clear('com_rsform.wizard.AdminEmail');
		$session->clear('com_rsform.wizard.AdminEmailTo');
		$session->clear('com_rsform.wizard.UserEmail');
		$session->clear('com_rsform.wizard.SubmissionAction');
		$session->clear('com_rsform.wizard.Thankyou');
		$session->clear('com_rsform.wizard.ReturnUrl');
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$row->FormId);
	}
	
	function componentsSave()
	{
		$db = JFactory::getDBO();
		
		$componentType = JRequest::getInt('COMPONENTTYPE');
		$params = JRequest::getVar('param', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		array_walk($params, array('RSFormProHelper', 'escapeArray'));
		$formId = JRequest::getInt('formId');
		
		$componentIdToEdit = JRequest::getInt('componentIdToEdit');
		if ($componentIdToEdit > 0)
		{
			$db->setQuery("SELECT PropertyName FROM #__rsform_properties WHERE ComponentId='".$componentIdToEdit."' AND PropertyName IN ('".implode("','", array_keys($params))."')");
			$properties = $db->loadResultArray();
			
			foreach ($params as $key => $val)
			{
				if (in_array($key, $properties))
					$db->setQuery("UPDATE #__rsform_properties SET PropertyValue='".$val."' WHERE PropertyName='".$key."' AND ComponentId='".$componentIdToEdit."'");
				else
					$db->setQuery("INSERT INTO #__rsform_properties SET PropertyValue='".$val."', PropertyName='".$key."', ComponentId='".$componentIdToEdit."'");
				
				$db->query();
			}
		}
		else
		{
			$db->setQuery("SELECT MAX(`Order`)+1 AS MO FROM #__rsform_components WHERE FormId='".$formId."'");
			$nextOrder = $db->loadResult();
			
			$db->setQuery("INSERT INTO #__rsform_components SET FormId='".$formId."', ComponentTypeId='".$componentType."', `Order`='".$nextOrder."'");
			$db->query();
			$componentId = $db->insertid();
			
			foreach ($params as $key => $val)
			{			
				$db->setQuery("INSERT INTO #__rsform_properties SET PropertyValue='".$val."', PropertyName='".$key."', ComponentId='".$componentId."'");
				$db->query();
			}
		}
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId);
	}
	
	function componentsSaveOrdering()
	{
		$db = JFactory::getDBO();
		$post = JRequest::get('post');
		foreach ($post as $key => $val)
		{
			$key = (int) str_replace('cid_', '', $key);
			$val = (int) $val;
			if (empty($key)) continue;
			
			$db->setQuery("UPDATE #__rsform_components SET `Order`='".$val."' WHERE ComponentId='".$key."'");
			$db->query();
		}
		
		echo 'Ok';
		
		exit();
	}

	/**
	 * Forms Save Process
	 *
	 * @param str $option
	 * @param int $apply
	 */
	function formsSave()
	{
		$formId = JRequest::getInt('formId');
		
		$model = $this->getModel('forms');
		$saved = $model->save();
		
		$task = JRequest::getWord('task');
		if ($task == 'formssave')
			$this->setRedirect('index.php?option=com_rsform&task=forms.manage', JText::_('RSFP_FORM_SAVED'));
		else
		{
			$tabposition = JRequest::getInt('tabposition', 0);
			$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId.'&tabposition='.$tabposition, JText::_('RSFP_FORM_SAVED'));
		}
	}

	/**
	 * Closes the form
	 */
	function formsCancel()
	{
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage');
	}
	/**
	 * Change the AutoGenerate layout
	 * @param unknown_type $formId
	 */
	function formsChangeAutoGenerateLayout()
	{
		$formId = JRequest::getInt('formId');
		$formLayoutName = JRequest::getVar('formLayoutName');
		
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE #__rsform_forms SET `FormLayoutAutogenerate` = ABS(FormLayoutAutogenerate-1), `FormLayoutName`='".$db->getEscaped($formLayoutName)."' WHERE `FormId` = '".$formId."'");
		$db->query();
		
		exit();
	}

	/**
	 * Validates a component name
	 */
	function componentsValidateName()
	{
		$componentName = trim(JRequest::getVar('componentName', ''));
		if (preg_match('#([^a-zA-Z0-9_ ])#', $componentName) || empty($componentName))
		{
			echo JText::_('RSFP_UNIQUE_NAME_MSG');
			exit();
		}
		
		//on file upload component, check destination
		$componentType = JRequest::getInt('componentType');
		if ($componentType == 9)
		{
			$destination = JRequest::getVar('destination');
			if (empty($destination))
			{
				echo JText::_('RSFP_ERROR_DESTINATION_MSG');
				exit();
			}
			if(!is_dir($destination))
			{
				echo JText::_('RSFP_ERROR_DESTINATION_MSG');
				exit();
			}
			if(!is_writable($destination))
			{
				echo JText::_('RSFP_ERROR_DESTINATION_WRITABLE_MSG');
				exit();
			}
		}
		
		if ($componentType == 6)
		{
			$mindate = JRequest::getVar('mindate');
			$maxdate = JRequest::getVar('maxdate');
			if ($mindate && $maxdate && @strtotime($mindate) > @strtotime($maxdate))
			{
				echo JText::_('RSFP_CALENDAR_DATES_ERROR_MSG');
				exit();
			}
		}
		
		$currentComponentId = JRequest::getInt('currentComponentId');
		$componentId		= JRequest::getInt('componentId');
		$formId				= JRequest::getInt('formId');
		
		$exists = RSFormProHelper::componentNameExists($componentName, $formId, $currentComponentId);
		if ($exists)
			echo JText::_('RSFP_UNIQUE_NAME_MSG');
		else
			echo 'Ok';

		exit();
	}

	/**
	 * Displays a component in the backend.
	 */
	function componentsDisplay()
	{
		JRequest::setVar('view', 'formajax');
		JRequest::setVar('layout', 'component');
		JRequest::setVar('format', 'raw');
		
		parent::display();
	}

	/**
	 * Components Copy Process
	 */
	function componentsCopyProcess()
	{
		$toFormId = JRequest::getInt('toFormId');
		$cids = JRequest::getVar('cid');
		JArrayHelper::toInteger($cids, array());
		foreach ($cids as $cid)
			RSFormProHelper::copyComponent($cid, $toFormId);
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$toFormId, JText::sprintf('RSFP_COMPONENTS_COPIED', count($cids)));
	}

	/**
	 * Components Copy Screen
	 */
	function componentsCopy()
	{
		$formId = JRequest::getInt('formId');
		$db = JFactory::getDBO();
		$db->setQuery("SELECT FormId FROM #__rsform_forms WHERE FormId != '".$formId."'");
		if (!$db->loadResult())
			return $this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::_('RSFP_NEED_MORE_FORMS'));
		
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'component_copy');
		
		parent::display();
	}
	
	function componentsCopyCancel()
	{
		$formId = JRequest::getInt('formId');
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId);
	}

	function componentsDuplicate()
	{
		$formId = JRequest::getInt('formId');
		
		$cids = JRequest::getVar('cid');
		JArrayHelper::toInteger($cids, array());
		foreach ($cids as $cid)
			RSFormProHelper::copyComponent($cid, $formId);
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::sprintf('RSFP_COMPONENTS_COPIED', count($cids)));
	}
	
	/**
	 * Publish / Unpublish a component
	 */
	function componentsChangeStatus()
	{
		$model = $this->getModel('formajax');
		$model->componentsChangeStatus();
		$componentId = $model->getComponentId();
		
		if (is_array($componentId))
		{
			$formId = JRequest::getInt('formId');
			
			$task = strtolower(JRequest::getWord('task'));
			$msg = 'ITEMS UNPUBLISHED';
			if ($task == 'componentspublish')
				$msg = 'ITEMS PUBLISHED';
			
			$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::sprintf($msg, count($componentId)));
		}
		// Ajax request
		else
		{
			JRequest::setVar('view', 'formajax');
			JRequest::setVar('layout', 'component_published');
			JRequest::setVar('format', 'raw');
		
			parent::display();
		}
	}

	/**
	 * Remove Component
	 */
	function componentsRemove()
	{
		$cids = JRequest::getVar('cid', array());
		JArrayHelper::toInteger($cids);
		$formId = JRequest::getInt('formId');
		$ajax = JRequest::getInt('ajax');

		$db = JFactory::getDBO();
		if (!empty($cids))
		{
			$db->setQuery("DELETE FROM #__rsform_components WHERE ComponentId IN (".implode(',', $cids).")");
			$db->query();
			$db->setQuery("DELETE FROM #__rsform_properties WHERE ComponentId IN (".implode(',', $cids).")");
			$db->query();
		}
		
		$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' ORDER BY `Order`");
		$components = $db->loadAssocList();
		$i = 1;
		foreach ($components as $r)
		{
			$db->setQuery("UPDATE #__rsform_components SET `Order`='".$i."' WHERE ComponentId='".$r['ComponentId']."'");
			$db->query();
			$i++;
		}
		
		if ($ajax)
		{
			$model = $this->getModel('forms');			
			if (!$model->getHasSubmitButton())
				echo 'NOSUBMIT';
			
			exit();
		}
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::sprintf('ITEMS REMOVED', count($cids)));
	}

	function layoutsGenerate()
	{
		$model = $this->getModel('forms');
		$model->getForm();
		$model->_form->FormLayoutName = JRequest::getCmd('layoutName');
		$model->autoGenerateLayout();
		
		echo $model->_form->FormLayout;
		exit();
	}

	function layoutsSaveName()
	{
		$formId = JRequest::getInt('formId');
		$name = JRequest::getVar('formLayoutName');
		
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE #__rsform_forms SET FormLayoutName='".$db->getEscaped($name)."' WHERE FormId='".$formId."'");
		$db->query();
		
		exit();
	}

	/**
	 * Submissions Manager Screen
	 * @param int $formId
	 */
	function submissionsManage()
	{
		JRequest::setVar('view', 'submissions');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}
	
	function submissionsColumns()
	{
		$mainframe =& JFactory::getApplication();
		
		$formId = JRequest::getInt('formId');
		
		$this->_db->setQuery("DELETE FROM #__rsform_submission_columns WHERE FormId='".$formId."'");
		$this->_db->query();
		
		$staticcolumns = JRequest::getVar('staticcolumns', array());
		foreach ($staticcolumns as $column)
		{
			$this->_db->setQuery("INSERT INTO #__rsform_submission_columns SET FormId='".$formId."', ColumnName='".$this->_db->getEscaped($column)."', ColumnStatic='1'");
			$this->_db->query();
		}
		
		$columns = JRequest::getVar('columns', array());
		foreach ($columns as $column)
		{
			$this->_db->setQuery("INSERT INTO #__rsform_submission_columns SET FormId='".$formId."', ColumnName='".$this->_db->getEscaped($column)."', ColumnStatic='0'");
			$this->_db->query();
		}
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage&formId='.$formId);
	}
	
	/**
	 * Edits one submission
	 *
	 * @param str $option
	 * @param int $formId
	 */
	function submissionsEdit()
	{
		JRequest::setVar('view', 'submissions');
		JRequest::setVar('layout', 'edit');
		
		parent::display();
	}

	function submissionsSave()
	{
		// Get the model
		$model = $this->getModel('submissions');
		
		// Save
		$model->save();
		
		$cid = $model->getSubmissionId();
		
		$task = JRequest::getWord('task');
		switch($task)
		{
			case 'submissionsapply':
				$this->setRedirect('index.php?option=com_rsform&task=submissions.edit&cid[]='.$cid, JText::_('RSFP_SUBMISSION_SAVED'));
			break;
		
			case 'submissionssave':
				$this->setRedirect('index.php?option=com_rsform&task=submissions.manage', JText::_('RSFP_SUBMISSION_SAVED'));
			break;
		}
	}
	
	function submissionsResend()
	{
		$formId = JRequest::getInt('formId');
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		
		foreach ($cid as $SubmissionId)
			RSFormProHelper::sendSubmissionEmails($SubmissionId);
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage&formId='.$formId);
	}
	
	/**
	 * Closes the form
	 */
	function submissionsCancel()
	{
		$this->setRedirect('index.php?option=com_rsform');
	}

	function submissionsClear()
	{
		$formId = JRequest::getInt('formId');
		
		$model = $this->getModel('submissions');
		$model->deleteSubmissionFiles($formId);
		$total = $model->deleteSubmissions($formId);
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', JText::sprintf('RSFP_SUBMISSIONS_CLEARED', $total));
	}
	
	/**
	 * Deletes submissions
	 */
	function submissionsDelete()
	{
		$formId = JRequest::getInt('formId');
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		
		$model = $this->getModel('submissions');
		$model->deleteSubmissionFiles($cid);
		$model->deleteSubmissions($cid);
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage&formId='.$formId);
	}

	/**
	 * Export Submissions Screen
	 */
	function submissionsExport()
	{
		$config = JFactory::getConfig();
		$tmp_path = $config->getValue('config.tmp_path');
		if (!is_writable($tmp_path))
		{
			JError::raiseWarning(500, JText::sprintf('RSFP_EXPORT_ERROR_MSG', $tmp_path));
			$this->setRedirect('index.php?option=com_rsform&task=submissions.manage');
		}
		
		JRequest::setVar('view', 'submissions');
		JRequest::setVar('layout', 'export');
		
		parent::display();
	}
	
	/**
	 * Submissions Export Process
	 */
	function submissionsExportProcess()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		$config = JFactory::getConfig();
	
		// Get post
		$session = JFactory::getSession();
		$post = $session->get($option.'.export.data', serialize(array()));
		$post = unserialize($post);
		
		// Limit
		$start = JRequest::getInt('exportStart');
		$mainframe->setUserState($option.'.submissions.limitstart', $start);
		$limit = JRequest::getInt('exportLimit', 500);
		$mainframe->setUserState($option.'.submissions.limit', $limit);
		
		// Tmp path
		$tmp_path = $config->getValue('config.tmp_path');
		$file = $tmp_path.DS.$post['ExportFile'];
		
		$formId = $post['formId'];
		
		// Type
		$type = strtolower($post['exportType']);
		
		// Selected rows or all rows
		$rows = !empty($post['ExportRows']) ? explode(',', $post['ExportRows']) : '';
		
		// Use headers ?
		$use_headers = (int) $post['ExportHeaders'];
		
		// Headers and ordering
		$staticHeaders = $post['ExportSubmission'];
		$headers = $post['ExportComponent'];
		$order = $post['ExportOrder'];
		
		// Remove headers that we're not going to export
		foreach ($order as $name => $id)
		{
			if (!isset($staticHeaders[$name]) && !isset($headers[$name]))
				unset($order[$name]);
		}
		
		// Adjust order array
		$order = array_flip($order);
		ksort($order);
		
		$model = $this->getModel('submissions');
		$model->export = true;
		$model->rows = $rows;
		$model->_query = $model->_buildQuery();
		$submissions = $model->getSubmissions();
		
		// CSV Options
		if ($type == 'csv')
		{
			$delimiter = str_replace(array('\t', '\n', '\r'), array("\t","\n","\r"), $post['ExportDelimiter']);
			$enclosure = str_replace(array('\t', '\n', '\r'), array("\t","\n","\r"), $post['ExportFieldEnclosure']);
			
			// Create and open file for writing if this is the first call
			// If not, just append to the file
			// Using fopen() because JFile::write() lacks such options
			$handle = fopen($file, $start == 0 ? 'w' : 'a');
			
			if ($start == 0 && $use_headers)
			{
				fwrite($handle, $enclosure.implode($enclosure.$delimiter.$enclosure,$order).$enclosure);
				fwrite($handle, "\n");
			}
			
			if (empty($submissions))
			{
				fclose($handle);
				// Adjust pagination
				$mainframe->setUserState($option.'.submissions.limitstart', 0);
				$mainframe->setUserState($option.'.submissions.limit', $mainframe->getCfg('list_limit'));
				echo 'END';
			}
			else
			{
				foreach ($submissions as $submissionId => $submission)
				{
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
						{
							$submission['SubmissionValues'][$header]['Value'] = ereg_replace("\015(\012)?", "\012", $submission['SubmissionValues'][$header]['Value']);
							// Is this right ?
							if (strpos($submission['SubmissionValues'][$header]['Value'],"\n") !== false)
								$submission['SubmissionValues'][$header]['Value'] = str_replace("\n",' ',$submission['SubmissionValues'][$header]['Value']);
						}
						fwrite($handle, $enclosure.(isset($submission['SubmissionValues'][$header]) ? str_replace(array('\\r','\\n','\\t',$enclosure), array("\015","\012","\011",$enclosure.$enclosure), $submission['SubmissionValues'][$header]['Value']) : (isset($submission[$header]) ? $submission[$header] : '')).$enclosure.($header != end($order) ? $delimiter : ""));
					}
					fwrite($handle, "\n");
				}
				fclose($handle);
			}
		}
		// Excel Options
		elseif ($type == 'excel')
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'excel.php');
			
			$xls = new RSFormProXLS();
			$xls->use_headers = $use_headers;
			$xls->open($file, $start == 0 ? 'w' : 'a', $start);
			
			if ($start == 0 && $use_headers)
				$xls->write_headers($order);
			
			if (empty($submissions))
			{
				$xls->close();
				// Adjust pagination
				$mainframe->setUserState($option.'.submissions.limitstart', 0);
				$mainframe->setUserState($option.'.submissions.limit', $mainframe->getCfg('list_limit'));
				echo 'END';
			}
			else
			{
				$array = array();
				foreach ($submissions as $submissionId => $submission)
				{
					$item = array();
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
							$item[$header] = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item[$header] = $submission[$header];
						else
							$item[$header] = '';
					}
					
					$array[] = $item;
				}
				$xls->write($array);
				$xls->close();
			}
		}
		// XML Options
		elseif ($type == 'xml')
		{
			$handle = fopen($file, $start == 0 ? 'w' : 'a');
			
			if ($start == 0)
			{
				$buffer = '';
				$buffer .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
				$buffer .= '<form>'."\n";
				$buffer .= '<title><![CDATA['.$model->getFormTitle().']]></title>'."\n";
				$buffer .= "\t".'<submissions>'."\n";
				fwrite($handle, $buffer);
			}
			
			if (empty($submissions))
			{
				$buffer = '';
				$buffer .= "\t".'</submissions>'."\n";
				$buffer .= '</form>';
				fwrite($handle, $buffer);
				fclose($handle);
				// Adjust pagination
				$mainframe->setUserState($option.'.submissions.limitstart', 0);
				$mainframe->setUserState($option.'.submissions.limit', $mainframe->getCfg('list_limit'));
				echo 'END';
			}
			else
			{
				foreach ($submissions as $submissionId => $submission)
				{
					fwrite($handle, "\t\t".'<submission>'."\n");
					$buffer = '';
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
							$item = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item = $submission[$header];
						else
							$item = '';
						
						if (!is_numeric($item))
							$item = '<![CDATA['.$item.']]>';
						
						$buffer .= "\t\t\t".'<'.$header.'>'.$item.'</'.$header.'>';
					}
					fwrite($handle, $buffer);
					fwrite($handle, "\t\t".'</submission>'."\n");
				}
				fclose($handle);
			}
		}
		
		exit();
	}
	
	function submissionsExportTask()
	{
		$option = JRequest::getVar('option', 'com_rsform');
		
		JRequest::setVar('view', 'submissions');
		JRequest::setVar('layout', 'exportprocess');
		
		parent::display();
		
		$session = JFactory::getSession();
		$session->set($option.'.export.data', serialize(JRequest::get('post', JREQUEST_ALLOWRAW)));
	}

	function submissionsExportFile()
	{
		$config = JFactory::getConfig();
		$file = JRequest::getCmd('ExportFile');
		$file = $config->getValue('config.tmp_path').DS.$file;
		
		$type = JRequest::getCmd('ExportType');
		$extension = 'csv';
		if ($type == 'csv')
			$extension = 'csv';
		elseif ($type == 'excel')
			$extension = 'xls';
		elseif ($type == 'xml')
			$extension = 'xml';
		
		RSFormProHelper::readFile($file, date('Y-m-d').'_rsform.'.$extension);
	}
	
	function submissionExportPDF()
	{		
		$cid = JRequest::getInt('cid');
		$this->setRedirect('index.php?option=com_rsform&view=submissions&layout=edit&cid='.$cid.'&format=pdf');
	}
	
	function submissionsViewFile()
	{
		$id = JRequest::getInt('id');
		$this->_db->setQuery("SELECT * FROM #__rsform_submission_values WHERE SubmissionValueId='".$id."'");
		$result = $this->_db->loadObject();
		
		// Not found
		if (empty($result))
			return $this->setRedirect('index.php?option=com_rsform&task=submissions.manage');
		
		// Not an upload field
		$this->_db->setQuery("SELECT c.ComponentTypeId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId=c.ComponentId) WHERE p.PropertyName='NAME' AND p.PropertyValue='".$this->_db->getEscaped($result->FieldName)."'");
		$type = $this->_db->loadResult();
		if ($type != 9)
			return $this->setRedirect('index.php?option=com_rsform&task=submissions.manage', JText::_('RSFP_VIEW_FILE_NOT_UPLOAD'));
		
		jimport('joomla.filesystem.file');
		
		if (JFile::exists($result->FieldValue))
			RSFormProHelper::readFile($result->FieldValue);
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage', JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
	}

	/**
	 * Saves registration form
	 */
	function saveRegistration()
	{
		$code = JRequest::getVar('code');
		$code = $this->_db->getEscaped($code);
		if (!empty($code))
		{
			$this->_db->setQuery("UPDATE #__rsform_config SET `SettingValue`='".$code."' WHERE `SettingName`='global.register.code'");
			$this->_db->query();
			$this->setRedirect('index.php?option=com_rsform&task=updates.manage', JText::_('RSFP_REGISTRATION_SAVED'));
		}
		else
			$this->setRedirect('index.php?option=com_rsform&task=configuration.edit');
	}

	/**
	 * Configuration Edit Screen
	 */
	function configurationEdit()
	{
		JRequest::setVar('view', 'configuration');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}
	
	function configurationCancel()
	{
		$this->setRedirect('index.php?option=com_rsform');
	}

	/**
	 * Configuration Save process
	 */
	function configurationSave()
	{
		$config = JRequest::getVar('rsformConfig', array(0), 'post');

		$db = JFactory::getDBO();
		foreach ($config as $name => $value)
		{
			$db->setQuery("UPDATE #__rsform_config SET SettingValue = '".$db->getEscaped($value)."' WHERE SettingName = '".$db->getEscaped($name)."'");
			$db->query();
		}
		
		$task = JRequest::getWord('task');
		$task = strtolower($task);
		
		if ($task == 'configurationsave')
			$link = 'index.php?option=com_rsform';
		else
		{
			$tabposition = JRequest::getInt('tabposition', 0);
			$link = 'index.php?option=com_rsform&task=configuration.edit&tabposition='.$tabposition;
		}
		
		RSFormProHelper::readConfig(true);
		
		$this->setRedirect($link, JText::_('RSFP_CONFIGURATION_SAVED'));
	}

	/**
	 * Backup / Restore Screen
	 */
	function backupRestore()
	{
		JRequest::setVar('view', 'backuprestore');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}

	/**
	 * Backup Generate Process
	 *
	 * @param str $option
	 */
	function backupDownload()
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'backup.php');
		
		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$tmpdir = uniqid('rsformbkp');
		$path = JPATH_SITE.DS.'media'.DS.$tmpdir;
		if (!JFolder::create($path, 0777))
		{
			JError::raiseWarning(500, JText::_('Could not create directory ').$path);
			return $this->setRedirect('index.php?option=com_rsform&task=backup.restore');
		}
		
		$export_submissions = JRequest::getInt('submissions');
		if (!RSFormProBackup::create($cid, $export_submissions, $path.DS.'install.xml'))
		{
			JError::raiseWarning(500, JText::_('Could not write to ').$path);
			return $this->setRedirect('index.php?option=com_rsform&task=backup.restore');
		}
		
		$name = 'rsform_backup_'.date('Y-m-d_His').'.zip';
		$files = array(array('data' => JFile::read($path.DS.'install.xml'), 'name' => 'install.xml'));
		
		$adapter =& JArchive::getAdapter('zip');
		if (!$adapter->create($path.DS.$name, $files))
		{
			JError::raiseWarning(500, JText::_('Could not create archive ').$path.DS.$name);
			return $this->setRedirect('index.php?option=com_rsform&task=backup.restore');
		}

		$this->setRedirect(JURI::root().'media/'.$tmpdir.'/'.$name);
	}

	function restoreProcess()
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'restore.php');
		
		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$lang = JFactory::getLanguage();
		$lang->load('com_installer');
		
		$link = 'index.php?option=com_rsform&task=backup.restore';
		
		if(!extension_loaded('zlib'))
		{
			JError::raiseWarning(500, JText::_('WARNINSTALLZLIB'));
			return $this->setRedirect($link);
		}
		
		$userfile = JRequest::getVar('userfile', null, 'files');
		if ($userfile['error'])
		{
			JError::raiseWarning(500, JText::_($userfile['error'] == 4 ? 'ERRORNOFILE' : 'WARNINSTALLUPLOADERROR'));				
			return $this->setRedirect($link);
		}

		$baseDir = JPATH_SITE.DS.'media';
		$moved = JFile::upload($userfile['tmp_name'], $baseDir.DS.$userfile['name']);
		if (!$moved)
		{
			JError::raiseWarning(500, JText::_('FAILED TO MOVE UPLOADED FILE TO'));
			return $this->setRedirect($link);
		}
		
		$options = array();
		$options['filename'] = $baseDir.DS.$userfile['name'];
		$options['overwrite'] = JRequest::getInt('overwrite');
		
		$restore = new RSFormProRestore($options);
		if (!$restore->process())
		{
			JError::raiseWarning(500, JText::_('Unable to extract archive'));
			return $this->setRedirect($link);
		}
		
		if (!$restore->restore())
			return $this->setRedirect($link);
		
		$this->setRedirect($link, JText::_('RSFP_RESTORE_OK'));
	}

	function updatesManage()
	{
		JRequest::setVar('view', 'updates');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}
	
	function goToPlugins()
	{
		$mainframe =& JFactory::getApplication();
		$mainframe->redirect('http://www.rsjoomla.com/joomla-plugins/rsform-pro.html');
	}
	
	function goToSupport()
	{
		$mainframe =& JFactory::getApplication();
		$mainframe->redirect('http://www.rsjoomla.com/customer-support/documentations/21-rsform-pro-user-guide.html');
	}
	
	function plugin()
	{
		$mainframe =& JFactory::getApplication();
		$mainframe->triggerEvent('rsfp_bk_onSwitchTasks');
	}
	
	function setMenu()
	{
		$app   =& JFactory::getApplication();
		
		$type  = json_decode('{"id":0,"title":"COM_RSFORM_MENU_FORM","request":{"option":"com_rsform","view":"rsform"}}');
		$title = 'component';
		
		$app->setUserState('com_menus.edit.item.type',	$title);
		
		$component = JComponentHelper::getComponent($type->request->option);
		$data['component_id'] = $component->id;
		
		$params['option'] = 'com_rsform';
		$params['view']   = 'rsform';
		$params['formId'] = JRequest::getInt('formId');
		
		$app->setUserState('com_menus.edit.item.link', 'index.php?'.JURI::buildQuery($params));
		
		$data['type'] = $title;
		$data['formId'] = JRequest::getInt('formId');
		$app->setUserState('com_menus.edit.item.data', $data);
		
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
	}
}
?>