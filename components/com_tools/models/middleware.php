<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'utils.php');
require_once(__DIR__ . '/middleware/zone.php');
require_once(__DIR__ . '/middleware/session.php');

/**
 * Tools middleware model
 */
class ToolsModelMiddleware extends \Hubzero\Base\Object
{
	/**
	 * \Hubzero\ItemList
	 *
	 * @var object
	 */
	private $_cache = array(
		'zones.count' => null,
		'zones.list'  => null,
		'zones.one'   => null,
		'session'     => null,
		'tool'        => null
	);

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_config = null;

	/**
	 * Constructor
	 *
	 * @param      mixed $oid Integer (ID), string (alias), object or array
	 * @return     void
	 */
	public function __construct($db=null)
	{
		if ($db instanceof JDatabase)
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = ToolsHelperUtils::getMWDBO();
		}
	}

	/**
	 * Get a session model
	 *
	 * @param   integer $sessnum
	 * @return  object
	 */
	public function session($sessnum=null, $permissions=null)
	{
		if (!isset($this->_cache['session'])
		 || ($sessnum !== null && (int) $this->_cache['session']->get('sessnum') != $sessnum))
		{
			$this->_cache['session'] = MiddlewareModelSession::getInstance($sessnum, $permissions);
		}

		return $this->_cache['session'];
	}

	/**
	 * Set and get a specific zone
	 *
	 * @return     void
	 */
	public function zone($id=null)
	{
		// If the current offering isn't set
		//    OR the ID passed doesn't equal the current offering's ID or alias
		if (!isset($this->_cache['zones.one'])
		 || ($id !== null && (int) $this->_cache['zones.one']->get('id') != $id))
		{
			// Reset current offering
			$this->_cache['zones.one'] = null;

			// If the list of all offerings is available ...
			if (isset($this->_cache['zones.list']))
			{
				// Find an offering in the list that matches the ID passed
				foreach ($this->_cache['zones.list'] as $key => $zone)
				{
					if ((int) $zone->get('id') == $id)
					{
						// Set current offering
						$this->_cache['zones.one'] = $zone;
						break;
					}
				}
			}

			if (is_null($this->_cache['zones.one']))
			{
				// Get current offering
				$this->_cache['zones.one'] = new MiddlewareModelZone($id);
			}
		}
		// Return current offering
		return $this->_cache['zones.one'];
	}

	/**
	 * Get a zone based on location
	 *
	 * Second param is a list of zone IDs to check against. That list
	 * is pulled from the #__tool_version_zone table.
	 *
	 * @param      string $ip
	 * @param      array  $allowed List of zone IDs to check against
	 * @return     object
	 */
	public function zoning($ip=null, $allowed=array())
	{
		if (!$ip)
		{
			return new MiddlewareModelZone();
		}

		// Find by IP
		$zones = $this->zones('list', array('state' => 'up', 'id' => $allowed, 'ip' => $ip), true);
		if ($zones->total() > 0)
		{
			foreach ($zones as $zone)
			{
				return $zone;
			}
		}

		// Find by city

		// Find by region

		// Find by country
		$country = \Hubzero\Geocode\Geocode::ipcountry($ip);
		if (!$country)
		{
			return new MiddlewareModelZone();
		}

		$zones = $this->zones('list', array('state' => 'up', 'id' => $allowed, 'countrySHORT' => $country), true);
		if ($zones->total() > 0)
		{
			foreach ($zones as $zone)
			{
				return $zone;
			}
		}

		// Find by continent
		$continent = \Hubzero\Geocode\Geocode::getContinentByCountry($country);
		if (!$continent)
		{
			return new MiddlewareModelZone();
		}

		$zones = $this->zones('list', array('state' => 'up', 'id' => $allowed, 'continent' => $continent), true);
		if ($zones->total() > 0)
		{
			foreach ($zones as $zone)
			{
				return $zone;
			}
		}

		return new MiddlewareModelZone();
	}

	/**
	 * Get a list of zones
	 *
	 * @param      string $rtrn    Data type to return [count, list]
	 * @param      array  $filters Filters to apply to query
	 * @return     mixed Returns an integer or array depending upon format chosen
	 */
	public function zones($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new MwZones($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['zones.count']) || $clear)
				{
					$this->_cache['zones.count'] = $tbl->find('count', $filters);
				}
				return $this->_cache['zones.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['zones.list'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new MiddlewareModelZone($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['zones.list'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['zones.list'];
			break;
		}
	}

	/**
	 * Get a config value
	 *
	 * @param      string $key     Property to return
	 * @param      mixed  $default Default value
	 * @return     mixed
	 */
	public function config($key='', $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = JComponentHelper::getParams('com_tools');
		}

		if ($key)
		{
			return $this->_config->get((string) $key, $default);
		}
		return $this->_config;
	}
}

