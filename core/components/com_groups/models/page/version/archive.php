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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Page\Version;

use Components\Groups\Models\Page;
use Components\Groups\Models\Page\Version;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;
use Request;

// include needed modelss
require_once dirname(__DIR__) . DS . 'version.php';

/**
 * Group page version archive model class
 */
class Archive extends Model
{
	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_versions = null;

	/**
	 * Version Count
	 *
	 * @var int
	 */
	private $_versions_count = null;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct()
	{
		// create database object
		$this->_db = \App::get('db');
	}

	/**
	 * Get a list of group page versions
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function versions($rtrn = 'list', $filters = array(), $clear = false)
	{
		$tbl = new Tables\PageVersion($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_versions_count))
				{
					$this->_versions_count = $tbl->count($filters);
				}
				return (int) $this->_versions_count;
			break;
			case 'list':
			default:
				if (!($this->_versions instanceof ItemList) || $clear)
				{
					// make sure we have page id
					if (!isset($filters['pageid']))
					{
						$filters['pageid'] = 0;
					}

					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Version($result);
						}
					}
					$this->_versions = new ItemList($results);
				}
				return $this->_versions;
			break;
		}
	}
}
