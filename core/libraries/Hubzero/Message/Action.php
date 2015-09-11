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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

/**
 * Table class for message actions
 */
class Action extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage_action', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->element = intval($this->element);
		if (!$this->element)
		{
			$this->setError(\Lang::txt('Please provide an element.'));
			return false;
		}
		return true;
	}

	/**
	 * Get records for specific type, element, component, and user
	 *
	 * @param   string   $type       Action type
	 * @param   string   $component  Component name
	 * @param   integer  $element    ID of element that needs action
	 * @param   integer  $uid        User ID
	 * @return  mixed    False if errors, array on success
	 */
	public function getActionItems($type=null, $component=null, $element=null, $uid=null)
	{
		$component = $component ?: $this->class;
		$element   = $element   ?: $this->element;

		if (!$component || !$element || !$uid || !$type)
		{
			$this->setError(\Lang::txt('Missing argument.'));
			return false;
		}

		$query = "SELECT m.id
				FROM `#__xmessage_recipient` AS r, $this->_tbl AS a, `#__xmessage` AS m
				WHERE m.id=r.mid AND r.actionid = a.id AND m.type=" . $this->_db->quote($type) . " AND r.uid=" . $this->_db->quote($uid) . " AND a.class=" . $this->_db->quote($component) . " AND a.element=" . $this->_db->quote($element);

		$this->_db->setQuery($query);
		return $this->_db->loadColumn();
	}
}

