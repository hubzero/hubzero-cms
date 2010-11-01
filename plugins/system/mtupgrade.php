<?php
/**
* @version		$Id: mtupgrade.php 18130 2010-07-14 11:21:35Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemMTUpgrade extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemMTUpgrade(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * onAfterInitialise handler
	 *
	 * Adds the mtupgrade folder to the list of directories to search for JHTML helpers.
	 *
	 * @access	public
	 * @return null
	 */
	function onAfterInitialise()
	{
		JHTML::addIncludePath(JPATH_PLUGINS.DS.'system'.DS.'mtupgrade');
	}
}
