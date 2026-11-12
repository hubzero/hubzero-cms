<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication access
 */
class Access extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_access', 'publication_version_id', $db);
    }

    /**
     * Load entry
     *
     * @param   integer  $vid       pub version id
     * @param   integer  $group_id  group id
     * @return  object or FALSE
     */
    public function loadEntry($vid = null, $group_id = null)
    {
        if (!$vid) {
            $vid = $this->publication_version_id;
        }
        if (!$vid || !$group_id) {
            return false;
        }

        return parent::load([
            'publication_version_id' => (int) $vid,
            'group_id'               => (int) $group_id,
        ]);
    }

    /**
     * Check record existence
     *
     * @param   integer  $vid       pub version id
     * @param   integer  $group_id  group id
     * @return  mixed    integer or null
     */
    public function existsEntry($vid = null, $group_id = null)
    {
        if (!$vid) {
            $vid = $this->publication_version_id;
        }
        if (!$vid || !$group_id) {
            return false;
        }

        $this->db->setQuery("SELECT publication_version_id FROM $this->tableName
			WHERE publication_version_id=" . $this->db->quote($vid) . "
			AND group_id=" . $this->db->quote($group_id));
        return $this->db->loadResult();
    }

    /**
     * Get groups
     *
     * @param   integer  $vid       pub version id
     * @param   integer  $pid       pub id
     * @param   string   $version   version name or number
     * @param   string   $sysgroup  name of system group
     * @return  object
     */
    public function getGroups($vid = null, $pid = null, $version = 'default', $sysgroup = '')
    {
        if (!$vid) {
            $vid = $this->publication_version_id;
        }
        if (!$vid && !$pid) {
            return false;
        }

        $query  = "SELECT A.group_id, X.cn, X.description, X.published as regconfirmed FROM $this->tableName as A ";
        $query .= " JOIN #__xgroups AS X ON X.gidNumber = A.group_id ";
        if ($vid) {
            $query .= "WHERE A.publication_version_id=" . $this->db->quote($vid);
        } else {
            $query .= " JOIN #__publication_versions AS V ON V.id=A.publication_version_id ";
            $query .= " WHERE V.publication_id=" . $this->db->quote($pid);
            if (in_array($version, ['default', 'current', 'main'])) {
                $query .= " AND V.main=1 ";
            } elseif ($version == 'dev') {
                $query .= " AND V.state=3 ";
            } elseif (intval($version)) {
                $query .= " AND V.version_number=" . $this->db->quote($version);
            } else {
                // Error in supplied version value
                $query .= " AND 1=2 ";
            }
        }
        if ($sysgroup) {
            $query .= " AND X.cn !=" . $this->db->quote($sysgroup);
        }

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Save groups
     *
     * @param   integer  $vid       pub version id
     * @param   array|string $groups
     * @param   string|int $sysgroup  name of system group
     * @return  integer
     */
    public function saveGroups($vid = null, $groups = '', $sysgroup = 0)
    {
        if (!$vid) {
            $vid = $this->publication_version_id;
        }
        if (!$vid) {
            return false;
        }

        // Clean up
        $this->deleteGroups($vid, $sysgroup);

        $add = [];
        $saved = 0;
        if ($groups) {
            $add = explode(',', $groups);
        }
        if ($sysgroup) {
            $add[] = $sysgroup;
        }

        foreach ($add as $a) {
            $a = trim($a);
            if ($a == '') {
                continue;
            }

            $group = \Hubzero\User\Group::getInstance($a);
            $gid = $group ? $group->get('gidNumber') : 0;

            if (!$gid or $this->existsEntry($vid, $gid)) {
                continue;
            } else {
                $query = "INSERT INTO $this->tableName (publication_version_id,group_id)
				          VALUES($this->db->quote($vid), $this->db->quote($gid))";
                $this->db->setQuery($query);
                if ($this->db->query()) {
                    $saved++;
                }
            }
        }

        return $saved;
    }

    /**
     * Delete groups
     *
     * @param   integer  $vid       pub version id
     * @param   string|int $sysgroup  name of system group
     * @return  boolean
     */
    public function deleteGroups($vid = null, $sysgroup = 0)
    {
        if (!$vid) {
            $vid = $this->publication_version_id;
        }
        if (!$vid) {
            return false;
        }

        $query = "DELETE FROM $this->tableName WHERE publication_version_id=" . $this->db->quote($vid);
        $query .= $sysgroup ? " AND group_id !=" . $this->db->quote($sysgroup) : "";
        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
        return true;
    }
}
