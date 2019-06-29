<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Countable;
use Iterator;

/**
 * Associative list
 */
class AssocList extends Assoc implements Iterator, Countable
{
	/**
	 * Description for 'rows'
	 *
	 * @var array
	 */
	private $rows = array();

	/**
	 * Description for 'pos'
	 *
	 * @var integer
	 */
	private $pos = 0;

	/**
	 * Is this scalar?
	 *
	 * @return  boolean
	 */
	public function is_scalar()
	{
		return false;
	}

	/**
	 * Set the plugin on the list of items
	 *
	 * @param   string   $plugin
	 * @param   boolean  $skip_cleanup
	 * @return  void
	 */
	public function set_plugin($plugin, $skip_cleanup = false)
	{
		foreach ($this->rows as $row)
		{
			$row->set_plugin($plugin, $skip_cleanup);
		}
	}

	/**
	 * Constructor
	 *
	 * @param   array   $rows
	 * @param   string  $plugin
	 * @return  void
	 */
	public function __construct($rows, $plugin = null)
	{
		$this->rows = is_array($rows) ? $rows : array($rows);
		$scale = 1;
		foreach ($this->rows as $idx => &$row)
		{
			if (!($row instanceof SearchResult))
			{
				$row = new AssocScalar($row);
				$row->set_plugin($plugin);
			}

			if ($idx == 0 && ($weight = $row->get_weight()) > 1)
			{
				$scale = $weight;
			}

			if ($scale > 1)
			{
				$row->scale_weight($scale, 'normalizing within plugin');
			}
		}
	}

	/**
	 * Get an item at the selected position
	 *
	 * @param   integer  $idx
	 * @return  mixed
	 */
	public function &at($idx)
	{
		return $this->rows[$idx];
	}

	/**
	 * Get a list of items as an AssocList object
	 *
	 * @return  object
	 */
	public function to_associative()
	{
		return $this;
	}

	/**
	 * Get items
	 *
	 * @return  array
	 */
	public function get_items()
	{
		return $this->rows;
	}

	/**
	 * Reset position
	 *
	 * @return  void
	 */
	public function rewind()
	{
		$this->pos = 0;
	}

	/**
	 * Get item for current position
	 *
	 * @return  mixed
	 */
	public function current()
	{
		return $this->rows[$this->pos];
	}

	/**
	 * Get current position key
	 *
	 * @return  integer
	 */
	public function key()
	{
		return $this->pos;
	}

	/**
	 * Get next position
	 *
	 * @return  void
	 */
	public function next()
	{
		++$this->pos;
	}

	/**
	 * Is current position valid?
	 *
	 * @return  boolean
	 */
	public function valid()
	{
		return isset($this->rows[$this->pos]);
	}

	/**
	 * Get a record count
	 *
	 * @return  integer
	 */
	public function count()
	{
		return count($this->rows);
	}
}
