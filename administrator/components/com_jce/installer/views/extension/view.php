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
 * Extension Manager Plugins View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
include_once(dirname(__FILE__).DS.'..'.DS.'default'.DS.'view.php');

class InstallerViewExtension extends InstallerViewDefault
{
	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::deleteList( '', 'remove', 'Uninstall' );
		JCEToolBarHelper::help( 'install.plugin' );

		// Get data from the model
		$items		= &$this->get('Items');
		$pagination	= &$this->get('Pagination');

		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		
		parent::display($tpl);
	}

	function loadItem($index=0)
	{
		$item =& $this->items[$index];
		$item->index	= $index;

		$item->cbd		= null;
		$item->style	= null;

		$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;

		$this->assignRef('item', $item);
	}
}