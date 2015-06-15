<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$this->_db = \JFactory::getDBO();

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