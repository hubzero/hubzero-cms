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

namespace Components\Courses\Models\Section;

use Components\Courses\Models\Base;
use Components\Courses\Tables;
use \Hubzero\Utility\Date;
use User;

require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'section.code.php');
require_once(dirname(__DIR__) . DS . 'base.php');

/**
 * Courses model class for a course
 */
class Code extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\SectionCode';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'section_code';

	/**
	 * User
	 *
	 * @var object
	 */
	private $_redeemer = NULL;

	/**
	 * Constructor
	 *
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid=null, $section_id=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				$this->_tbl->load($oid, $section_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a course offering model
	 *
	 * This method must be invoked as:
	 *     $offering = \Components\Courses\Models\Offering::getInstance($alias);
	 *
	 * @param      mixed $oid ID (int) or alias (string)
	 * @return     object \Components\Courses\Models\Offering
	 */
	static function &getInstance($oid=null, $section_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = 0;

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . ($section_id ? '_' . $section_id : '');
		}
		else if (is_object($oid))
		{
			$key = $oid->get('id') . ($section_id ? '_' . $section_id : '');
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . ($section_id ? '_' . $section_id : '');
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $section_id);
		}

		return $instances[$key];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function redeemer($property=null)
	{
		if (!$this->_redeemer)
		{
			$this->_redeemer = User::getInstance($this->get('redeemed_by'));
		}
		if ($property)
		{
			return $this->_redeemer->get($property);
		}
		return $this->_redeemer;
	}

	/**
	 * Check if a code has expired
	 *
	 * @return    string
	 */
	public function isExpired()
	{
		if (!$this->exists())
		{
			return false;
		}

		if ($this->isRedeemed())
		{
			return true;
		}

		$now = Date::toSql();

		if ($this->get('expires')
		 && $this->get('expires') != $this->_db->getNullDate()
		 && $this->get('expires') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if a code has been redeemed
	 *
	 * @return    string
	 */
	public function isRedeemed()
	{
		if (!$this->exists())
		{
			return false;
		}
		if ($this->get('redeemed_by'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate a coupon code
	 *
	 * @return    string
	 */
	public function redeem($redeemed_by=0, $code=null)
	{
		if (!$code)
		{
			$code = $this->get('code');
		}
		if (!$redeemed_by)
		{
			$redeemed_by = User::get('id');
		}
		$this->set('redeemed_by', $redeemed_by);
		$this->set('redeemed', Date::toSql());
		return $this->store();
	}
}

