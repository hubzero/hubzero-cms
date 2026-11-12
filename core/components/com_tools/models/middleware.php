<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models;

use Components\Tools\Models\Middleware\Session;
use Components\Tools\Models\Middleware\Zone;
use Components\Tools\Helpers\Utils;
use Hubzero\Geocode\Geocode;
use Hubzero\Base\ItemList;
use Hubzero\Base\Obj;
use Hubzero\Facades\Component;

/**
 * Tools middleware model
 */
class Middleware extends Obj
{
    protected $db;

    /**
     * \Hubzero\ItemList
     *
     * @var object
     */
    private $cache = [
        'zones.count' => null,
        'zones.list'  => null,
        'zones.one'   => null,
        'session'     => null,
        'tool'        => null,
    ];

    /**
     * Registry
     *
     * @var object|null
     */
    private $config = null;

    /**
     * Constructor
     *
     * @param      mixed $db Integer (ID), string (alias), object or array
     * @return     void
     */
    public function __construct($db = null)
    {
        if (!($db instanceof \Hubzero\Database\Driver)) {
            $db = Utils::getMWDBO();
        }

        $this->db = $db;
    }

    /**
     * Get a session model
     *
     * @param   integer $sessnum
     * @return  object
     */
    public function session($sessnum = null, $permissions = null)
    {
        if (
            !isset($this->cache['session'])
            || ($sessnum !== null && (int) $this->cache['session']->get('sessnum') != $sessnum)
        ) {
            $this->cache['session'] = Session::getInstance($sessnum, $permissions);
        }

        return $this->cache['session'];
    }

    /**
     * Set and get a specific zone
     *
     * @return     mixed
     */
    public function zone($id = null)
    {
        // If the current offering isn't set
        //    OR the ID passed doesn't equal the current offering's ID or alias
        if (
            !isset($this->cache['zones.one'])
            || ($id !== null && (int) $this->cache['zones.one']->get('id') != $id)
        ) {
            // Reset current offering
            $this->cache['zones.one'] = null;

            // If the list of all offerings is available ...
            if (isset($this->cache['zones.list'])) {
                // Find an offering in the list that matches the ID passed
                foreach ($this->cache['zones.list'] as $key => $zone) {
                    if ((int) $zone->get('id') == $id) {
                        // Set current offering
                        $this->cache['zones.one'] = $zone;
                        break;
                    }
                }
            }

            if (is_null($this->cache['zones.one'])) {
                // Get current offering
                $this->cache['zones.one'] = new Zone($id);
            }
        }
        // Return current offering
        return $this->cache['zones.one'];
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
    public function zoning($ip = null, $allowed = [])
    {
        if (!$ip) {
            return new Zone();
        }

        // Find by IP
        $zones = $this->zones('list', ['state' => 'up', 'id' => $allowed, 'ip' => $ip], true);
        if ($zones->total() > 0) {
            foreach ($zones as $zone) {
                return $zone;
            }
        }

        // Find by city

        // Find by region

        // Find by country
        $country = Geocode::ipcountry($ip);
        if (!$country) {
            return new Zone();
        }

        $zones = $this->zones('list', ['state' => 'up', 'id' => $allowed, 'countrySHORT' => $country], true);
        if ($zones->total() > 0) {
            foreach ($zones as $zone) {
                return $zone;
            }
        }

        // Find by continent
        $continent = Geocode::getContinentByCountry($country);
        if (!$continent) {
            return new Zone();
        }

        $zones = $this->zones('list', ['state' => 'up', 'id' => $allowed, 'continent' => $continent], true);
        if ($zones->total() > 0) {
            foreach ($zones as $zone) {
                return $zone;
            }
        }

        return new Zone();
    }

    /**
     * Get a list of zones
     *
     * @param      string $rtrn    Data type to return [count, list]
     * @param      array  $filters Filters to apply to query
     * @return     mixed Returns an integer or array depending upon format chosen
     */
    public function zones($rtrn = 'list', $filters = [], $clear = false)
    {
        $tbl = new \Components\Tools\Tables\Zones($this->db);

        switch (strtolower($rtrn)) {
            case 'count':
                if (!isset($this->cache['zones.count']) || $clear) {
                    $this->cache['zones.count'] = $tbl->find('count', $filters);
                }
                return $this->cache['zones.count'];

            case 'list':
            case 'results':
            default:
                if (!($this->cache['zones.list'] instanceof ItemList) || $clear) {
                    if ($results = $tbl->find('list', $filters)) {
                        foreach ($results as $key => $result) {
                            $results[$key] = new Zone($result);
                        }
                    } else {
                        $results = [];
                    }
                    $this->cache['zones.list'] = new ItemList($results);
                }
                return $this->cache['zones.list'];
        }
    }

    /**
     * Get a config value
     *
     * @param      string $key     Property to return
     * @param      mixed  $default Default value
     * @return     mixed
     */
    public function config($key = '', $default = null)
    {
        if (!isset($this->config)) {
            $this->config = Component::params('com_tools');
        }

        if ($key) {
            return $this->config->get((string) $key, $default);
        }
        return $this->config;
    }
}
