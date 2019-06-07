<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Iterator;

/**
 * Associative list
 */
class AssocList extends Assoc implements Iterator
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
	 * Short description for 'is_scalar'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function is_scalar()
	{
		return false;
	}

	/**
	 * Short description for 'set_plugin'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $plugin Parameter description (if any) ...
	 * @param      boolean $skip_cleanup Parameter description (if any) ...
	 * @return     void
	 */
	public function set_plugin($plugin, $skip_cleanup = false)
	{
		foreach ($this->rows as $row)
		{
			$row->set_plugin($plugin, $skip_cleanup);
		}
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $rows Parameter description (if any) ...
	 * @param      unknown $plugin Parameter description (if any) ...
	 * @return     void
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
	 * Short description for 'at'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $idx Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &at($idx)
	{
		return $this->rows[$idx];
	}

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function to_associative()
	{
		return $this;
	}

	/**
	 * Short description for 'get_items'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function get_items()
	{
		return $this->rows;
	}

	/**
	 * Short description for 'rewind'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function rewind()
	{
		$this->pos = 0;
	}

	/**
	 * Short description for 'current'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function current()
	{
		return $this->rows[$this->pos];
	}

	/**
	 * Short description for 'key'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function key()
	{
		return $this->pos;
	}

	/**
	 * Short description for 'next'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function next()
	{
		++$this->pos;
	}

	/**
	 * Short description for 'valid'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function valid()
	{
		return isset($this->rows[$this->pos]);
	}
}
