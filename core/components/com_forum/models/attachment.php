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

namespace Components\Forum\Models;

use LogicException;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'attachment.php');
require_once(__DIR__ . DS . 'base.php');

/**
 * Model class for a forum post attachment
 */
class Attachment extends Base
{
	/**
	 * Table class name
	 *
	 * @var  object
	 */
	protected $_tbl_name = '\\Components\\Forum\\Tables\\Attachment';

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid  ID (integer), alias (string), array or object
	 * @param   integer  $pid  Post ID
	 * @return  void
	 */
	public function __construct($oid=null, $pid=null)
	{
		$this->_db = \App::get('db');

		$cls = $this->_tbl_name;
		$this->_tbl = new $cls($this->_db);

		if (!($this->_tbl instanceof \JTable))
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
			);
			throw new LogicException(Lang::txt('Table class must be an instance of JTable.'));
		}

		if ($oid)
		{
			if (is_numeric($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_string($oid))
			{
				$this->_tbl->loadByAlias($oid, $section_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
		else if ($pid)
		{
			$this->_tbl->loadByPost($pid);
		}
	}

	/**
	 * Returns a reference to a forum post attachment model
	 *
	 * @param   mixed    $oid  ID (int), alias (string), array, or object
	 * @param   integer  $pid  Post ID
	 * @return  object
	 */
	static function &getInstance($oid=0, $pid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $pid . '_' . $oid;
		}
		else if (is_object($oid))
		{
			$key = $pid . '_' . $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $pid . '_' . $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $pid);
		}

		return $instances[$key];
	}
}

