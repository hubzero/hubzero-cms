<?php
/**
 * @version     $Id: jqueryintegrator.php revision date tushev $
 * @package     Joomla
 * @subpackage  System
 * @copyright   Copyright (C) S.A. Tushev, 2010. All rights reserved.
 * @license     GNU GPL v2.0
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemJquery extends JPlugin
{
	/**
	 * Hook for after routing application
	 * 
	 * @return  void
	 */
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();

		$client = 'Site';
		if ($app->isAdmin())
		{
			$client = 'Admin';
			return;
		}

		// Check if active for this client (Site|Admin)
		if (!$this->params->get('activate' . $client) || JRequest::getVar('format') == 'pdf')
		{
			return;
		}

		JHTML::_('behavior.framework');

		if ($this->params->get('jqueryui'))
		{
			JHTML::_('behavior.framework', true);
		}

		if ($this->params->get('jqueryfb'))
		{
			JHTML::_('behavior.modal');
		}

		if ($this->params->get('noconflict' . $client))
		{
			JFactory::getDocument()->addScript(JURI::root(true) . '/media/system/js/jquery.noconflict.js');
		}
	}
}
