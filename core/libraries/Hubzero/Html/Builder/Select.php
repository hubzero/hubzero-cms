<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Html\Builder;

use Hubzero\Utility\Arr;
use Hubzero\Base\Object;
use stdClass;
use Lang;

/**
 * Utility class for creating HTML select lists
 */
class Select
{
	/**
	 * Default values for options. Organized by option group.
	 *
	 * @var  array
	 */
	static protected $optionDefaults = array(
		'option' => array(
			'option.attr' => null,
			'option.disable' => 'disable',
			'option.id' => null,
			'option.key' => 'value',
			'option.key.toHtml' => true,
			'option.label' => null,
			'option.label.toHtml' => true,
			'option.text' => 'text',
			'option.text.toHtml' => true
		)
	);

	/**
	 * Generates a yes/no radio list.
	 *
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $selected  The key that is selected
	 * @param   string  $yes       Language key for Yes
	 * @param   string  $no        Language key for no
	 * @param   string  $id        The id for the field
	 * @return  string  HTML for the radio list
	 */
	public static function booleanlist($name, $attribs = null, $selected = null, $yes = 'JYES', $no = 'JNO', $id = false)
	{
		$arr = array(
			self::option('0', Lang::txt($no)),
			self::option('1', Lang::txt($yes))
		);
		return self::radiolist($arr, $name, $attribs, 'value', 'text', (int) $selected, $id);
	}

	/**
	 * Generates an HTML selection list.
	 *
	 * @param   array    $data       An array of objects, arrays, or scalars.
	 * @param   string   $name       The value of the HTML name attribute.
	 * @param   mixed    $attribs    Additional HTML attributes for the <select> tag. This
	 *                               can be an array of attributes, or an array of options. Treated as options
	 *                               if it is the last argument passed. Valid options are:
	 *                               Selection options
	 *                               list.attr, string|array: Additional attributes for the select
	 *                               element.
	 *                               id, string: Value to use as the select element id attribute.
	 *                               Defaults to the same as the name.
	 *                               list.select, string|array: Identifies one or more option elements
	 *                               to be selected, based on the option key values.
	 * @param   string   $optKey     The name of the object variable for the option value. If
	 *                               set to null, the index of the value array is used.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string).
	 * @param   mixed    $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True to translate
	 * @return  string   HTML for the select list.
	 */
	public static function genericlist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		// Set default options
		$options = array('id' => false);

		if (is_array($attribs) && func_num_args() == 3)
		{
			// Assume we have an options array
			$options = array_merge($options, $attribs);
		}
		else
		{
			// Get options from the parameters
			$options['id']             = $idtag;
			$options['list.attr']      = $attribs;
			$options['list.translate'] = $translate;
			$options['option.key']     = $optKey;
			$options['option.text']    = $optText;
			$options['list.select']    = $selected;
		}

		$attribs = '';
		if (isset($options['list.attr']))
		{
			if (is_array($options['list.attr']))
			{
				$attribs = Arr::toString($options['list.attr']);
			}
			else
			{
				$attribs = $options['list.attr'];
			}

			if ($attribs != '')
			{
				$attribs = ' ' . $attribs;
			}
		}

		$id = $options['id'] !== false ? $options['id'] : $name;
		$id = str_replace(array('[', ']'), '', $id);

		$html  = '<select' . ($id !== '' ? ' id="' . $id . '"' : '') . ' name="' . $name . '"' . $attribs . '>';
		$html .= self::options($data, $options);
		$html .= '</select>';

