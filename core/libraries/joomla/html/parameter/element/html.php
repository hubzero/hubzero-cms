<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


defined('JPATH_BASE') or die();

class JElementHTML extends JElement
{
	var	$_name = 'HTML';

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $node->_data;
	}
}
