<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Lang;
use App;

/**
 * Form Field class for module ordering
 */
class ModuleOrder extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'ModuleOrder';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		$html[] = '<script type="text/javascript">';

		$ordering = $this->form->getValue('ordering');
		$position = $this->form->getValue('position');
		$clientId = $this->form->getValue('client_id');

		$html[] = 'var originalOrder = "'.$ordering.'";';
		$html[] = 'var originalPos = "'.$position.'";';
		$html[] = 'var orders = new Array();';

		$db = App::get('db');
		$query = $db->getQuery()
			->select('position')
			->select('ordering')
			->select('title')
			->from('#__modules')
			->whereEquals('client_id', (int) $clientId)
			->order('ordering', 'asc');

		$db->setQuery($query->toString());
		$orders = $db->loadObjectList();
		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		$orders2 = array();
		for ($i = 0, $n = count($orders); $i < $n; $i++)
		{
			if (!isset($orders2[$orders[$i]->position]))
			{
				$orders2[$orders[$i]->position] = 0;
			}
			$orders2[$orders[$i]->position]++;
			$ord = $orders2[$orders[$i]->position];
			$title = Lang::txt('COM_MODULES_OPTION_ORDER_POSITION', $ord, addslashes($orders[$i]->title));

			$html[] = 'orders['.$i.'] =  new Array("'.$orders[$i]->position.'","'.$ord.'","'.$title.'");';
		}

		$html[] = 'writeDynaList(\'name="'.$this->name.'" id="'.$this->id.'"'.$attr.'\', orders, originalPos, originalPos, originalOrder);';
		$html[] = '</script>';

		return implode("\n", $html);
	}
}
