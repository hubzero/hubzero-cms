<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Components\Tools\Helpers\Iprange;
use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Middleware zones table class
 */
class Zones extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('zones', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  False if invalid data, true on success
     */
    public function check()
    {
        $this->zone = preg_replace("/[^A-Za-z0-9\-\_\.]/", '', $this->zone);
        if (!$this->zone) {
            $this->setError(Lang::txt('No zone provided'));
            return false;
        }
        if (!$this->title) {
            $this->title = $this->zone;
        }
        $this->master = trim($this->master);
        if (!$this->master) {
            $this->setError(Lang::txt('No master provided'));
            return false;
        }
        $this->state = strtolower(trim($this->state));
        if (!$this->state) {
            $this->setError(Lang::txt('No state provided.'));
            return false;
        }
        if (!in_array($this->state, ['up', 'down'])) {
            $this->setError(Lang::txt('Invalid state provided.'));
            return false;
        }

        return true;
    }

    /**
     * Delete a record and any associated records in the #__zone_locations table
     *
     * @param   integer  $oid  Record ID
     * @return  boolean  True if successful otherwise returns and error message
     */
    public function delete($oid = null)
    {
        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = $oid;
        }

        $location = new ZoneLocations($this->_db);
        if (!$location->deleteByZone($oid)) {
            $this->setError($location->getError());
            return false;
        }

        return parent::delete($oid);
    }

    /**
     * Construct an SQL statement based on the array of filters passed
     *
     * @param   array   $filters  Filters to build SQL from
     * @return  string  SQL
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    private function _buildQuery($filters = [])
    {
        $where = [];

        if (isset($filters['state']) && $filters['state'] != '') {
            $where[] = "c.`state`=" . $this->_db->quote($filters['state']);
        }
        if (isset($filters['master']) && $filters['master'] != '') {
            $where[] = "c.`master`=" . $this->_db->quote($filters['master']);
        }
        if (isset($filters['zone']) && $filters['zone'] != '') {
            $where[] = "c.`zone`=" . $this->_db->quote($filters['zone']);
        }
        if (isset($filters['id'])) {
            if (!is_array($filters['id'])) {
                $filters['id'] = [$filters['id']];
            }
            $filters['id'] = array_map('intval', $filters['id']);
            if (empty($filters['id'])) {
                $filters['id'][] = 0;
            }
            //if (!empty($filters['id']))
            //{
            $where[] = "c.`id` IN (" . implode(',', $filters['id']) . ")";
            //}
        }

        if (isset($filters['search']) && $filters['search'] != '') {
            $where[] = "(LOWER(c.`zone`) LIKE "
                . $this->_db->quote('%' . strtolower($filters['search']) . '%')
                . " OR LOWER(c.`master`) LIKE "
                . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
        }

        $query = "FROM $this->_tbl AS c";
        if (
            isset($filters['ip']) || isset($filters['ipFROM']) || isset($filters['ipTO'])
            || isset($filters['continent']) || isset($filters['countrySHORT'])
            || isset($filters['ipREGION']) || isset($filters['ipCITY'])
        ) {
            $query .= " JOIN `zone_locations` AS t ON c.`id`=t.`zone_id`";
            //$where[] = "t.`id` = " . $this->_db->quote($this->view->filters['location']);
            // Addresses compare in the form zone_locations stores them; one
            // that is not an address matches nothing. A location with no
            // range ('') never matches an address -- and while the columns
            // are still the old integers, before Migration20261104170100ComTools,
            // this keeps their 0 (no range) from matching every address, since
            // MySQL reads the hex as the number 0.
            $ranged = false;
            foreach (['ipFROM' => ['ipFROM' => '='], 'ipTO' => ['ipTO' => '='], 'ip' => ['ipFROM' => '<=', 'ipTO' => '>=']] as $filter => $compare) {
                if (!isset($filters[$filter]) || $filters[$filter] == '') {
                    continue;
                }

                if (!$ranged) {
                    $where[] = "t.`ipTO` <> ''";
                    $ranged  = true;
                }

                $ip = Iprange::toColumn($filters[$filter]);

                if ($ip === false || $ip === '') {
                    $where[] = "0=1";
                    continue;
                }

                foreach ($compare as $column => $op) {
                    $where[] = "t.`" . $column . "` " . $op . " " . $this->_db->quote($ip);
                }
            }
            if (isset($filters['continent']) && $filters['continent']) {
                if (!is_array($filters['continent'])) {
                    $filters['continent'] = [$filters['continent']];
                }
                foreach ($filters['continent'] as $k => $v) {
                    $filters['continent'][$k] = $this->_db->quote(strtolower($v));
                }
                $where[] = "LOWER(t.`continent`) IN (" . implode(',', $filters['continent']) . ")";
            }
            if (isset($filters['countrySHORT']) && $filters['countrySHORT'] != '') {
                $where[] = "LOWER(t.`countrySHORT`)=" . $this->_db->quote(strtolower($filters['countrySHORT']));
            }
            if (isset($filters['ipREGION']) && $filters['ipREGION'] != '') {
                $where[] = "LOWER(t.`ipREGION`)=" . $this->_db->quote(strtolower($filters['ipREGION']));
            }
            if (isset($filters['ipCITY']) && $filters['ipCITY'] != '') {
                $where[] = "LOWER(t.`ipCITY`)=" . $this->_db->quote(strtolower($filters['ipCITY']));
            }
        }
        if (count($where) > 0) {
            $query .= " WHERE ";
            $query .= implode(" AND ", $where);
        }
        $query .= " GROUP BY `zone`, c.id ";

        return $query;
    }

    /**
     * Get a list of records
     *
     * @param   string  $what     Data to return
     * @param   array   $filters  Filters to build SQL from
     * @return  mixed
     */
    public function find($what = 'list', $filters = [])
    {
        switch ($what) {
            case 'count':
                $filters['limit'] = 0;

                // _buildQuery() groups by zone, so count the grouped rows
                $query = "SELECT COUNT(*) FROM (SELECT c.id " . $this->_buildQuery($filters) . ") AS z";

                $this->_db->setQuery($query);
                return $this->_db->loadResult();
                break;

            case 'all':
                $filters['limit'] = 0;
                return $this->find('list', $filters);
                break;

            case 'list':
            default:
                $query  = "SELECT c.* " . $this->_buildQuery($filters);

                if (!isset($filters['sort']) || !$filters['sort']) {
                    $filters['sort'] = 'zone';
                }
                if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) {
                    $filters['sort_Dir'] = 'ASC';
                }
                $query .= " ORDER BY " . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['sort']) ?: '1') . " " . (strtoupper((string) $filters['sort_Dir']) === 'ASC' ? 'ASC' : 'DESC');

                if (isset($filters['limit']) && $filters['limit'] != 0) {
                    $query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
                }

                $this->_db->setQuery($query);
                return $this->_db->loadObjectList();
                break;
        }
    }
}
