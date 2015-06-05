<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Tools\Models\Middleware;

use Hubzero\Base\ItemList;

require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'zones.php');
require_once(__DIR__ . DS . 'location.php');

/**
 * Middleware zone mdel
 */
class Zone extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Tools\\Tables\\Zones';

	/**
	 * \Hubzero\ItemList
	 *
	 * @var object
	 */
	private $_cache = array(
		'locations.count' => null,
		'locations.list'  => null,
		'locations.one'   => null
	);

	/**
	 * Returns a reference to a zone model
	 *
	 * @param      mixed  $oid Zone ID or name
	 * @return     object
	 */
	static function &getInstance($oid=null)
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
	 * Get a list of locations
	 *
	 * @param      string $rtrn    Data type to return [count, list]
	 * @param      array  $filters Filters to apply to query
	 * @return     mixed Returns an integer or array depending upon format chosen
	 */
	public function locations($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new \Components\Tools\Tables\ZoneLocations($this->_db);

		if (!isset($filters['zone_id']))
		{
			$filters['zone_id'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['locations.count']) || $clear)
				{
					$this->_cache['locations.count'] = $tbl->find('count', $filters);
				}
				return $this->_cache['locations.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['locations.list'] instanceof ItemList) || $clear)
				{
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Location($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['locations.list'] = new ItemList($results);
				}
				return $this->_cache['locations.list'];
			break;
		}
	}

	/**
	 * Get the section logo
	 *
	 * @param      string $rtrn Property to return
	 * @return     string
	 */
	public function logo($rtrn='')
	{
		$rtrn = strtolower(trim($rtrn));

		// Return just the file name
		if ($rtrn == 'file')
		{
			return $this->get('picture');
		}

		// Build the path
		$path = PATH_APP . '/site/tools/zones/' . $this->get('id');

		// Return just the upload path?
		if ($rtrn == 'path')
		{
			return $path;
		}

		// Do we have a logo set?
		if ($file = $this->get('picture'))
		{
			// Return the web path to the image
			$path .= '/' . $file;
			if (file_exists($path))
			{
				$path = \Route::url('index.php?option=com_tools&app=zones&task=assets&version=' . $this->get('id') . '&file=' . $file);
			}
			return $path;
		}

		return '';
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Can't delete what doesn't exist
		if (!$this->exists())
		{
			return true;
		}

		// Remove locations
		foreach ($this->locations('list') as $location)
		{
			if (!$location->delete())
			{
				$this->setError($location->getError());
				return false;
			}
		}

		// Check for a picture
		if ($file = $this->get('picture'))
		{
			// Make sure the picture exists
			if (file_exists($this->logo('path') . DS . $file))
			{
				// Remove picture
				if (!\Filesystem::delete($this->logo('path') . DS . $file))
				{
					$this->setError(\Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
				}
			}
		}

		// Attempt to delete the record
		return parent::delete();
	}
}

