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
 * Database query element base class
 */
abstract class Base
{
	/**
	 * The query element constraints
	 *
	 * @var array
	 **/
	protected $constraints = null;

	/**
	 * The database connection object
	 *
	 * @var object
	 **/
	protected $connection = null;

	/**
	 * Constructs query element class, setting database connection
	 *
	 * @param  object $connection the database connection to use
	 * @return void
	 * @since  1.3.2
	 **/
	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Should return string representation of query element for use in query
	 *
	 * @return string
	 * @since  1.3.2
	 **/
	public abstract function toString();

	/**
	 * Constrains a query element
	 *
	 * @param  mixed $constraint the constraint to set
	 * @return void
	 * @since  1.3.2
	 **/
	public function constrain($constraint)
	{
		$this->addConstraint($constraint);
	}

	/**
	 * Sets the query element constraint, overwritting it if it already exists
	 *
	 * @param  mixed|string $constraint the query constraint
	 *                                  If the value is set, this becomes the constraint array key
	 * @param  mixed|null   $value the query constraint
	 * @return void
	 * @since  1.3.2
	 **/
	protected function setConstraint($constraint, $value=null)
	{
		if (isset($value))
		{
			$this->constraints[$constraint] = $value;
		}
		else
		{
			$this->constraints = $constraint;
		}
	}

	/**
	 * Adds a new query element constraint, appending to the existing array
	 *
	 * @param  mixed $constraint the constraint to add
	 * @return void
	 * @since  1.3.2
	 **/
	protected function addConstraint($constraint)
	{
		$this->constraints[] = $constraint;
	}

	/**
	 * Clears existing constraints
	 *
	 * @return void
	 * @since  1.3.2
	 **/
	protected function clearConstraints()
	{
		$this->constraints = null;
	}
}