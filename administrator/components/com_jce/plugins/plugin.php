<?php
/**
 * @version		plugin.php 08/03/2009
 * @package		JCE.Administration
 * @copyright	Copyright (C) 2006 - 2009 Ryan Demmer. All rights reserved.
 * @license		GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
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
class JCETablePlugin extends JTable
{
	/**
	 * Primary Key
	 *
	 *  @var int
	 */
	var $id = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	var $title = null;

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
	var $type = 'plugin';
	
	/**
	 *
	 *
	 * @var varchar
	 */
	var $icon = null;
	
	/**
	 *
	 *
	 * @var varchar
	 */
	var $layout = null;

	/**
	 *
	 *
	 * @var int
	 */
	var $row = 0;
	
	/**
	 *
	 *
	 * @var int
	 */
	var $ordering = 0;

	/**
	 *
	 *
	 * @var tinyint
	 */
	var $published = 1;
	
	/**
	 *
	 *
	 * @var tinyint
	 */
	var $editable = 1;

	/**
	 *
	 *
	 * @var tinyint
	 */
	var $iscore = 0;
	
	// Legacy
	
	var $elements = null;

	/**
	 *
	 *
	 * @var int unsigned
	 */
	var $checked_out = 0;

	/**
	 *
	 *
	 * @var datetime
	 */
	var $checked_out_time = 0;

	function __construct(& $db) {
		parent::__construct('#__jce_plugins', 'id', $db);
	}

	/**
	* Overloaded bind function
	*
	* @access public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	function bind($array, $ignore = '')
	{
		if (isset( $array['params'] ) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}
}
?>