		return $html;
	}

	/**
	 * Generates a grouped HTML selection list from nested arrays.
	 *
	 * @param   array   $data     An array of groups, each of which is an array of options.
	 * @param   string  $name     The value of the HTML name attribute
	 * @param   array   $options  Options, an array of key/value pairs. Valid options are:
	 *                            Selection options.
	 *                            group.id: The property in each group to use as the group id
	 *                            attribute. Defaults to none.
	 *                            group.label: The property in each group to use as the group
	 *                            label. Defaults to "text". If set to null, the data array index key is
	 *                            used.
	 *                            group.items: The property in each group to use as the array of
	 *                            items in the group. Defaults to "items". If set to null, group.id and
	 *                            group. label are forced to null and the data element is assumed to be a
	 *                            list of selections.
	 *                            id: Value to use as the select element id attribute. Defaults to
	 *                            the same as the name.
	 *                            list.attr: Attributes for the select element. Can be a string or
	 *                            an array of key/value pairs. Defaults to none.
	 *                            list.select: either the value of one selected option or an array
	 *                            of selected options. Default: none.
	 *                            list.translate: Boolean. If set, text and labels are translated.
	 * @return  string  HTML for the select list
	 * @throws  Exception If a group has unprocessable contents.
	 */
	public static function groupedlist($data, $name, $options = array())
	{
		// Set default options and overwrite with anything passed in
		$options = array_merge(
			array(
				'group.items' => 'items',
				'group.label' => 'text',
				'group.label.toHtml' => true,
				'id' => false
			),
			$options
		);

		// Apply option rules
		if ($options['group.items'] === null)
		{
			$options['group.label'] = null;
		}

		$attribs = '';

		if (isset($options['list.attr']))
		{
			if (is_array($options['list.attr']))
			{
				$attribs = Arr::toString($options['list.attr']);
			}
			else
			{
				$attribs = $options['list.attr'];
			}
			if ($attribs != '')
			{
				$attribs = ' ' . $attribs;
			}
		}

		$id = $options['id'] !== false ? $options['id'] : $name;
		$id = str_replace(array('[', ']'), '', $id);

		// Disable groups in the options.
		$options['groups'] = false;

		$html = '<select' . ($id !== '' ? ' id="' . $id . '"' : '') . ' name="' . $name . '"' . $attribs . '>';

		foreach ($data as $dataKey => $group)
		{
			$label = $dataKey;
			$id = '';
			$noGroup = is_int($dataKey);

			if ($options['group.items'] == null)
			{
				// Sub-list is an associative array
				$subList = $group;
			}
			elseif (is_array($group))
			{
				// Sub-list is in an element of an array.
				$subList = $group[$options['group.items']];
				if (isset($group[$options['group.label']]))
				{
					$label = $group[$options['group.label']];
					$noGroup = false;
				}
				if (isset($options['group.id']) && isset($group[$options['group.id']]))
				{
					$id = $group[$options['group.id']];
					$noGroup = false;
				}
			}
			elseif (is_object($group))
			{
				// Sub-list is in a property of an object
				$subList = $group->$options['group.items'];
				if (isset($group->$options['group.label']))
				{
					$label = $group->$options['group.label'];
					$noGroup = false;
				}
				if (isset($options['group.id']) && isset($group->$options['group.id']))
				{
					$id = $group->$options['group.id'];
					$noGroup = false;
				}
			}
			else
			{
				throw new Exception('Invalid group contents.', 1, E_WARNING);
			}

			if ($noGroup)
			{
				$html .= self::options($subList, $options);
			}
			else
			{
				$html .= '<optgroup' . (empty($id) ? '' : ' id="' . $id . '"') . ' label="' . ($options['group.label.toHtml'] ? self::escape($label) : $label) . '">';
				$html .= self::options($subList, $options);
				$html .= '</optgroup>';
			}
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Generates a selection list of integers.
	 *
	 * @param   integer  $start     The start integer
	 * @param   integer  $end       The end integer
	 * @param   integer  $inc       The increment
	 * @param   string   $name      The value of the HTML name attribute
	 * @param   mixed    $attribs   Additional HTML attributes for the <select> tag, an array of
	 *                              attributes, or an array of options. Treated as options if it is the last
	 *                              argument passed.
	 * @param   mixed    $selected  The key that is selected
	 * @param   string   $format    The printf format to be applied to the number
	 * @return  string   HTML for the select list
	 */
	public static function integerlist($start, $end, $inc, $name, $attribs = null, $selected = null, $format = '')
	{
		// Set default options
		$options = array(
			'option.format' => '',
			'id' => null
		);

		if (is_array($attribs) && func_num_args() == 5)
		{
			// Assume we have an options array
			$options = array_merge($options, $attribs);
			// Extract the format and remove it from downstream options
			$format = $options['option.format'];
			unset($options['option.format']);
		}
		else
		{
			// Get options from the parameters
			$options['list.attr'] = $attribs;
			$options['list.select'] = $selected;
		}

		$start = intval($start);
		$end   = intval($end);
		$inc   = intval($inc);

		$data = array();
		for ($i = $start; $i <= $end; $i += $inc)
		{
			$data[$i] = $format ? sprintf($format, $i) : $i;
		}

		// Tell genericlist() to use array keys
		$options['option.key'] = null;

		return self::genericlist($data, $name, $options);
	}

	/**
	 * Create a placeholder for an option group.
	 *
	 * @param   string  $text     The text for the option
	 * @param   string  $optKey   The returned object property name for the value
	 * @param   string  $optText  The returned object property name for the text
	 * @return  object
	 * @deprecated  Use self::groupedList()
	 */
	public static function optgroup($text, $optKey = 'value', $optText = 'text')
	{
		// Set initial state
		static $state = 'open';

		// Toggle between open and close states:
		switch ($state)
		{
			case 'open':
				$obj = new stdClass;
				$obj->$optKey = '<OPTGROUP>';
				$obj->$optText = $text;
				$state = 'close';
				break;
			case 'close':
				$obj = new stdClass;
				$obj->$optKey = '</OPTGROUP>';
				$obj->$optText = $text;
				$state = 'open';
				break;
		}

		return $obj;
	}

	/**
	 * Create an object that represents an option in an option list.
	 *
	 * @param   string   $value    The value of the option
	 * @param   string   $text     The text for the option
	 * @param   mixed    $optKey   If a string, the returned object property name for
	 *                             the value. If an array, options. Valid options are:
	 *                             attr: String|array. Additional attributes for this option.
	 *                             Defaults to none.
	 *                             disable: Boolean. If set, this option is disabled.
	 *                             label: String. The value for the option label.
	 *                             option.attr: The property in each option array to use for
	 *                             additional selection attributes. Defaults to none.
	 *                             option.disable: The property that will hold the disabled state.
	 *                             Defaults to "disable".
	 *                             option.key: The property that will hold the selection value.
	 *                             Defaults to "value".
	 *                             option.label: The property in each option array to use as the
	 *                             selection label attribute. If a "label" option is provided, defaults to
	 *                             "label", if no label is given, defaults to null (none).
	 *                             option.text: The property that will hold the the displayed text.
	 *                             Defaults to "text". If set to null, the option array is assumed to be a
	 *                             list of displayable scalars.
	 * @param   string   $optText  The property that will hold the the displayed text. This
	 *                             parameter is ignored if an options array is passed.
	 * @param   boolean  $disable  Not used.
	 * @return  object
	 */
	public static function option($value, $text = '', $optKey = 'value', $optText = 'text', $disable = false)
	{
		$options = array(
			'attr'           => null,
			'disable'        => false,
			'option.attr'    => null,
			'option.disable' => 'disable',
			'option.key'     => 'value',
			'option.label'   => null,
			'option.text'    => 'text'
		);

		if (is_array($optKey))
		{
			// Merge in caller's options
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key']  = $optKey;
			$options['option.text'] = $optText;
			$options['disable']     = $disable;
		}

		$obj = new Object;
		$obj->$options['option.key'] = $value;
		$obj->$options['option.text'] = trim($text) ? $text : $value;

		// If a label is provided, save it. If no label is provided and there is
		// a label name, initialise to an empty string.
		$hasProperty = $options['option.label'] !== null;
		if (isset($options['label']))
		{
			$labelProperty = $hasProperty ? $options['option.label'] : 'label';
			$obj->$labelProperty = $options['label'];
		}
		elseif ($hasProperty)
		{
			$obj->$options['option.label'] = '';
		}

		// Set attributes only if there is a property and a value
		if ($options['attr'] !== null)
		{
			$obj->$options['option.attr'] = $options['attr'];
		}

		// Set disable only if it has a property and a value
		if ($options['disable'] !== null)
		{
			$obj->$options['option.disable'] = $options['disable'];
		}
		return $obj;
	}

	/**
	 * Generates the option tags for an HTML select list (with no select tag
	 * surrounding the options).
	 *
	 * @param   array    $arr        An array of objects, arrays, or values.
	 * @param   mixed    $optKey     If a string, this is the name of the object variable for
	 *                               the option value. If null, the index of the array of objects is used. If
	 *                               an array, this is a set of options, as key/value pairs. Valid options are:
	 *                               -groups: Boolean. If set, looks for keys with the value
	 *                                "&lt;optgroup>" and synthesizes groups from them. Deprecated. Defaults
	 *                                true for backwards compatibility.
	 *                               -list.select: either the value of one selected option or an array
	 *                                of selected options. Default: none.
	 *                               -list.translate: Boolean. If set, text and labels are translated via
	 *                                JText::_(). Default is false.
	 *                               -option.id: The property in each option array to use as the
	 *                                selection id attribute. Defaults to none.
	 *                               -option.key: The property in each option array to use as the
	 *                                selection value. Defaults to "value". If set to null, the index of the
	 *                                option array is used.
	 *                               -option.label: The property in each option array to use as the
	 *                                selection label attribute. Defaults to null (none).
	 *                               -option.text: The property in each option array to use as the
	 *                               displayed text. Defaults to "text". If set to null, the option array is
	 *                               assumed to be a list of displayable scalars.
	 *                               -option.attr: The property in each option array to use for
	 *                                additional selection attributes. Defaults to none.
	 *                               -option.disable: The property that will hold the disabled state.
	 *                                Defaults to "disable".
	 *                               -option.key: The property that will hold the selection value.
	 *                                Defaults to "value".
	 *                               -option.text: The property that will hold the the displayed text.
	 *                               Defaults to "text". If set to null, the option array is assumed to be a
	 *                               list of displayable scalars.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string)
	 * @param   boolean  $translate  Translate the option values.
	 *
	 * @return  string  HTML for the select list
	 *
	 * @since   11.1
	 */
	public static function options($arr, $optKey = 'value', $optText = 'text', $selected = null, $translate = false)
	{
		$options = array_merge(
			self::$optionDefaults['option'],
			array(
				'groups' => true,
				'list.select' => null,
				'list.translate' => false
			)
		);

		if (is_array($optKey))
		{
			// Set default options and overwrite with anything passed in
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key'] = $optKey;
			$options['option.text'] = $optText;
			$options['list.select'] = $selected;
			$options['list.translate'] = $translate;
		}

		$html = '';

		foreach ($arr as $elementKey => &$element)
		{
			$attr  = '';
			$extra = '';
			$label = '';
			$id    = '';
			if (is_array($element))
			{
				$key  = $options['option.key'] === null ? $elementKey : $element[$options['option.key']];
				$text = $element[$options['option.text']];
				if (isset($element[$options['option.attr']]))
				{
					$attr = $element[$options['option.attr']];
				}
				if (isset($element[$options['option.id']]))
				{
					$id = $element[$options['option.id']];
				}
				if (isset($element[$options['option.label']]))
				{
					$label = $element[$options['option.label']];
				}
				if (isset($element[$options['option.disable']]) && $element[$options['option.disable']])
				{
					$extra .= ' disabled="disabled"';
				}
			}
			elseif (is_object($element))
			{
				$key = $options['option.key'] === null ? $elementKey : $element->$options['option.key'];
				$text = $element->$options['option.text'];
				if (isset($element->$options['option.attr']))
				{
					$attr = $element->$options['option.attr'];
				}
				if (isset($element->$options['option.id']))
				{
					$id = $element->$options['option.id'];
				}
				if (isset($element->$options['option.label']))
				{
					$label = $element->$options['option.label'];
				}
				if (isset($element->$options['option.disable']) && $element->$options['option.disable'])
				{
					$extra .= ' disabled="disabled"';
				}
			}
			else
			{
				// This is a simple associative array
				$key  = $elementKey;
				$text = $element;
			}

			// The use of options that contain optgroup HTML elements was
			// somewhat hacked for J1.5. J1.6 introduces the grouplist() method
			// to handle this better. The old solution is retained through the
			// "groups" option, which defaults true in J1.6, but should be
			// deprecated at some point in the future.

			$key = (string) $key;
			if ($options['groups'] && $key == '<OPTGROUP>')
			{
				$html .= '<optgroup label="' . ($options['list.translate'] ? Lang::txt($text) : $text) . '">';
			}
			elseif ($options['groups'] && $key == '</OPTGROUP>')
			{
				$html .= '</optgroup>';
			}
			else
			{
				// if no string after hyphen - take hyphen out
				$splitText = explode(' - ', $text, 2);
				$text = $splitText[0];
				if (isset($splitText[1]))
				{
					$text .= ' - ' . $splitText[1];
				}

				if ($options['list.translate'] && !empty($label))
				{
					$label = Lang::txt($label);
				}
				if ($options['option.label.toHtml'])
				{
					$label = htmlentities($label);
				}
				if (is_array($attr))
				{
					$attr = Arr::toString($attr);
				}
				else
				{
					$attr = trim($attr);
				}

				$extra = ($id ? ' id="' . $id . '"' : '') . ($label ? ' label="' . $label . '"' : '') . ($attr ? ' ' . $attr : '') . $extra;

				if (is_array($options['list.select']))
				{
					foreach ($options['list.select'] as $val)
					{
						$key2 = is_object($val) ? $val->$options['option.key'] : $val;
						if ($key == $key2)
						{
							$extra .= ' selected="selected"';
							break;
						}
					}
				}
				elseif ((string) $key == (string) $options['list.select'])
				{
					$extra .= ' selected="selected"';
				}

				if ($options['list.translate'])
				{
					$text = Lang::txt($text);
				}

				// Generate the option, encoding as required
				$html .= '<option value="' . ($options['option.key.toHtml'] ? self::escape($key) : $key) . '"' . $extra . '>';
				$html .= $options['option.text.toHtml'] ? self::escape(html_entity_decode($text, ENT_COMPAT, 'UTF-8')) : $text;
				$html .= '</option>' . "\n";
			}
		}

		return $html;
	}

	/**
	 * Generates an HTML radio list.
	 *
	 * @param   array    $data       An array of objects
	 * @param   string   $name       The value of the HTML name attribute
	 * @param   string   $attribs    Additional HTML attributes for the <select> tag
	 * @param   mixed    $optKey     The key that is selected
	 * @param   string   $optText    The name of the object variable for the option value
	 * @param   string   $selected   The name of the object variable for the option text
	 * @param   boolean  $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True if options will be translated
	 * @return  string HTML for the select list
	 */
	public static function radiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		reset($data);

		$html = '';

		if (is_array($attribs))
		{
			$attribs = Arr::toString($attribs);
		}

		$id_text = $idtag ? $idtag : $name;

		foreach ($data as $obj)
		{
			$k  = $obj->$optKey;
			$t  = $translate ? Lang::txt($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);

			$extra = $id ? ' id="' . $obj->id . '"' : '';

			if (is_array($selected))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object($val) ? $val->$optKey : $val;
					if ($k == $k2)
					{
						$extra .= ' selected="selected"';
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k == (string) $selected ? ' checked="checked"' : '');
			}

			$html .= '<label for="' . $id_text . $k . '"' . ' id="' . $id_text . $k . '-lbl" class="radiobtn option">' . "\n";
			$html .= '<input type="radio" name="' . $name . '"' . ' id="' . $id_text . $k . '" value="' . $k . '"' . ' ' . $extra . ' ' . $attribs . '/>' . $t . "\n";
			$html .= '</label>' . "\n";
		}
		$html .= "\n";
		return $html;
	}

	/**
	 * Escape text
	 *
	 * @param   string  $text
	 * @return  string
	 */
	protected static function escape($str)
	{
		return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
	}
}
