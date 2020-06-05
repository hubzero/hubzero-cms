<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Page\Category;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;

// include needed modelss
require_once dirname(__DIR__) . DS . 'category.php';

/**
 * Group page category archive model class
 */
class Archive extends Model
{
	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_categories = null;

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
	 * Get a list of categories
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function categories($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':

			break;
			case 'list':
			default:
				if (!($this->_categories instanceof ItemList) || $clear)
				{
					$tbl = new Tables\PageCategory($this->_db);
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Page\Category($result);
						}
					}
					$this->_categories = new ItemList($results);
				}
				return $this->_categories;
			break;
		}
	}
}
