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
use Html;
use Lang;
use App;

/**
 * Supports an HTML select list of plugins
 */
class Ordering extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Ordering';

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
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get some field values from the form.
		$pluginId = (int) $this->form->getValue('extension_id');
		$folder = $this->form->getValue('folder');

		$db = App::get('db');

		// Build the query for the ordering list.
		$query = 'SELECT ordering AS value, name AS text, type AS type, folder AS folder, extension_id AS extension_id
				FROM `#__extensions`
				WHERE (type =' . $db->Quote('plugin') . ' AND folder=' . $db->Quote($folder) . ')
				ORDER BY ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = self::ordering('', $query, trim($attr), $this->value, $pluginId ? 0 : 1);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
		}
		// Create a regular list.
		else
		{
			$html[] = self::ordering($this->name, $query, trim($attr), $this->value, $pluginId ? 0 : 1);
		}

		return implode($html);
	}

	/**
	 * Returns an array of options
	 *
	 * @param   string   $sql  	SQL with 'ordering' AS value and 'name field' AS text
	 * @param   integer  $chop  The length of the truncated headline
	 * @return  array    An array of objects formatted for JHtml list processing
	 */
	public static function genericordering($sql, $chop = '30')
	{
		$db = App::get('db');
		$options = array();
		$db->setQuery($sql);

		$items = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			App::abort(500, $db->getErrorMsg());
		}

		if (empty($items))
		{
			$options[] = Html::select('option', 1, Lang::txt('JOPTION_ORDER_FIRST'));
			return $options;
		}

		$options[] = Html::select('option', 0, '0 ' . Lang::txt('JOPTION_ORDER_FIRST'));
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$items[$i]->text = Lang::txt($items[$i]->text);
			if (strlen($items[$i]->text) > $chop)
			{
				$text = substr($items[$i]->text, 0, $chop) . '...';
			}
			else
			{
				$text = $items[$i]->text;
			}

			$options[] = Html::select('option', $items[$i]->value, $items[$i]->value . '. ' . $text);
		}
		$options[] = Html::select('option', $items[$i - 1]->value + 1, ($items[$i - 1]->value + 1) . ' ' . Lang::txt('JOPTION_ORDER_LAST'));

		return $options;
	}

	/**
	 * Build the select list for Ordering derived from a query
	 *
	 * @param   integer  $name      The scalar value
	 * @param   string   $query     The query
	 * @param   string   $attribs   HTML tag attributes
	 * @param   string   $selected  The selected item
	 * @param   integer  $neworder  1 if new and first, -1 if new and last, 0  or null if existing item
	 * @param   string   $chop      The length of the truncated headline
	 * @return  string   Html for the select list
	 */
	public static function ordering($name, $query, $attribs = null, $selected = null, $neworder = null, $chop = null)
	{
		if (empty($attribs))
		{
			$attribs = 'class="inputbox" size="1"';
		}

		if (empty($neworder))
		{
			$orders = self::genericordering($query);
			$html = Html::select('genericlist', $orders, $name, array('list.attr' => $attribs, 'list.select' => (int) $selected));
		}
		else
		{
			if ($neworder > 0)
			{
				$text = Lang::txt('JGLOBAL_NEWITEMSLAST_DESC');
			}
			elseif ($neworder <= 0)
			{
				$text = Lang::txt('JGLOBAL_NEWITEMSFIRST_DESC');
			}
			$html = '<input type="hidden" name="' . $name . '" value="' . (int) $selected . '" />' . '<span class="readonly">' . $text . '</span>';
		}
		return $html;
	}
}
