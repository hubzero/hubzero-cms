<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Renders a textarea element
 */
class ResourcesElementTextarea extends ResourcesElement
{
	/**
	* Element name
	*
	* @var  string
	*/
	protected $_name = 'Textarea';

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value to check against
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  string  HTML
	 */
	public function fetchElement($name, $value, &$element, $control_name)
	{
		$rows = isset($element->rows) ? $element->rows : 6;
		$cols = isset($element->cols) ? $element->cols : 50;
		$class = isset($element->class) ? 'class="' . $element->class . '"' : 'class="text_area"';
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return '<span class="field-wrap">' . JFactory::getEditor()->display($control_name . '[' . $name . ']', $value, '', '', $cols, $rows, false, $control_name . '-' . $name, null, null, array('class' => 'minimal no-footer')) . '</span>';
	}
}