<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Iterator;
use Countable;

/**
 * Database Iterable class
 */
class Rows implements Iterator, Countable
{

	public function __construct($rows = array())
	{
		// While arrays are traversable with foreach,
		// they will not return true as an instance of Traverable
		if (is_array($rows) || $rows instanceof \Traversable)
		{
			foreach ($rows as $row)
			{
				$this->push($row);
			}
		}
	}

	/*
	 * Errors trait for error handling
	 **/
	use Traits\ErrorBag;

	/**
	 * Internal array of iterable data
	 *
	 * @var  array
	 **/
	private $rows = array();

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
	 * Calls the given array function on the rows object and attaches itself to the model
	 *
	 * @return  mixed
	 * @since   2.1.0
	 **/
	private function callArrayFunc($function)
	{
		$row = $function($this->rows);

		return ($row) ? $row->setIterator($this) : $row;
	}

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
		$this->rows = array();
	}

	/**
	 * Selects a number of randomly selected rows
	 *
	 * @param   integer  $n  The number of rows to randomly select
	 * @return  Rows
	 * @since   2.1.13
	 **/
	public function pickRandom($n)
	{
		$rows = $this->rows;

		shuffle($rows);
		$randomRows = array_slice($rows, 0, $n);
		$rowsObject = new self($randomRows);

		return $rowsObject;
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
		return $this->callArrayFunc('current');
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
		return $this->callArrayFunc('reset');
	}

	/**
	 * Gets previous item in iterable list
	 *
	 * @return  mixed
	 * @since   2.1.0
	 **/
	public function prev()
	{
		return $this->callArrayFunc('prev');
	}

	/**
	 * Gets next item in iterable list
	 *
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function next()
	{
		return $this->callArrayFunc('next');
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
		return $this->callArrayFunc('end');
	}

	/**
	 * Checks to see if the current item is the first in the list
	 *
	 * @param   int  $key  The key to check against
	 * @return  bool
	 * @since   2.1.0
	 **/
	public function isFirst($key)
	{
		if ($this->rows && $this->count())
		{
			return $key == array_slice($this->rows, 0, 1)[0]->getPkValue();
		}

		return false;
	}

	/**
	 * Checks to see if the current item is the last in the list
	 *
	 * @param   int  $key  The key to check against
	 * @return  bool
	 * @since   2.1.0
	 **/
	public function isLast($key)
	{
		if ($this->rows && $this->count())
		{
			return $key == array_slice($this->rows, -1, 1)[0]->getPkValue();
		}

		return false;
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
			$valid = ($key !== null && $key !== false);
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
