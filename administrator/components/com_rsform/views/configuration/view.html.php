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

class RSFormViewConfiguration extends JView
{
	function display($tpl = null)
	{		
		JToolBarHelper::title('RSForm! Pro','rsform');
		
		if (RSFormProHelper::isJ16())
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_rsform.sys', JPATH_ADMINISTRATOR);
			
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_FORMS'), 'index.php?option=com_rsform&task=forms.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_SUBMISSIONS'), 'index.php?option=com_rsform&task=submissions.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_CONFIGURATION'), 'index.php?option=com_rsform&task=configuration.edit', true);
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_BACKUP_RESTORE'), 'index.php?option=com_rsform&task=backup.restore');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_UPDATES'), 'index.php?option=com_rsform&task=updates.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_PLUGINS'), 'index.php?option=com_rsform&task=goto.plugins');
		}
		
		JToolBarHelper::apply('configuration.apply');
		JToolBarHelper::save('configuration.save');
		JToolBarHelper::cancel('');
		
		$params = array('startOffset' => JRequest::getInt('tabposition', 0));
		$tabs =& JPane::getInstance('Tabs', $params, true);
		$this->assignRef('tabs', $tabs);
		
		$this->assign('code', RSFormProHelper::getConfig('global.register.code'));
		$lists['global.debug.mode'] = JHTML::_('select.booleanlist','rsformConfig[global.debug.mode]','class="inputbox"', RSFormProHelper::getConfig('global.debug.mode'));
		$lists['global.iis'] = JHTML::_('select.booleanlist','rsformConfig[global.iis]','class="inputbox"', RSFormProHelper::getConfig('global.iis'));
		$lists['global.editor'] = JHTML::_('select.booleanlist','rsformConfig[global.editor]','class="inputbox"', RSFormProHelper::getConfig('global.editor'));
		
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);
	}
	
	function triggerEvent($event)
	{
		$mainframe =& JFactory::getApplication();
		$mainframe->triggerEvent($event);
	}
}