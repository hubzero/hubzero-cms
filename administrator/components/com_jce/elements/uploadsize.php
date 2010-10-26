<?php
/**
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

* Based on section.php in joomla/libraries/html/parameter/element/ - Ryan Demmer : ryandemmer@gmail.com 05/12/2008

*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a section element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementUploadSize extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'UploadSize';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$class 	= ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
		$unit 	= $node->attributes('unit') ? $node->attributes('unit') : 'KB';
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value 	= htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
		$size 	= ini_get('upload_max_filesize');

		return '<input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" size="10" value="'.$value.'" '.$class.' /> ' . JText::_('Server Upload Size') . ' - ' . $this->getUnitValue( $size, $unit );
	}
	function getUnitValue( $value, $unit ) {
		$value 	= trim( $value );
		
		// Convert to bytes
		switch( strtolower( $value{strlen( $value )-1} ) ) {
			case 'g':
				$value *= 1073741824;
				break;
			case 'm':
				$value *= 1048576;
				break;
			case 'k':
				$value *= 1024;
				break;
		}
		// Convert to unit value
		switch( strtolower( $unit{0} ) ) {
			case 'g':
				$value /= 1073741824;
				break;
			case 'm':
				$value /= 1048576;
				break;
			case 'k':
				$value /= 1024;
				break;
		}
		return preg_replace( '/[^0-9]/', '', $value ) .' '. $unit;
	}
}
