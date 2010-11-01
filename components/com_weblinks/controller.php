<?php
/**
 * @version		$Id: controller.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Weblinks Component Controller
 *
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.5
 */
class WeblinksController extends JController
{
	/**
	 * Method to show a weblinks view
	 *
	 * @access	public
	 * @since	1.5
	 */
	function display()
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'categories' );
		}

		//update the hit count for the weblink
		if(JRequest::getCmd('view') == 'weblink')
		{
			$model =& $this->getModel('weblink');
			$model->hit();
		}
		
		// View caching logic -- simple... are we logged in?
		$user = &JFactory::getUser();
		$view = JRequest::getVar('view');
		$viewcache = JRequest::getVar('viewcache', '1', 'POST', 'INT');
		if ($user->get('id') || ($view == 'category' && $viewcache == 0)) {
			parent::display(false);
		} else {
			parent::display(true);
		}
	}
}
