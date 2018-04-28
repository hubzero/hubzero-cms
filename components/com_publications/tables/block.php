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

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication building blocks
 */
class Block extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_blocks', 'id', $db);
	}

	/**
	 * Get record by name
	 *
	 * @param   string  $name
	 * @return  mixed   object or false
	 */
	public function getBlock($name = '')
	{
		if (!$name)
		{
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE block=" . $this->_db->quote($name)  . " LIMIT 1" );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Get record id by name
	 *
	 * @param   string   $name
	 * @return  integer
	 */
	public function getBlockId($name='')
	{
		if (!$name)
		{
			return false;
		}
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE block=" . $this->_db->quote($name)  . " LIMIT 1" );
		return $this->_db->loadResult();
	}

	/**
	 * Get available blocks
	 *
	 * @param   string  $select  Select query
	 * @param   string  $where
	 * @param   string  $order
	 * @return  array
	 */
	public function getBlocks($select = '*', $where = '', $order = '')
	{
		$query  = "SELECT $select FROM $this->_tbl " . $where;
		$query .= $order ? $order : " ORDER BY id ";

		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		if ($select == 'block')
		{
			$blocks = array();
			if ($results)
			{
				foreach ($results as $result)
				{
					$blocks[] = $result->block;
				}
			}
			return $blocks;
		}
		return $results;
	}

	/**
	 * Load default block manifest
	 *
	 * @param   string  $name  Block name
	 * @return  mixed   False if error, Object on success
	 */
	public function getManifest($name = null)
	{
		if ($name === null)
		{
			return false;
		}

		$query = "SELECT manifest FROM $this->_tbl WHERE block=" . $this->_db->quote($name);
		$query.= " LIMIT 1";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();

		return $result ? json_decode($result) : false;
	}
}
