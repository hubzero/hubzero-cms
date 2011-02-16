<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSFormViewFormAjax extends JView
{
	function display($tpl = null)
	{
		switch ($this->getLayout())
		{
			case 'component':
				$this->assignRef('fields', $this->get('componentFields'));
				$this->assignRef('data', $this->get('componentData'));
				
				$this->assign('type_id', $this->get('componentType'));
				$this->assign('componentId', $this->get('componentId'));
				$this->assign('show_save', count($this->fields) > 3);
			break;
			
			case 'component_published':
				$this->assign('i', $this->get('i'));
				$this->assign('field', $this->get('component'));
			break;
		}
		
		parent::display($tpl);
	}
}