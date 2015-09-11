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

// No direct access
defined('_HZEXEC_') or die();

include_once dirname(__DIR__) . DS . 'tables' . DS . 'preferences.php';

class MembersDashboardModelPreferences extends \Hubzero\Base\Model
{
	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'MembersDashboardTablePreferences';

	/**
	 * Constructor
	 *
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct( $oid = null )
	{
		// create needed objects
		$this->_db = App::get('db');

		// load page jtable
		$this->_tbl = new $this->_tbl_name($this->_db);

		// load object
		if (is_numeric($oid))
		{
			$this->_tbl->load( $oid );
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind( $oid );
		}
	}

	/**
	 * Get Instance this Model
	 *
	 * @param   $key   Instance Key
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($key);
		}

		return $instances[$key];
	}

	/**
	 * Load Member Dashboard Prefs by uidNumber
	 *
	 * @param  int $uidNumber  Member uidNumber
	 * @return object
	 */
	public static function loadForUser($uidNumber=null)
	{
		// get db
		$db = App::get('db');

		// load prefs by userid
		$sql = "SELECT * FROM `#__xprofiles_dashboard_preferences` WHERE `uidNumber`=" . $db->quote($uidNumber) . " LIMIT 1";
		$db->setQuery($sql);
		$data = $db->loadObject();

		// create new instance of this object
		$object = new self();

		// bind data if we have any
		if (isset($data))
		{
			$object->bind($data);
		}

		// return object
		return $object;
	}
}
