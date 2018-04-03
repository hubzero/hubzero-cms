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

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project types
 */
class Type extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_types', 'id', $db);
	}

	/**
	 * Get params
	 *
	 * @param   integer  $type
	 * @return  string   or null
	 */
	public function getParams($type = 1)
	{
		$this->_db->setQuery("SELECT params FROM $this->_tbl WHERE id=$type");
		return $this->_db->loadResult();
	}

	/**
	 * Get types
	 *
	 * @return  object  or null
	 */
	public function getTypes()
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl");
		return $this->_db->loadObjectList();
	}

	/**
	 * Get type title
	 *
	 * @param   integer  $id
	 * @return  string   or null
	 */
	public function getTypeTitle($id = 0)
	{
		$this->_db->setQuery("SELECT type FROM $this->_tbl WHERE id=" . $this->_db->quote($id));
		return $this->_db->loadResult();
	}

	/**
	 * Get ID by type title
	 *
	 * @param   string  $type
	 * @return  string  or null
	 */
	public function getIdByTitle($type = '')
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE type=" . $this->_db->quote($type));
		return $this->_db->loadResult();
	}
}
