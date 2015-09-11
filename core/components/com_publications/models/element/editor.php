<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;

/**
 * Renders an editor element
 */
class Editor extends Base
{
	/**
	* Element name
	*
	* @var  string
	*/
	protected $_name = 'Editor';

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
		$editorMacros  = isset($element->editorMacros)
						? $element->editorMacros : 0;
		$editorMinimal = isset($element->editorMinimal)
						? $element->editorMinimal : 1;
		$editorImages  = isset($element->editorImages)
						? $element->editorImages : 0;

		$classes  = $editorMinimal == 1 ? 'minimal ' : '';
		$classes .= ' no-footer ';
		$classes .= $editorImages == 1 ? 'images ' : '';
		$classes .= $editorMacros == 1 ? 'macros ' : '';

		return '<span class="field-wrap">' . \App::get('editor')->display($control_name . '[' . $name . ']', $value, '', '', $cols, $rows, false, $control_name.'-'.$name, null, null, array('class' => $classes)) . '</span>';
	}
}