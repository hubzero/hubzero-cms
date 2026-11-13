<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Members quota classes db table class
 */
class SessionClass extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__tool_session_classes', 'id', $db);
    }

    /**
     * Override the check function to do a little input cleanup
     *
     * @return  boolean  True if valid, False if not
     */
    public function check()
    {
        // Make sure they gave an alias
        if (trim($this->alias) == '') {
            $this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_MUST_HAVE_ALIAS'));
        }

        // Make sure the alias isn't 'custom'
        if (trim($this->alias) == 'custom') {
            $this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_CUSTOM'));
        }

        if ($this->getError()) {
            return false;
        }

        $this->jobs = intval($this->jobs);

        if (!$this->id) {
            $query  = "SELECT id" . $this->buildQuery(['alias' => $this->alias]);

            $this->db->setQuery($query);
            if ($this->db->loadResult()) {
                $this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_NON_UNIQUE_ALIAS'));
                return false;
            }

            $query  = "SELECT id" . $this->buildQuery(['jobs' => $this->jobs]);

            $this->db->setQuery($query);
            if ($this->db->loadResult()) {
                $this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_NON_UNIQUE_VALUE'));
                return false;
            }
        }

        return true;
    }

    /**
     * Override store to add logging
     *
     * @return  boolean
     */
    public function createDefault()
    {
        $tbl = new self($this->db);
        $tbl->alias = 'default';
        $tbl->jobs  = 3;

        if (!$tbl->check()) {
            $this->setError($tbl->getError());
            return false;
        }

        if (!$tbl->store()) {
            $this->setError($tbl->getError());
            return false;
        }

        return true;
    }

    /**
     * Get a user count
     *
     * @return  string|null
     */
    public function userCount()
    {
        $query = "SELECT COUNT(*) FROM `#__users_tool_preferences` "
            . "WHERE `class_id`=" . $this->db->quote($this->id);
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Build query method
     *
     * @param   array   $filters
     * @return  string  Database query
     */
    protected function buildQuery($filters = [])
    {
        $query = " FROM $this->tableName AS tsc";

        $where = [];

        if (isset($filters['alias']) && $filters['alias']) {
            $where[] = "`alias`=" . $this->db->quote($filters['alias']);
        }

        if (isset($filters['jobs']) && $filters['jobs']) {
            $where[] = "`jobs`=" . $this->db->quote($filters['jobs']);
        }

        if (count($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        return $query;
    }

    /**
     * Get a count of, single entry, or list of entries
     *
     * @param   string   $what     Data to return
     * @param   array    $filters  Filters to apply to data retrieval
     * @param   array    $select   List of fields to select
     * @return  mixed
     */
    public function find($what = '', $filters = [], $select = [])
    {
        $what = strtolower($what);
        $select = (array) $select;

        switch ($what) {
            case 'count':
                $query = "SELECT COUNT(*) " . $this->buildQuery($filters);

                $this->db->setQuery($query);
                return $this->db->loadResult();

            case 'one':
                $filters['limit'] = 1;

                $result = null;
                if ($results = $this->find('list', $filters)) {
                    $result = $results[0];
                }

                return $result;

            case 'first':
                $filters['start'] = 0;

                return $this->find('one', $filters);

            case 'all':
                if (isset($filters['limit'])) {
                    unset($filters['limit']);
                }
                return $this->find('list', $filters);

            case 'list':
            default:
                if (!isset($filters['sort'])) {
                    $filters['sort'] = 'id';
                }
                if (!isset($filters['sort_Dir'])) {
                    $filters['sort_Dir'] = 'ASC';
                }
                if ($filters['sort_Dir']) {
                    $filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
                    if (!in_array($filters['sort_Dir'], ['ASC', 'DESC'])) {
                        $filters['sort_Dir'] = 'ASC';
                    }
                }

                if (empty($select)) {
                    $select = ['*'];
                }

                $query  = "SELECT " . implode(', ', $select) . " " . $this->buildQuery($filters);
                $query .= " ORDER BY "
                    . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['sort']) ?: '1')
                    . " "
                    . (strtoupper((string) $filters['sort_Dir']) === 'ASC' ? 'ASC' : 'DESC');

                if (isset($filters['limit']) && $filters['limit'] > 0) {
                    $filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

                    $query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
                }

                $this->db->setQuery($query);
                return $this->db->loadObjectList();
        }
    }

    /**
     * Get the an object list of quotas classes
     *
     * @param   array  $id  Start and limit, needed for pagination
     * @return  array  Return password rule records
     */
    public function getGroupIds($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }

        $groups = [];

        if ($id) {
            $qcGroups = new SessionClassGroup($this->db);
            foreach ($qcGroups->find('list', ['class_id' => $id]) as $group) {
                $groups[] = $group->group_id;
            }
        }

        return $groups;
    }

    /**
     * Get the an object list of quotas classes
     *
     * @param   array  $groups  Start and limit, needed for pagination
     * @return  array|bool  Return password rule records
     */
    public function setGroupIds($groups = [])
    {
        if (!is_array($groups)) {
            $groups = [$groups];
        }
        $groups = array_map('intval', $groups);

        $qcGroups = new SessionClassGroup($this->db);
        $qcGroups->class_id = $this->id;

        // Clear old records
        if (!$qcGroups->deleteByClassId($this->id)) {
            $this->setError($qcGroups->getError());
            return false;
        }

        foreach ($groups as $group) {
            $qcGroups->id       = null;
            $qcGroups->group_id = $group;
            if (!$qcGroups->store()) {
                $this->setError($qcGroups->getError());
                return false;
            }
        }

        return true;
    }
}
