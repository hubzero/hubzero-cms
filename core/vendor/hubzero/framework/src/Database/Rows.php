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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database;

use Iterator;

/**
 * Database Iterable class
 */
class Rows implements Iterator
{
	/*
	 * Errors trait for error handling
	 **/
	use Traits\ErrorBag;

	/**
	 * Internal array of iterable data
	 *
	 * @var  array
	 **/
	private $rows = NULL;

	/**
	 * Order by used to retrieve these rows
	 *
	 * @var  string
	 **/
	public $orderBy = 'id';

	/**
	 * Order direction used to retrieve these rows
	 *
	 * @var  string
	 **/
	public $orderDir = 'asc';

	/**
	 * The pagination object based on these rows
	 *
	 * @var  \Hubzero\Database\Pagination
	 **/
	public $pagination = null;

	/**
	 * Pushes a new model on to the stack
	 *
	 * @param   \Hubzero\Database\Relational|static  $model  The model to add
	 * @return  void
	 * @since   2.0.0
	 **/
	public function push(Relational $model)
	{
		// Index by primary key if possible, otherwise plain incremental array
		// Also check to see if that key already exists.  If so, we'll just start
		// appending items to the array.  This will result in a mixed array and 
		// subsequent items will not be seekable.
		if ($model->getPkValue() && (!is_array($this->rows) || !array_key_exists($model->getPkValue(), $this->rows)))
		{
			$this->rows[$model->getPkValue()] = $model;
		}
		else
		{
			$this->rows[] = $model;
		}
	}

	/**
	 * Removes model from the stack
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public function drop($key)
	{
		unset($this->rows[$key]);
	}

	/**
	 * Clears out any existing rows
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public function clear()
	{
		$this->rows = null;
	}

	/**
	 * Transforms rows into a JSON array
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public function toJson()
	{
		return $this->to('json');
	}

	/**
	 * Transforms rows into an object array
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public function toObject()
	{
		return $this->to('object');
	}

	/**
	 * Transforms rows into an array of arrays
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public function toArray()
	{
		return $this->to();
	}

	/**
	 * Outputs rows as given type
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public function to($type = 'array')
	{
		$rows = [];

		if ($this->rows && $this->count())
		{
			foreach ($this->rows as $row)
			{
				$method = 'to' . ucfirst($type);
				$rows[] = $row->$method();
			}
		}

		return $rows;
	}

	/**
	 * Grabs the raw rows out of the iterator
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public function raw()
	{
		return $this->rows;
	}

	/**
	 * Gets current row in array of rows
	 *
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function current()
	{
		return current($this->rows);
	}

	/**
	 * Gets the current key
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function key()
	{
		if (isset($this->rows))
		{
			return key($this->rows);
		}
	}

	/**
	 * Returns the result keys for the current dataset
	 *
	 * @param   string  $key  The key for which to pull all values
	 * @return  array
	 * @since   2.0.0
	 **/
	public function fieldsByKey($key)
	{
		$keys = array();

		if ($this->rows && $this->count())
		{
			foreach ($this->rows as $row)
			{
				$keys[] = $row->$key;
			}
		}

		return $keys;
	}

	/**
	 * Gets first item from rows property, if set
	 *
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function first()
	{
		return reset($this->rows);
	}

	/**
	 * Gets next item in iterable list
	 *
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function next()
	{
		return next($this->rows);
	}

	/**
	 * Rewinds rows back to start
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public function rewind()
	{
		if (isset($this->rows))
		{
			reset($this->rows);
		}
	}

	/**
	 * Fast-forwards to the end of the iterable list
	 *
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function last()
	{
		return end($this->rows);
	}

	/**
	 * Validates current key
	 *
	 * @return  bool
	 * @since   2.0.0
	 **/
	public function valid()
	{
		$valid = false;

		if ($this->rows && $this->count())
		{
			$key   = key($this->rows);
			$valid = ($key !== NULL && $key !== FALSE);
		}

		return $valid;
	}

	/**
	 * Counts the number of rows
	 *
	 * @return  int  number of rows
	 * @since   2.0.0
	 **/
	public function count()
	{
		return count($this->rows);
	}

	/**
	 * Seeks to the given key
	 *
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function seek($key)
	{
		return isset($this->rows[$key]) ? $this->rows[$key] : false;
	}

	/**
	 * Search for the given key/value pair, returning false if not found
	 *
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function search($key, $value)
	{
		foreach ($this->rows as $row)
		{
			if ($row->$key == $value)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Sorts the rows by a given field
	 *
	 * @param   string  $field  The field to sort by
	 * @param   bool    $asc    True if sort direction is ascending, false for descending
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function sort($field, $asc = true)
	{
		usort($this->rows, function($a, $b) use ($field, $asc)
		{
			$result = strcmp($a->$field, $b->$field);

			return ($asc) ? $result : $result * -1;
		});

		return $this;
	}

	/**
	 * Retrieves only the most recent applicable row
	 *
	 * @param   string  $limiter  The column name to use to determine the latest row
	 * @return  \Hubzero\Database\Relational|static
	 * @since   2.0.0
	 **/
	public function latest($limiter = 'created')
	{
		return $this->sort($limiter, false)->first();
	}

	/**
	 * Saves a collection of models
	 *
	 * @return  bool
	 * @since   2.0.0
	 **/
	public function save()
	{
		if ($this->count())
		{
			foreach ($this->rows as $model)
			{
				if (!$model->save())
				{
					$this->setErrors($model->getErrors());
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Deletes all models in this collection
	 *
	 * @return  bool
	 * @since   2.0.0
	 **/
	public function destroyAll()
	{
		// @FIXME: could make this a single query...
		if ($this->count())
		{
			foreach ($this->rows as $model)
			{
				if (!$model->destroy())
				{
					$this->setErrors($model->getErrors());
					return false;
				}
			}
		}

		return true;
	}
}