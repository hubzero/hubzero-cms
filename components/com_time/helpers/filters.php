<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Filters helper class for time component
 */
class TimeFilters
{
	/**
	 * Gets the request filters and returns them
	 *
	 * @param  string $namespace the application state variable namespace
	 * @return array
	 **/
	public static function getFilters($namespace)
	{
		// Set filters for view
		$app = JFactory::getApplication();

		// Process query filters
		$q = $app->getUserState("{$namespace}.query");
		if ($incoming = JRequest::getVar('q', false))
		{
			$q[] = $incoming;
		}

		// Set some defaults for the filters, if not set otherwise
		if (!is_array($q))
		{
			$q[0]['column']   = ($namespace == 'com_time.tasks') ? 'assignee_id' : 'user_id';
			$q[0]['operator'] = 'e';
			$q[0]['value']    = \JFactory::getUser()->get('id');
		}

		// Translate operators and augment query filters with human-friendly text
		$query = self::filtersMap($q);

		// Turn search into array of results, if not already
		$search = JRequest::getVar('search', $app->getUserState("{$namespace}.search", ''));
		// If we have a search and it's not an array (i.e. it's coming in fresh with this request)
		if ($search && !is_array($search))
		{
			// Explode multiple words into array
			$search = explode(" ", $search);
			// Only allow alphabetical characters for search
			$search = preg_replace("/[^a-zA-Z]/", "", $search);
		}

		// Set some values in the session
		$app->setUserState("{$namespace}.search", $search);
		$app->setUserState("{$namespace}.query",  $query);

		return array('search' => $search, 'q' => $query);
	}

	/**
	 * Get column names
	 *
	 * @param  array $table   - table to get column names for
	 * @param  array $exclude - array of columns to exclude
	 * @return $columns       - array of column names
	 */
	public static function getColumnNames($table, $exclude=array())
	{
		// Get the column names
		$prefix = JFactory::getApplication()->getCfg('dbprefix');
		$db     = JFactory::getDbo();
		$cols   = $db->getTableFields($prefix.$table);

		$columns = array();

		// Loop through them and make a guess at the human readable equivalent
		$cols = array_keys($cols[$prefix.$table]);
		foreach ($cols as $c)
		{
			if (!in_array($c, $exclude))
			{
				$human = $c;

				// Try to be tricky and remove id from column names
				if (strpos($human, '_id') > 0)
				{
					$human = str_replace('_id', '', $human);
				}

				// Now replace other instances of '_' with spaces
				$human = str_replace('_', ' ', $human);
				$human = ucwords($human);
				$columns[] = array("raw" => $c, "human" => $human);
			}
		}

		return $columns;
	}

	/**
	 * Build the operators html
	 *
	 * @return string $html - html for operators select box
	 */
	public static function buildSelectOperators()
	{
		$html  = '<select name="q[operator]" id="filter-operator">';
		$html .= '<option value="e">equals (&#61;)</option>';
		$html .= '<option value="de">doesn\'t equal (&#8800;)</option>';
		$html .= '<option value="gt">is greater than (&#62;)</option>';
		$html .= '<option value="lt">is less than (&#60;)</option>';
		$html .= '<option value="gte">is greater than or equal to (&#62;&#61;)</option>';
		$html .= '<option value="lte">is less than or equal to (&#60;&#61;)</option>';
		$html .= '<option value="like">is like (LIKE)</option>';
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
	 * @param  $q - query arguments
	 * @return void
	 */
	public static function filtersMap($q=array())
	{
		// Initialize variables
		$db        = JFactory::getDbo();
		$filters   = array();
		$return    = array();
		$dcolumn   = '';
		$doperator = '';
		$dvalue    = '';

		// First, make sure we have something to iterate over
		if (!empty($q[0]))
		{
			// Go through query filters
			foreach ($q as $val)
			{
				// Make sure we're not deleting this filter
				if (!array_key_exists('delete', $val))
				{
					// Add an if statement here if you want to augment a fields 'human readable' value
					// Augment user_id information
					if ($val['column'] == 'user_id')
					{
						$val['human_column']   = 'User';
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = JFactory::getUser($val['value'])->name;
						$filters[]  = $val;
					}
					// Augment name information for multiple fields
					elseif ($val['column'] == 'assignee_id' || $val['column'] == 'liaison_id')
					{
						$val['human_column']   = ucwords(str_replace('_id', '', $val['column']));
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = JFactory::getUser($val['value'])->name;

						if (is_null($val['human_value']))
						{
							$val['human_value'] = 'Unidentified';
						}
						$filters[]  = $val;
					}
					// Augment task_id information
					elseif ($val['column'] == 'task_id')
					{
						$val['human_column']   = 'Task';
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = Task::oneOrFail($val['value'])->name;
						$filters[]  = $val;
					}
					// Augment 'active' column information
					elseif ($val['column'] == 'active')
					{
						$val['human_column']   = ucwords($val['column']);
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = ($val['value'] == 1) ? 'yes' : 'no';
						$filters[]  = $val;
					}
					elseif ($val['column'] == 'hub_id')
					{
						$val['human_column']   = 'Hub';
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = Hub::oneOrFail($val['value'])->name;
						$filters[]  = $val;
					}
					// All others
					else
					{
						$val['human_column']   = ucwords(str_replace("_", " ", $val['column']));
						$val['o']              = self::translateOperator($val['operator']);
						$val['human_operator'] = self::mapOperator($val['o']);
						$val['human_value']    = $val['value'];
						$filters[]  = $val;
					}
				}
				else // we're establishing the details of the query filter to delete (which we'll do below)
				{
					// Values to delete
					$dcolumn   = $val['column'];
					$doperator = $val['operator'];
					$dvalue    = $val['value'];
				}
			}
		}

		// Distil down the results to only unique filters
		$filters = array_map("unserialize", array_unique(array_map("serialize", $filters)));

		// Now go through them again and only keep ones not marked for deletion (there's probably a much better way to do this)
		foreach ($filters as $filter)
		{
			if (!($filter['column'] == $dcolumn && $filter['operator'] == $doperator && $filter['value'] == $dvalue))
			{
				$return[] = $filter;
			}
		}

		return $return;
	}

	/**
	 * Override default filter values
	 *
	 * ex: change hub_id to Hub
	 *
	 * @param  $vals   - incoming values
	 * @param  $column - incoming column for which values pertain
	 * @return $return - outgoing values
	 */
	public static function filtersOverrides($vals, $column)
	{
		$db     = JFactory::getDbo();
		$return = array();

		foreach ($vals as $val)
		{
			// Just so I don't have to keep writing $val->val
			$value = $val->val;

			$x            = array();
			$x['value']   = $value;
			$x['display'] = $value;

			// Now override at will...
			if ($column == 'assignee_id' || $column == 'liaison_id' || $column == 'user_id')
			{
				$x['value'] = $value;
				$x['display'] = JFactory::getUser($value)->get('name');

				if ($value == 0)
				{
					$x['display'] = 'No User';
				}
			}
			elseif ($column == 'hub_id')
			{
				$x['value'] = $value;
				$x['display'] = Hub::oneOrFail($value)->name;
			}
			elseif ($column == 'task_id')
			{
				$x['value'] = $value;
				$x['display'] = Task::oneOrFail($value)->name;
			}
			elseif ($column == 'active')
			{
				$x['value'] = $value;
				$x['display'] = ($value) ? 'Yes' : 'No';
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
	 * @param  $o - operator of interest
	 * @return void
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
	 * @param  $o - operator of interest
	 * @return string - value of operator
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