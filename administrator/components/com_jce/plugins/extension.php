<?php
/**
 * @version		$Id: extension.php 47 2009-05-26 18:06:30Z happynoodleboy $
 * @package		Joomla.Framework
 * @subpackage	Table
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Plugin table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JCETableExtension extends JTable
{
	/**
	 * Primary Key
	 *
	 *  @var int
	 */
	var $id = null;
	
	/**
	*  @var int
	 */
	var $pid = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	var $name = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	var $extension = null;
	/**
	 *
	 *
	 * @var varchar
	 */
	var $folder = null;

	/**
	 *
	 *
	 * @var tinyint
	 */
	var $published = 1;

	function __construct(& $db) {
		parent::__construct('#__jce_extensions', 'id', $db);
	}
}
?>