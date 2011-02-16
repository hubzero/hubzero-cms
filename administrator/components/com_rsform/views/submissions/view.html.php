<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pane');

class RSFormViewSubmissions extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		
		JToolBarHelper::title('RSForm! Pro','rsform');
		
		if (RSFormProHelper::isJ16())
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_rsform.sys', JPATH_ADMINISTRATOR);
			
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_FORMS'), 'index.php?option=com_rsform&task=forms.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_SUBMISSIONS'), 'index.php?option=com_rsform&task=submissions.manage', true);
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_CONFIGURATION'), 'index.php?option=com_rsform&task=configuration.edit');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_BACKUP_RESTORE'), 'index.php?option=com_rsform&task=backup.restore');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_UPDATES'), 'index.php?option=com_rsform&task=updates.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_PLUGINS'), 'index.php?option=com_rsform&task=goto.plugins');
		}
		
		$layout = $this->getLayout();
		
		if ($layout == 'export')
		{
			JToolBarHelper::custom('submissions.export.task', 'archive', 'archive', JText::_('RSFP_EXPORT'), false);
			JToolBarHelper::spacer();
			JToolBarHelper::cancel('submissions.manage');
			
			$this->assignRef('formId', $this->get('formId'));
			$this->assignRef('headers', $this->get('headers'));
			$this->assignRef('staticHeaders', $this->get('staticHeaders'));
			
			$previewArray = array();
			$i = 0;
			foreach ($this->staticHeaders as $header)
			{
				$i++;
				$previewArray[] = 'Value '.$i;
			}
			foreach ($this->headers as $header)
			{
				$i++;
				$previewArray[] = 'Value '.$i;
			}
			$this->assign('previewArray', $previewArray);
			
			$this->assignRef('formTitle', $this->get('formTitle'));
			$this->assignRef('exportSelected', $this->get('exportSelected'));
			$this->assign('exportSelectedCount', count($this->exportSelected));
			$this->assign('exportAll', $this->exportSelectedCount == 0);
			$this->assign('exportType', $this->get('exportType'));
			$this->assign('exportFile', $this->get('exportFile'));
			
			$formTitle = $this->get('formTitle');
			JToolBarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $formTitle).']</small>','rsform');
			
			$tabs =& JPane::getInstance('Tabs', array(), true);
			$this->assignRef('tabs', $tabs);
		}
		elseif (strtolower($layout) == 'exportprocess')
		{
			$this->assign('limit', 500);
			$this->assign('total', $this->get('exportTotal'));
			$this->assign('file', JRequest::getCmd('ExportFile'));
			$this->assign('exportType', JRequest::getCmd('exportType'));
			
			$formTitle = $this->get('formTitle');
			JToolBarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $formTitle).']</small>','rsform');
		}
		elseif ($layout == 'edit')
		{
			JToolBarHelper::custom('submission.export.pdf', 'archive', 'archive', JText::_('RSFP_EXPORT_PDF'), false);
			JToolBarHelper::spacer();
			JToolBarHelper::apply('submissions.apply');
			JToolBarHelper::save('submissions.save');
			JToolBarHelper::spacer();
			JToolBarHelper::cancel('submissions.manage');
			
			$this->assignRef('formId', $this->get('submissionFormId'));
			$this->assignRef('submissionId', $this->get('submissionId'));
			$this->assignRef('staticHeaders', $this->get('staticHeaders'));
			$this->assignRef('staticFields', $this->get('staticFields'));
			$this->assignRef('fields', $this->get('editFields'));
		}
		else
		{
			JToolBarHelper::custom('submissions.export.csv', 'archive', 'archive', JText::_('RSFP_EXPORT_CSV'), false);
			JToolBarHelper::custom('submissions.export.excel', 'archive', 'archive', JText::_('RSFP_EXPORT_EXCEL'), false);
			JToolBarHelper::custom('submissions.export.xml', 'archive', 'archive', JText::_('RSFP_EXPORT_XML'), false);
			JToolBarHelper::spacer();
			JToolBarHelper::custom('submissions.resend', 'send', 'send', JText::_('RSFP_RESEND_EMAILS'), false);
			JToolBarHelper::editListX('submissions.edit', JText::_('Edit'));
			JToolBarHelper::deleteList(JText::_('VALIDDELETEITEMS'), 'submissions.delete', JText::_('DELETE'));
			JToolBarHelper::spacer();
			JToolBarHelper::cancel('submissions.cancel', JText::_('Close'));
			
			$forms = $this->get('forms');
			$formId = $this->get('formId');
		
			$formTitle = $this->get('formTitle');
			JToolBarHelper::title('RSForm! Pro <small>['.$formTitle.']</small>','rsform');
		
			$this->assignRef('headers', $this->get('headers'));
			$this->assignRef('staticHeaders', $this->get('staticHeaders'));
		
			$this->assignRef('submissions', $this->get('submissions'));
			$this->assignRef('pagination', $this->get('pagination'));
		
			$this->assignRef('sortColumn', $this->get('sortColumn'));
			$this->assignRef('sortOrder', $this->get('sortOrder'));
		
			$this->assign('filter', $this->get('filter'));
			$this->assign('formId', $formId);
		
			$calendars['from'] = JHTML::calendar($this->get('dateFrom'), 'dateFrom', 'dateFrom');
			$calendars['to']   = JHTML::calendar($this->get('dateTo'), 'dateTo', 'dateTo');
			$this->assignRef('calendars', $calendars);
		
			$lists['forms'] = JHTML::_('select.genericlist', $forms, 'formId', 'onchange="submissionChangeForm(this.value)"', 'value', 'text', $formId);
			$this->assignRef('lists', $lists);
		}
		
		parent::display($tpl);
	}
	
	function isHeaderEnabled($header, $static=0)
	{
		if (!isset($this->headersEnabled))
			$this->headersEnabled = $this->get('headersEnabled');
		
		$array = 'headers';
		if ($static)
			$array = 'staticHeaders';
		
		if (empty($this->headersEnabled->headers) && empty($this->headersEnabled->staticHeaders))
			return true;
		
		return in_array($header, $this->headersEnabled->{$array});
	}
}