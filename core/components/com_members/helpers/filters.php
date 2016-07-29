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

namespace Components\Members\Helpers;

use Components\Members\Models\Profile\Field;
use Request;
use User;
use Lang;

/**
 * Filters helper class for time component
 */
class Filters
{
	/**
	 * Gets the request filters and returns them
	 *
	 * @param   string  $namespace  The application state variable namespace
	 * @return  array
	 **/
	public static function getFilters($namespace)
	{
		// Process query filters
		$q = Request::getVar('q', array());

		// Translate operators and augment query filters with human-friendly text
		$query = self::filtersMap($q);

		// Turn search into array of results, if not already
		$search = Request::getVar('search', '');

		// If we have a search and it's not an array (i.e. it's coming in fresh with this request)
		if ($search && !is_array($search))
		{
			// Explode multiple words into array
			$search = explode(' ', $search);

			// Only allow alphabetical characters for search
			$search = preg_replace("/[^a-zA-Z]/", '', $search);
		}

		$tags = Request::getVar('tags', '');

		return array(
			'search' => $search,
			'q'      => $query,
			'tags'   => $tags,
			'sortby' => strtolower(Request::getWord('sortby', 'surname'))
		);
	}

	/**
	 * Gets the field names
	 *
	 * @param   array  $exclude  An array of columns to exclude
	 * @return  array
	 */
	public static function getFieldNames($exclude=array())
	{
		// Get the field names
		$columns = array();

		$columns[] = array(
			'raw'   => 'name',
			'human' => Lang::txt('COM_MEMBERS_FIELD_NAME')
		);

		$fields = Field::all()
			->whereIn('action_browse', User::getAuthorisedViewLevels())
			->where('type', '!=', 'tags')
			->ordered()
			->rows();

		foreach ($fields as $field)
		{
			if (in_array($field->get('name'), $exclude))
			{
				continue;
			}

			$columns[] = array(
				'raw'   => $field->get('name'),
				'human' => $field->get('label')
			);
		}

		return $columns;
	}

	/**
	 * Build the operators html
	 *
	 * @return  string  Html for operators select box
	 */
	public static function buildSelectOperators()
	{
		$html  = '<select name="q[0][operator]" id="filter-operator">';
		$html .= '	<option value="like">contains (LIKE)</option>';
		$html .= '	<option value="e">equals (&#61;)</option>';
		$html .= '	<option value="de">doesn\'t equal (&#8800;)</option>';
		$html .= '	<option value="gt">is greater than (&#62;)</option>';
		$html .= '	<option value="lt">is less than (&#60;)</option>';
		$html .= '	<option value="gte">is greater than or equal to (&#62;&#61;)</option>';
		$html .= '	<option value="lte">is less than or equal to (&#60;&#61;)</option>';
		$html .= '</select>';

		return $html;
	}

	/**
	 * Augment query filters
	 *
	 * Here we're basically modifying what we get from the database,
	 * either by changing the display column (ex: 'user_id' => 'User'),
	 * or by changing the display value (ex: '1' to 'yes', or '1000' to Sam Wilson)
	 *
	 * @param   array  $q  Query arguments
	 * @return  void
	 */
	public static function filtersMap($q=array())
	{
		// Initialize variables
		$filters   = [];
		$return    = [];
		$dcolumn   = '';
		$doperator = '';
		$dvalue    = '';

		// First, make sure we have something to iterate over
		if (!empty($q))
		{
			// Go through query filters
			foreach ($q as $val)
			{
				$val['human_field']    = ucwords(str_replace('_', ' ', $val['field']));
				$val['o']              = self::translateOperator($val['operator']);
				$val['human_operator'] = self::mapOperator($val['o']);
				$val['human_value']    = $val['value'];

				$filters[]  = $val;
			}
		}

		// Distil down the results to only unique filters
		$filters = array_map('unserialize', array_unique(array_map('serialize', $filters)));

		return $filters;
	}

	/**
	 * Override default filter values
	 *
	 * ex: change hub_id to Hub
	 *
	 * @param   array   $vals    incoming values
	 * @param   string  $column  incoming column for which values pertain
	 * @return  array   $return  outgoing values
	 */
	public static function filtersOverrides($vals, $column)
	{
		$return = [];

		foreach ($vals as $val)
		{
			// Just so I don't have to keep writing $val->val
			$value = $val->val;

			$x            = array();
			$x['value']   = $value;
			$x['display'] = $value;

			// Now override at will...
			if ($column == 'uidNumber' || $column == 'id')
			{
				$x['value']   = $value;
				$x['display'] = User::getInstance($value)->get('name');

				if ($value == 0)
				{
					$x['display'] = 'No User';
				}
			}

			$return[] = $x;
		}

		// Get an array of kays for sorting purposes
		// We do this here, as opposed to in the query, because the data could have been modified at this point by the overrides above
		foreach ($return as $key => $row)
		{
			$display[$key] = $row['display'];
		}

		// Do the sort
		array_multisort($display, SORT_ASC, $return);

		return $return;
	}

	/**
	 * Translate operators from form value to database operator
	 *
	 * @param   string  $o  Operator of interest
	 * @return  void
	 */
	private static function translateOperator($o)
	{
		if ($o == 'e')
		{
			return '=';
		}
		elseif ($o == 'de')
		{
			return '!=';
		}
		elseif ($o == 'gt')
		{
			return '>';
		}
		elseif ($o == 'gte')
		{
			return '>=';
		}
		elseif ($o == 'lt')
		{
			return '<';
		}
		elseif ($o == 'lte')
		{
			return '<=';
		}
		elseif ($o == 'like')
		{
			return 'LIKE';
		}
		return $o;
	}

	/**
	 * Map operator symbol to text equivalent (ex: '>' = 'is greater than')
	 *
	 * @param   string  $o  Operator of interest
	 * @return  string  Value of operator
	 */
	private static function mapOperator($o)
	{
		if ($o == '=')
		{
			return 'is';
		}
		elseif ($o == '!=')
		{
			return 'is not';
		}
		elseif ($o == '>')
		{
			return 'is greater than';
		}
		elseif ($o == '>=')
		{
			return 'is greater than or equal to';
		}
		elseif ($o == '<')
		{
			return 'is less than';
		}
		elseif ($o == '<=')
		{
			return 'is less than or equal to';
		}
		elseif ($o == 'LIKE')
		{
			return 'is like';
		}
		return $o;
	}
}