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
 * Table class for available publication handlers
 */
class Handler extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_handlers', 'id', $db);
	}

	/**
	 * Load handler
	 *
	 * @param   string  $name  Alias name of handler
	 * @return  mixed   False if error, Object on success
	 */
	public function loadRecord($name = null)
	{
		if ($name === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE name=" . $this->_db->quote($name);
		$query.= " LIMIT 1";
		$this->_db->setQuery($query);

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get connections
	 *
	 * @param   integer  $vid  pub version id
	 * @param   array    $find
	 * @return  object
	 */
	public function getHandlers($vid = null, $elementid = 0)
	{
		if (!$vid)
		{
			$vid = $this->publication_version_id;
		}

		$query  = "SELECT H.*, IFnull(A.id, 0) as assigned, IFnull(A.ordering, 0) as ordering,
				A.params as assigned_params, A.status as active  FROM $this->_tbl as H ";
		$query .= "LEFT JOIN #__publication_handler_assoc as A ON H.id=A.handler_id ";
		$query .= " AND A.publication_version_id=" . $this->_db->quote($vid)
				. " AND A.element_id=" . $this->_db->quote($elementid);
		$query .= " WHERE H.status = 1";
		$query .= " ORDER BY assigned DESC, A.ordering ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Load handler config
	 *
	 * @param   string  $name  Alias name of handler
	 * @param   object  $entry
	 * @return  mixed   False if error, Object on success
	 */
	public function getConfig($name = null, $entry = null)
	{
		if ($name == null)
		{
			return false;
		}

		if (!$entry || !is_object($entry))
		{
			$query = "SELECT * FROM $this->_tbl WHERE name=" . $this->_db->quote($name);
			$query.= " LIMIT 1";

			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			$entry = $result ? $result[0] : null;
		}

		// Parse configs
		if ($entry)
		{
			$output = array();
			$output['params'] = array();
			foreach ($entry as $field => $value)
			{
				if ($field == 'params')
				{
					$params = json_decode($value, true);
					if (is_array ($params))
					{
						foreach ($params as $paramName => $paramValue)
						{
							$output['params'][$paramName] = $paramValue;
						}
					}
				}
				else
				{
					$output[$field] = $value;
				}
			}

			return $output;
		}

		return false;
	}
}
