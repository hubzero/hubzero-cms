<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSFormViewForms extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSForm! Pro','rsform');
		
		if (RSFormProHelper::isJ16())
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_rsform.sys', JPATH_ADMINISTRATOR);
			
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_FORMS'), 'index.php?option=com_rsform&task=forms.manage', true);
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_SUBMISSIONS'), 'index.php?option=com_rsform&task=submissions.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_CONFIGURATION'), 'index.php?option=com_rsform&task=configuration.edit');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_BACKUP_RESTORE'), 'index.php?option=com_rsform&task=backup.restore');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_UPDATES'), 'index.php?option=com_rsform&task=updates.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_PLUGINS'), 'index.php?option=com_rsform&task=goto.plugins');
		}
		
		$layout = $this->getLayout();
		if ($layout == 'edit')
		{
			JToolBarHelper::apply('forms.apply');
			JToolBarHelper::save('forms.save');
			JToolBarHelper::spacer();
			JToolBarHelper::custom('forms.preview', 'preview', 'preview', JText::_('PREVIEW'), false);
			JToolBarHelper::custom('submissions.manage', 'forward', 'forward', JText::_('RSFP_SUBMISSIONS'), false);
			JToolBarHelper::custom('components.copy', 'copy', 'copy', JText::_('RSFP_COPY_TO_FORM'), false);
			JToolBarHelper::custom('components.duplicate', 'copy', 'copy', JText::_('RSFP_DUPLICATE'), false);
			JToolBarHelper::deleteList(JText::_('VALIDDELETEITEMS'), 'components.remove', JText::_('DELETE'));
			JToolBarHelper::publishList('components.publish', JText::_('Publish'));
			JToolBarHelper::unpublishList('components.unpublish', JText::_('Unpublish'));
			JToolBarHelper::spacer();
			JToolBarHelper::cancel('forms.cancel');
			
			jimport('joomla.html.pane');
			$params = array('startOffset' => JRequest::getInt('tabposition', 0));
			$tabs =& JPane::getInstance('Tabs', $params, true);
			$this->assignRef('tabs', $tabs);
			
			$this->assignRef('form', $this->get('form'));
			
			$this->assign('hasSubmitButton', $this->get('hasSubmitButton'));
			
			JToolBarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EDITING_FORM', $this->form->FormTitle).']</small>','rsform');
			
			$this->assignRef('fields', $this->get('fields'));
			$this->assignRef('quickfields', $this->get('quickfields'));
			$this->assignRef('pagination', $this->get('fieldspagination'));
			
			$lists['Published'] = JHTML::_('select.booleanlist','Published','class="inputbox"',$this->form->Published);
			$lists['ShowThankyou'] = JHTML::_('select.booleanlist','ShowThankyou','class="inputbox" onclick="enableThankyou(this.value);"',$this->form->ShowThankyou);
			$lists['ShowContinue'] = JHTML::_('select.booleanlist', 'ShowContinue', 'class="inputbox"'.(!$this->form->ShowThankyou ? 'disabled="disabled"' : ''), $this->form->ShowContinue);
			$lists['UserEmailMode'] = JHTML::_('select.booleanlist', 'UserEmailMode', 'class="inputbox" onclick="enableEmailMode(\'User\', this.value)"', $this->form->UserEmailMode, JText::_('HTML'), JText::_('Text'));
			$lists['UserEmailAttach'] = JHTML::_('select.booleanlist', 'UserEmailAttach', 'class="inputbox" onclick="enableAttachFile(this.value)"', $this->form->UserEmailAttach);
			$lists['AdminEmailMode'] = JHTML::_('select.booleanlist', 'AdminEmailMode', 'class="inputbox" onclick="enableEmailMode(\'Admin\', this.value)"', $this->form->AdminEmailMode, JText::_('HTML'), JText::_('Text'));
			$lists['MetaTitle'] = JHTML::_('select.booleanlist', 'MetaTitle', 'class="inputbox"', $this->form->MetaTitle);
			$lists['TextareaNewLines'] = JHTML::_('select.booleanlist', 'TextareaNewLines', 'class="inputbox"', $this->form->TextareaNewLines);
			$lists['AjaxValidation'] = JHTML::_('select.booleanlist', 'AjaxValidation', 'class="inputbox"', $this->form->AjaxValidation);
			
			$this->assignRef('themes', $this->get('themes'));
			
			$this->assignRef('lists', $lists);
		}
		elseif ($layout == 'new')
		{
			JToolBarHelper::custom('forms.new.steptwo', 'forward', 'forward', JText::_('Next'), false);
			JToolBarHelper::cancel('forms.cancel');
		}
		elseif ($layout == 'new2')
		{
			JToolBarHelper::custom('forms.new.stepthree', 'forward', 'forward', JText::_('Next'), false);
			JToolBarHelper::cancel('forms.cancel');
			
			$lists['AdminEmail'] = JHTML::_('select.booleanlist', 'AdminEmail', 'class="inputbox" onclick="changeAdminEmail(this.value)"', 1);
			$lists['UserEmail'] = JHTML::_('select.booleanlist', 'UserEmail', 'class="inputbox"', 1);
			$actions = array(
				JHTML::_('select.option', 'refresh', JText::_('RSFP_SUBMISSION_REFRESH_PAGE')),
				JHTML::_('select.option', 'thankyou', JText::_('RSFP_SUBMISSION_THANKYOU')),
				JHTML::_('select.option', 'redirect', JText::_('RSFP_SUBMISSION_REDIRECT_TO'))				
			);
			$lists['SubmissionAction'] = JHTML::_('select.genericlist', $actions, 'SubmissionAction', 'onclick="changeSubmissionAction(this.value)"');
			
			$this->assignRef('adminEmail', $this->get('adminEmail'));
			$this->assignRef('lists', $lists);
			$this->assignRef('editor', JFactory::getEditor());
		}
		elseif ($layout == 'new3')
		{
			JToolBarHelper::custom('forms.new.stepfinal', 'forward', 'forward', JText::_('Finish'), false);
			JToolBarHelper::cancel('forms.cancel');
			
			$lists['predefinedForms'] = JHTML::_('select.genericlist', $this->get('predefinedforms'), 'predefinedForm', '');
			$this->assignRef('lists', $lists);
		}
		elseif ($layout == 'component_copy')
		{
			JToolBarHelper::custom('components.copy.process', 'copy', 'copy', 'Copy', false);
			JToolBarHelper::cancel('components.copy.cancel');
			
			$formlist = $this->get('formlist');
			$lists['forms'] = JHTML::_('select.genericlist', $formlist, 'toFormId', '', 'value', 'text');
			
			$this->assign('formId', JRequest::getInt('formId'));
			$this->assign('cids', JRequest::getVar('cid', array()));
			$this->assignRef('lists', $lists);
		}
		elseif ($layout == 'richtext')
		{
			$this->assignRef('editor', JFactory::getEditor());
			$this->assign('noEditor', JRequest::getInt('noEditor'));
			$this->assign('formId', JRequest::getInt('formId'));
			$this->assign('editorName', JRequest::getCmd('opener'));
			$this->assign('editorText', $this->get('editorText'));
		}
		else
		{
			JToolBarHelper::addNewX('forms.new', JText::_('New'));
			JToolBarHelper::spacer();
			JToolBarHelper::custom('forms.copy', 'copy.png', 'copy_f2.png', JText::_('RSFP_DUPLICATE'), false);
			JToolBarHelper::spacer();
			JToolBarHelper::deleteList(JText::_('VALIDDELETEITEMS'), 'forms.delete', JText::_('DELETE'));
			JToolBarHelper::spacer();
			JToolBarHelper::publishList('forms.publish', JText::_('Publish'));
			JToolBarHelper::unpublishList('forms.unpublish', JText::_('Unpublish'));
		
			$this->assignRef('forms', $this->get('forms'));
			$this->assignRef('pagination', $this->get('pagination'));
		
			$this->assignRef('sortColumn', $this->get('sortColumn'));
			$this->assignRef('sortOrder', $this->get('sortOrder'));
		}
		
		parent::display($tpl);
	}
	
	function triggerEvent($event)
	{
		$mainframe =& JFactory::getApplication();		
		$mainframe->triggerEvent($event);
	}
}