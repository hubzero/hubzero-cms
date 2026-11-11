<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication collaborator
 */
class Collaborator extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_collaborators', 'id', $db);
    }

    /**
     * Get collaborator by name
     *
     * @param   string  $name
     *
     * @return  object|false
     */
    public function getCollaboratorByName($name)
    {
        if (!$name) {
            return false;
        }

        $query  = "SELECT * FROM $this->tableName WHERE name=" . $this->db->quote($name);
        $this->db->setQuery($query);

        return $this->db->loadObject();
    }

    /**
     * Save ORCID Record
     *
     * @param   string  $name
     * @param   string  $orcid
     * @param   string  $accessToken
     *
     * @return  object or false
     */
    public function saveORCIDRecord($name, $orcid, $accessToken)
    {
        if (empty($name) || empty($orcid) || empty($accessToken)) {
            return false;
        }

        $record = $this->getCollaboratorByName($name);

        if (empty($record)) {
            $query = "INSERT INTO $this->tableName (id, name, orcid, access_token, acquisition_date) VALUES ("
                . $this->db->quote($name) . ","
                . $this->db->quote($orcid) . ","
                . $this->db->quote($accessToken) . ","
                . $this->db->quote(date('Y-m-d H:i:s')) . ")";
        } elseif (empty($record->orcid) && empty($record->access_token)) {
            $query = "UPDATE $this->tableName SET orcid=" . $this->db->quote($orcid)
                . " AND access_token=" . $this->db->quote($accessToken)
                . " WHERE name=" . $this->db->quote($name);
        } else {
            // The collaborator already has ORCID details; nothing is written
            return false;
        }

        $this->db->setQuery($query);
        return $this->db->query();
    }
}
