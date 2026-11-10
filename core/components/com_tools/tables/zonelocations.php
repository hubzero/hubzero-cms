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
 * Middleware zone locations table class
 */
class ZoneLocations extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('zone_locations', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  False if invalid data, true on success
     */
    public function check()
    {
        $this->zone_id = intval($this->zone_id);
        if (!$this->zone_id) {
            $this->setError(Lang::txt('No zone ID provided'));
            return false;
        }

        // Addresses as entered, or as loaded when a stored row is saved again
        $range = Iprange::fromInput($this->ipFROM, $this->ipTO);
        if ($range === false) {
            $this->setError(Lang::txt('Invalid IP address or range'));
            return false;
        }
        list($this->ipFROM, $this->ipTO) = $range;

        return true;
    }

    /**
     * Delete one or more records by zone ID
     *
     * @param   integer  $zone_id  Zone ID
     * @return  boolean  True if successful otherwise returns and error message
     */
    public function deleteByZone($zone_id = null)
    {
        $zone_id = intval($zone_id);
        if (!$zone_id) {
            $zone_id = $this->zone_id;
        }

        $query = 'DELETE FROM ' . $this->db->quoteName($this->tableName)
                . ' WHERE zone_id = ' . $this->db->quote($zone_id);
        $this->db->setQuery($query);

        if ($this->db->query()) {
            return true;
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Construct an SQL statement based on the array of filters passed
     *
     * @param   array   $filters  Filters to build SQL from
     * @return  string  SQL
     */
    private function buildQuery($filters = [])
    {
        $where = [];

        if (isset($filters['zone_id']) && $filters['zone_id'] != '') {
            $where[] = "c.`zone_id`=" . $this->db->quote($filters['zone_id']);
        }

        $query  = "FROM $this->tableName AS c";
        $query .= " JOIN zones AS t ON c.zone_id=t.id";

        if (count($where) > 0) {
            $query .= " WHERE ";
            $query .= implode(" AND ", $where);
        }

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

                $query = "SELECT COUNT(*) " . $this->buildQuery($filters);

                $this->db->setQuery($query);
                return $this->db->loadResult();
                break;

            case 'all':
                $filters['limit'] = 0;
                return $this->find('list', $filters);
                break;

            case 'list':
            default:
                $query  = "SELECT c.*, t.zone " . $this->buildQuery($filters);

                if (!isset($filters['sort']) || !$filters['sort']) {
                    $filters['sort'] = 'c.zone_id';
                }
                if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) {
                    $filters['sort_Dir'] = 'ASC';
                }
                $query .= " ORDER BY "
                    . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['sort']) ?: '1')
                    . " "
                    . (strtoupper((string) $filters['sort_Dir']) === 'ASC' ? 'ASC' : 'DESC');

                if (isset($filters['limit']) && $filters['limit'] != 0) {
                    $query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
                }

                $this->db->setQuery($query);
                return $this->db->loadObjectList();
                break;
        }
    }
}
