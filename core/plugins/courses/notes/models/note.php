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

namespace Plugins\Courses\Notes\Models;

use Components\Courses\Models\Base;
use Components\Courses\Models\Iterator;
use User;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'note.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'base.php');

/**
 * Courses model class for a course note
 */
class Note extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Plugins\\Courses\\Notes\\Tables\\Note';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'note';

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	protected $_notes = null;

	/**
	 * Serialized string of filers
	 *
	 * @var string
	 */
	protected $_filters = null;

	/**
	 * Returns a reference to a course note model
	 *
	 * @param   integer  $oid  ID (int)
	 * @return  object
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get a list or count of notes
	 *
	 * @param   array   $filters  Filters to apply
	 * @return  object
	 */
	public function notes($filters=array())
	{
		if (!isset($filters['created_by']))
		{
			$filters['created_by'] = (int) User::get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}

		if (isset($filters['count']) && $filters['count'])
		{
			return $this->_tbl->count($filters);
		}

		if (!isset($this->_notes) || !($this->_notes instanceof Iterator) || (!empty($filters) && serialize($filters) != $this->_filters))
		{
			$this->_filters = serialize($filters);

			if ($results = $this->_tbl->find($filters))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new self($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_notes = new Iterator($results);
		}

		return $this->_notes;
	}
}

