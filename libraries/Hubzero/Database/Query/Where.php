<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since     Class available since release 1.3.2
 */

namespace Hubzero\Database\Query;

/**
 * Database query from element
 */
class Where extends Base
{
	/**
	 * Constrains a query element
	 *
	 * @param  mixed $constraint the constraint to set
	 * @return void
	 * @since  1.3.2
	 **/
	public function constrain($constraint)
	{
		if (!is_array($constraint))
		{
			$constraint = array(
				'column'   => func_get_arg(0),
				'operator' => func_get_arg(1),
				'value'    => func_get_arg(2),
				'logical'  => func_get_arg(3),
				'depth'    => func_get_arg(4)
			);
		}

		parent::constrain($constraint);
	}

	/**
	 * Should return string representation of query element for use in query
	 *
	 * @return string
	 * @since  1.3.2
	 **/
	public function toString()
	{
		$strings = array();
		$first   = true;
		$depth   = 0;

		foreach ($this->constraints as $constraint)
		{
			$string  = '';
			$string .= ($constraint['depth'] < $depth) ? ') ' : '';
			$string .= ($first) ? 'WHERE ' : strtoupper($constraint['logical']) . ' ';
			$string .= ($constraint['depth'] > $depth) ? '(' : '';
			$string .= $this->connection->quoteName($constraint['column']);
			$string .= ' ' . $constraint['operator'];
			if (is_array($constraint['value']))
			{
				$values = array();
				foreach ($constraint['value'] as $value)
				{
					$values[] = $this->connection->quote($value);
				}
				$string .= ' (' . ((!empty($values)) ? implode(',', $values) : "''") . ')';
			}
			else
			{
				$string .= ' ' . $this->connection->quote($constraint['value']);
			}

			$strings[] = $string;
			$first     = false;
			$depth     = $constraint['depth'];
		}

		// Catch instance where last item was at a greater depth and never got a closing ')'
		if ($depth > 0) $strings[] = str_repeat(')', $depth);

		return implode("\n", $strings);
	}
}