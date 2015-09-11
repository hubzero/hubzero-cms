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

namespace Components\Groups\Models\Page;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;

// include needed jtables
require_once PATH_CORE . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'page.category.php';

/**
 * Group page category model class
 */
class Category extends Model
{
	/**
	 * Table object
	 *
	 * @var object
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Groups\\Tables\\PageCategory';

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_pages = null;

	/**
	 * Page count
	 *
	 * @var integer
	 */
	private $_pages_count = null;

	/**
	 * Constructor
	 *
	 * @param      mixed $oid Integer, array, or object
	 * @return     void
	 */
	public function __construct($oid = null)
	{
		// create database object
		$this->_db = \App::get('db');

		// create page cateogry jtable object
		$this->_tbl = new $this->_tbl_name($this->_db);

		// load object
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Get pages in this category
	 *
	 * @param     string  $rtrn    What do we want back
	 * @param     boolean $clear   Fetch an updated list
	 * @return    object  \Hubzero\Base\ItemList
	 */
	public function getPages($rtrn = 'list', $clear = false)
	{
		// create page jtable
		$tbl = new Tables\Page($this->_db);

		// build array of filters
		$filters = array(
			'gidNumber' => $this->get('gidNumber'),
			'category'  => $this->get('id'),
			'state'     => array(0, 1)
		);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_pages_count))
				{
					$this->_pages_count = $tbl->count($filters);
				}
				return (int) $this->_pages_count;
			break;
			case 'list':
			default:
				if (!($this->_pages instanceof ItemList) || $clear)
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Page($result);
						}
					}
					$this->_pages = new ItemList($results);
				}
				return $this->_pages;
			break;
		}
	}
}