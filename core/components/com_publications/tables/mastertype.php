<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Table class for publication master type
 */
class MasterType extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_master_types', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  True if data is valid
     */
    public function check()
    {
        if (trim($this->type) == '') {
            $this->setError(Lang::txt('Your publication master type must contain text.'));
            return false;
        }
        if (trim($this->alias) == '') {
            $this->setError(Lang::txt('Your publication master type alias must contain text.'));
            return false;
        }
        // the new-type form posts an empty ordering box; strict SQL refuses ''
        // for the integer column, so put a new type at the end
        if (trim((string) $this->ordering) === '') {
            $this->db->setQuery("SELECT MAX(ordering) FROM $this->tableName");
            $this->ordering = (int) $this->db->loadResult() + 1;
        }
        return true;
    }

    /**
     * Get record by alias name or ID
     *
     * @param   string  $id
     * @return  mixed   object or false
     */
    public function getType($id = '')
    {
        if (!$id) {
            return false;
        }
        $field = is_numeric($id) ? 'id' : 'alias';

        $this->db->setQuery("SELECT * FROM $this->tableName WHERE $field=" . $this->db->quote($id) . " LIMIT 1");
        $result = $this->db->loadObjectList();
        return $result ? $result[0] : false;
    }

    /**
     * Get record id by alias name
     *
     * @param   string  $alias
     * @return  integer|false
     */
    public function getTypeId($alias = '')
    {
        if (!$alias) {
            return false;
        }
        $this->db->setQuery("SELECT id FROM $this->tableName WHERE alias=" . $this->db->quote($alias) . " LIMIT 1");
        return $this->db->loadResult();
    }

    /**
     * Get record alias by id
     *
     * @param   integer|string $id
     * @return  integer|false
     */
    public function getTypeAlias($id = '')
    {
        if (!$id) {
            return false;
        }
        $this->db->setQuery("SELECT alias FROM $this->tableName WHERE id=" . $this->db->quote($id) . " LIMIT 1");
        return $this->db->loadResult();
    }

    /**
     * Get curator groups
     *
     * @return  array
     */
    public function getCuratorGroups()
    {
        $groups = [];

        $query = "SELECT curatorgroup FROM $this->tableName WHERE contributable=1
				  AND curatorgroup !=0 AND curatorgroup IS NOT null";

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        if ($results) {
            foreach ($results as $result) {
                if (trim($result->curatorgroup)) {
                    $groups[] = $result->curatorgroup;
                }
            }
        }

        return $groups;
    }

    /**
     * Get types for which user is authorized (curation)
     *
     * @param   array  $usergroups
     * @param   bool   $authorized
     * @return  mixed  array or False
     */
    public function getAuthTypes($usergroups = [], $authorized = false)
    {
        $types = [];

        if (empty($usergroups)) {
            return false;
        }
        if ($authorized == 'admin' || $authorized == 'curator') {
            // Access to all types
            $query = "SELECT id FROM $this->tableName WHERE contributable=1";
        } else {
            $query = "SELECT id FROM $this->tableName WHERE contributable=1
					  AND curatorgroup !=0 AND curatorgroup IS NOT null ";

            $tquery = '';
            foreach ($usergroups as $g) {
                $tquery .= "'" . $g->gidNumber . "',";
            }
            $tquery = substr($tquery, 0, strlen($tquery) - 1);
            $query .= " AND (curatorgroup IN (" . $tquery . ")) ";
        }

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        if ($results) {
            foreach ($results as $result) {
                if (trim($result->id)) {
                    $types[] = $result->id;
                }
            }
        }

        return $types;
    }

    /**
     * Get records
     *
     * @param   string   $select         Select query
     * @param   integer  $contributable  Contributable?
     * @param   integer  $supporting     Supporting?
     * @param   string   $orderby        Order by
     * @param   string   $config
     * @return  array
     */
    public function getTypes($select = '*', $contributable = 0, $supporting = 0, $orderby = 'id', $config = '')
    {
        $query  = "SELECT $select FROM $this->tableName ";
        if ($contributable) {
            $query .= "WHERE contributable=1 ";
        } elseif ($supporting) {
            $query .= "WHERE supporting=1 ";
        }

        $query .= "ORDER BY " . $orderby;

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        if ($select == 'alias') {
            $types = [];
            if ($results) {
                foreach ($results as $result) {
                    $types[] = $result->alias;
                }
            }
            return $types;
        }
        return $results;
    }

    /**
     * Get records
     *
     * @param   array  $filters  Filters to build query from
     * @return  array
     */
    public function getRecords($filters = [])
    {
        $query  = "SELECT c.*";
        $query .= $this->buildQuery($filters);

        if (!isset($filters['sort']) || !$filters['sort']) {
            $filters['sort'] = 'id';
        }
        if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) {
            $filters['sort_Dir'] = 'DESC';
        }
        $query .= " ORDER BY " . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['sort']) ?: '1')
            . " " . ((strtoupper(trim($filters['sort_Dir'])) == 'ASC') ? 'ASC' : 'DESC');

        if (isset($filters['limit']) && $filters['limit'] != 0) {
            $query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
        }

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get record counts
     *
     * @param   array  $filters  Filters to build query from
     * @return  array
     */
    public function getCount($filters = [])
    {
        $filters['limit'] = 0;

        $query = "SELECT COUNT(*) " . $this->buildQuery($filters);

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Build a query from filters
     *
     * @param   array   $filters  Filters to build query from
     * @return  string  SQL
     */
    protected function buildQuery($filters = [])
    {
        $query  = "FROM $this->tableName AS c";

        $where = [];

        if (count($where) > 0) {
            $query .= " WHERE ";
            $query .= implode(" AND ", $where);
        }

        return $query;
    }

    /**
     * Check type usage
     *
     * @param   integer  $id  type id
     * @return  integer|false
     */
    public function checkUsage($id = null)
    {
        if (!$id) {
            $id = $this->id;
        }
        if (!$id) {
            return false;
        }

        $p = new \Components\Publications\Tables\Publication($this->db);

        $this->db->setQuery("SELECT count(*) FROM $p->tableName WHERE master_type=" . $this->db->quote($id));
        return $this->db->loadResult();
    }

    /**
     * Load by ordering
     *
     * @param   mixed  $ordering  Integer or string (alias)
     * @return  mixed  False if error, Object on success
     */
    public function loadByOrder($ordering = null)
    {
        if ($ordering === null) {
            return false;
        }

        $this->db->setQuery(
            "SELECT * FROM $this->tableName WHERE ordering=" . $this->db->quote($ordering) . " LIMIT 1",
        );
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Change order
     *
     * @param   integer  $dir
     * @return  mixed    False if error, Object on success
     */
    public function changeOrder($dir)
    {
        $newOrder = $this->ordering + $dir;

        // Load record in prev position
        $old = new self($this->db);

        if ($old->loadByOrder($newOrder)) {
            $old->ordering  = $this->ordering;
            $old->store();
        }

        $this->ordering = $newOrder;
        $this->store();

        return true;
    }
}
