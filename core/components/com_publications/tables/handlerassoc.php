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
 * Table class for publication handler associations
 */
class HandlerAssoc extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_handler_assoc', 'id', $db);
	}

	/**
	 * Get associated handler(s)
	 *
	 * @param   integer  $vid        Publication Version ID
	 * @param   integer  $elementid  Element ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getAssoc($vid = null, $elementid = null)
	{
		if (!intval($vid) || !intval($elementid))
		{
			return false;
		}

		$query  = "SELECT H.*, A.params as configs, A.status, A.ordering FROM $this->_tbl as A ";
		$query .= " JOIN #__publication_handlers as H ON H.id=A.handler_id";
		$query .= " WHERE A.publication_version_id=" . $this->_db->quote($vid);
		$query .= " AND A.element_id=" . $this->_db->quote($elementid);
		$query .= " ORDER BY A.ordering ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Load associated handler
	 *
	 * @param   integer  $vid        Publication Version ID
	 * @param   integer  $elementid  Element ID
	 * @param   string   $handler    Handler name
	 * @return  mixed    False if error, Object on success
	 */
	public function getAssociation($vid = null, $elementid = null, $handler = null)
	{
		if (!intval($vid) || !intval($elementid) || !$handler)
		{
			return false;
		}

		$query  = "SELECT H.*, A.params as configs, A.status, A.ordering FROM $this->_tbl as A ";
		$query .= " JOIN #__publication_handlers as H ON H.id=A.handler_id";
		$query .= " WHERE A.publication_version_id=" . $this->_db->quote($vid);
		$query .= " AND A.element_id=" . $this->_db->quote($elementid);
		$query .= " AND H.name=" . $this->_db->quote($handler);
		$query .= " LIMIT 1";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}
}
