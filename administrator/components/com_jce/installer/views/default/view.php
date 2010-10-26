<?php
/**
 * @version		$Id: view.php 47 2009-05-26 18:06:30Z happynoodleboy $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * Extension Manager Default View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerViewDefault extends JView
{
	function __construct($config = null)
	{
		parent::__construct( array(
			'base_path' =>  JPATH_COMPONENT .DS. 'installer'
		) );
		$this->_addPath('template', dirname(__FILE__).DS.'tmpl');
	}

	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::title( JText::_( 'JCE Installer'), 'install.png' );

		// Document
		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('JCE Installer').' : '.JText::_( $this->getName() ));

		// Get data from the model
		$state		= &$this->get('State');

		// Are there messages to display ?
		$showMessage	= false;
		if ( is_object($state) )
		{
			$message1		= $state->get('message');
			$message2		= $state->get('extension.message');
			$showMessage	= ( $message1 || $message2 );
		}

		$this->assign('showMessage',	$showMessage);
		$this->assignRef('state',		$state);

		JHTML::_('behavior.tooltip');
		parent::display($tpl);
	}

	/**
	 * Should be overloaded by extending view
	 *
	 * @param	int $index
	 */
	function loadItem($index=0)
	{
	}
}