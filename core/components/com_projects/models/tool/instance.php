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

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;

/**
 * Project Tool Instance model
 */
class Instance extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\ToolInstance';

	/**
	 * Registry
	 *
	 * @var object
	 */
	public $config = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct($oid, $parent = NULL)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\ToolInstance($this->_db);

		if ($oid && $oid != 'dev')
		{
			if (is_numeric($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_string($oid))
			{
				$this->_tbl->loadFromInstanceName($oid);
			}
			else if (is_object($oid))
			{
				$this->bind($oid);
			}
		}
		elseif ($parent)
		{
			// Load dev instance
			$this->_tbl->loadFromParent($parent, 'dev');
		}

		$this->params = new \Hubzero\Config\Registry($this->_tbl->get('params'));
	}

	/**
	 * Returns a reference to the model
	 *
	 * @param      mixed $oid object ID
	 * @return     object Todo
	 */
	static function &getInstance($oid=null, $parent=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $parent);
		}

		return $instances[$key];
	}

	/**
	 * Update Parent Name
	 *
	 * @param      integer $id
	 * @param      string $name
	 * @return     boolean
	 */
	public function updateParentName($id=null, $name=null)
	{
		if (!$id || !$name)
		{
			return false;
		}
		return $this->_tbl->updateParentName($id, $name);
	}
}
