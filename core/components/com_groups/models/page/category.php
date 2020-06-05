<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Page;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;

// include needed tables
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'page.category.php';

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
	 * @param   mixed  $oid  Integer, array, or object
	 * @return  void
	 */
	public function __construct($oid = null)
	{
		// create database object
		$this->_db = \App::get('db');

		// create page cateogry table object
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
	 * @param   string   $rtrn   What do we want back
	 * @param   boolean  $clear  Fetch an updated list
	 * @return  object   \Hubzero\Base\ItemList
	 */
	public function getPages($rtrn = 'list', $clear = false)
	{
		// create page table
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
