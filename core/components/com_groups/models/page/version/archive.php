<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
