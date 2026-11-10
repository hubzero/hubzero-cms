<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication license
 */
class License extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_licenses', 'id', $db);
    }

    /**
     * Load a record and bind to $this
     *
     * @param   mixed  $oid  Integer or string (alias)
     * @return  mixed  False if error, Object on success
     */
    public function loadLicense($oid = null)
    {
        if ($oid === null) {
            return false;
        }

        if (is_numeric($oid)) {
            return parent::load($oid);
        }

        $oid = trim($oid);

        $this->db->setQuery("SELECT * FROM $this->tableName WHERE name=" . $this->db->quote($oid));
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Validate data
     *
     * @return  boolean  True if data is valid
     */
    public function check()
    {
        $this->text = trim($this->text);

        if (!$this->title) {
            $this->title = substr($this->text, 0, 70);
            if (strlen($this->title >= 70)) {
                $this->title .= '...';
            }
        }

        if (!$this->name) {
            $this->name = $this->title;
        }
        $this->name = str_replace(' ', '-', strtolower($this->name));
        $this->name = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->name);

        return true;
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
        if (isset($filters['state'])) {
            $where[] = "c.state=" . $this->db->quote($filters['state']);
        }
        if (isset($filters['search']) && $filters['search'] != '') {
            $where[] = "(LOWER(c.title) LIKE " . $this->db->quote('%' . strtolower($filters['search']) . '%') . "
				OR LOWER(c.`text`) LIKE " . $this->db->quote('%' . strtolower($filters['search']) . '%') . ")";
        }

        if (count($where) > 0) {
            $query .= " WHERE ";
            $query .= implode(" AND ", $where);
        }

        return $query;
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
            $filters['sort'] = 'title';
        }
        if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) {
            $filters['sort_Dir'] = 'DESC';
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
    }

    /**
     * Get licenses
     *
     * @param   array   $filters  Filters to build query from
     * @return  object
     */
    public function getLicenses($filters = [])
    {
        $sortby  = isset($filters['sortby']) && $filters['sortby'] != '' ? $filters['sortby'] : 'ordering';

        $query  = "SELECT * FROM $this->tableName ";
        $query .= " WHERE active=1 ";
        $query .= " ORDER BY " . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $sortby) ?: '1');

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get licenses
     *
     * @param   object  $manifest
     * @param   object  $selected
     * @return  object
     */
    public function getBlockLicenses($manifest = null, $selected = null)
    {
        if (!$manifest) {
            return false;
        }

        $include = isset($manifest->params->include) ? $manifest->params->include : [];
        $exclude = isset($manifest->params->exclude) ? $manifest->params->exclude : [];

        $query = "SELECT * FROM $this->tableName WHERE active=1 ";
        if ($include && !empty($include)) {
            $query .= " AND (";
            $i = 0;
            foreach ($include as $inc) {
                $i++;
                $query .= "id=" . $inc;
                $query .= $i == count($include) ? " " : " OR ";
            }
            $query .= ")";
        }
        if ($exclude && !empty($exclude)) {
            $query .= " AND (";
            $i = 0;
            foreach ($exclude as $ex) {
                $i++;
                $query .= "id != " . $ex;
                $query .= $i == count($exclude) ? " " : " AND ";
            }
            $query .= ")";
        }
        if ($selected && !in_array($selected->id, $include)) {
            $query .= " OR id=" . $selected->id;
        }

        $query .= " ORDER BY ordering";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get default license
     *
     * @return  object
     */
    public function getDefaultLicense()
    {
        $query  = "SELECT * FROM $this->tableName ";
        $query .= " WHERE main=1 ";
        $query .= " LIMIT 1";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get license by id
     *
     * @param   integer  $id  License ID
     * @return  mixed    False if error, Object on success
     */
    public function getLicense($id = 0)
    {
        if (!$id) {
            return false;
        }
        $query  = "SELECT * FROM $this->tableName ";
        $query .= " WHERE id=" . $this->db->quote($id);
        $query .= " LIMIT 1";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        return $result ? $result[0] : false;
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

    /**
     * Get license by name
     *
     * @param   string  $name  License name
     * @return  mixed   False if error, Object on success
     */
    public function getLicenseByName($name = '')
    {
        if (!$name) {
            return false;
        }
        $query  = "SELECT * FROM $this->tableName ";
        $query .= " WHERE name LIKE " . $this->db->quote($name);
        $query .= " LIMIT 1";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        return $result ? $result[0] : false;
    }

    /**
     * Get license by title
     *
     * @param   string  $title  License title
     * @return  mixed   False if error, Object on success
     */
    public function getLicenseByTitle($title = '')
    {
        if (!$title) {
            return false;
        }
        $query  = "SELECT * FROM $this->tableName ";
        $query .= " WHERE title LIKE " . $this->db->quote($title);
        $query .= " LIMIT 1";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        return $result ? $result[0] : false;
    }

    /**
     * Get license by pub version id
     *
     * @param   integer  $vid  Pub version ID
     * @return  mixed    False if error, Object on success
     */
    public function getPubLicense($vid = null)
    {
        if (!$vid) {
            return false;
        }

        $query = "SELECT L.*, v.license_type, v.license_text FROM $this->tableName AS L, `#__publication_versions` AS "
            . "v ";
        $query .= " WHERE v.id=" . $this->db->quote($vid) . " AND v.license_type=L.id";
        $query .= " LIMIT 1";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        return $result ? $result[0] : false;
    }

    /**
     * Make license  not default
     *
     * @param   integer  $except  License ID to exclude
     * @return  boolean
     */
    public function undefault($except = 0)
    {
        $query  = "UPDATE $this->tableName SET main=0 ";
        $query .= $except ? " WHERE id != " . $this->db->quote($except) : '';
        $this->db->setQuery($query);
        if ($this->db->query()) {
            return true;
        }
        return false;
    }
}
