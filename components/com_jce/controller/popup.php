<?php
/**
* @version		popup.php 2008-06-04
* @package		Joomla Content Editor (JCE)
* @subpackage	Components
* @copyright	Copyright (C) 2005 - 2008 Ryan Demmer. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Users Component Controller
 *
 * @package		Joomla
 * @subpackage	Users
 * @since 1.5
 */
class JCEControllerPopup extends JController
{
	/**
	 * Constructor
	 *
	 * @params	array	Controller configuration array
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask( 'popup', 'display' );		
	}

	/**
	 * Displays a view
	 */
	function display()
	{		
		parent::display();
	}
}