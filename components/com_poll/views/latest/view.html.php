<?php
/**
* @version		$Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Poll
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Poll component
 *
 * @static
 * @package		Joomla
 * @subpackage	Poll
 * @since 1.0
 */
class PollViewLatest extends JView
{
	public function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$db        = JFactory::getDBO();
		$document  = JFactory::getDocument();
		$pathway   = $mainframe->getPathway();

		require_once(JPATH_COMPONENT . DS . 'models' . DS . 'poll.php');
		$model = new PollModelPoll();
		$poll = $model->getLatest();

		// if id value is passed and poll not published then exit
		if ($poll->id > 0 && $poll->published != 1) 
		{
			JError::raiseError(403, JText::_('Access Forbidden'));
			return;
		}

		$options = $model->getPollOptions($poll->id);

		// Adds parameter handling
		$params = $mainframe->getParams();

		//Set page title information
		$menus = JFactory::getApplication()->getMenu();
		$menu  = $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu)) 
		{
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) 
			{
				$params->set('page_title', $poll->title);
			}
		} 
		else 
		{
			$params->set('page_title', $poll->title);
		}
		$document->setTitle($params->get('page_title'));

		//Set pathway information
		$pathway->addItem($poll->title, '');

		$params->def('show_page_title', 1);
		$params->def('page_title', $poll->title);

		$this->assignRef('options', $options);
		$this->assignRef('params', $params);
		$this->assignRef('poll', $poll);

		parent::display($tpl);
	}
}
