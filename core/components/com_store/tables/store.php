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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Store\Tables;

/**
 * Table class for store items
 */
class Store extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__store', 'id', $db);
	}

	/**
	 * Get a record
	 *
	 * @param   integer  $id
	 * @return  object
	 */
	public function getInfo($id)
	{
		if ($id == null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records
	 *
	 * @param   string  $rtrn     Return data (record count or array or records)
	 * @param   array   $filters  Filters to build query from
	 * @param   object  $config   Registry
	 * @return  mixed
	 */
	public function getItems($rtrn='count', $filters, $config)
	{
		// build body of query
		$query  = "FROM $this->_tbl AS C WHERE ";

		if (isset($filters['filterby']))
		{
			switch ($filters['filterby'])
			{
				case 'all':
					$query .= "1=1";
				break;
				case 'available':
					$query .= "C.available=1";
				break;
				case 'published':
					$query .= "C.published=1";
				break;
				default:
					$query .= "C.published=1";
				break;
			}
		}
		else
		{
			$query .= "C.published=1";
		}

		switch ($filters['sortby'])
		{
			case 'pricelow':  $query .= " ORDER BY C.price DESC"; break;
			case 'pricehigh': $query .= " ORDER BY C.price ASC"; break;
			case 'date':      $query .= " ORDER BY C.created DESC"; break;
			case 'category':  $query .= " ORDER BY C.category DESC"; break;
			case 'type':      $query .= " ORDER BY C.type DESC"; break;
			default:          $query .= " ORDER BY C.featured DESC, C.id DESC"; break; // featured and newest first
		}

		// build count query (select only ID)
		$query_count  = "SELECT count(*) ";

		// build fetch query
		$query_fetch  = "SELECT C.id, C.title, C.description, C.price, C.created, C.available,
						C.params, C.special, C.featured, C.category, C.type, C.published ";
		$query_fetch .= $query;
		if ($filters['limit'] && $filters['start'])
		{
			$query_fetch .= " LIMIT " . $start . ", " . $limit;
		}

		// execute query
		$result = NULL;
		if ($rtrn == 'count')
		{
			$this->_db->setQuery($query_count);
			$result = $this->_db->loadResult();
		}
		else
		{
			$this->_db->setQuery($query_fetch);
			$result = $this->_db->loadObjectList();
			if ($result)
			{
				for ($i=0; $i < count($result); $i++)
				{
					$row = &$result[$i];

					$row->webpath = substr(PATH_APP, strlen(PATH_ROOT)) . $config->get('webpath', '/site/store');
					$row->root = PATH_ROOT;

					// Get parameters
					$params = new \Hubzero\Config\Registry($row->params);
					$row->size  = $params->get('size', '');
					$row->color = $params->get('color', '');
				}
			}
		}

		return $result;
	}
}

