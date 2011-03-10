<?php
/**
* @version		
* @package		Joomdle
* @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');

/**
 * Renders a list element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementRole extends JElement
{
	/**
	* Element type
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Role';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );

		$roles = JoomdleHelperContent::call_method ('get_roles');

		$options = array ();
		if (is_array ($roles))
		{
			foreach ($roles as $role)
			{
				$val = $role['id'];
				$text = $role['name'];
				$options[] = JHTML::_('select.option', $val, JText::_($text));
			}
		}
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('CJ SELECT ROLE').' -'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
	}
}
