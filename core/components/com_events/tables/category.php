<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Events table class for category
 */
class Category extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__categories', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  True if data is valid
     */
    public function check()
    {
        // check for valid name
        if (trim($this->title) == '') {
            $this->_error = Lang::txt('EVENTS_CATEGORY_MUST_HAVE_TITLE');
            return false;
        }
        return true;
    }

    /**
     * Update the count of an entry
     *
     * @param   integer  $oid  Category ID
     * @return  void
     */
    public function updateCount($oid = null)
    {
        if ($oid == null) {
            $oid = $this->id;
        }
        $this->db->setQuery("UPDATE $this->tableName SET count = count-1 WHERE id=" . $this->db->quote($oid));
        $this->db->query();
    }

    /**
     * Set en entry to unpublished
     *
     * @param   integer  $oid     Category ID
     * @param   integer  $state
     * @param   integer  $userId
     * @return  void
     */
    public function publish($oid = null, $state = 1, $userId = 0)
    {
        if (!$oid) {
            $oid = $this->id;
        }
        $this->db->setQuery("UPDATE $this->tableName SET published=1 WHERE id=" . $this->db->quote($oid));
        $this->db->query();
    }

    /**
     * Set an entry to published
     *
     * @param   integer  $oid  Category ID
     * @return  void
     */
    public function unpublish($oid = null)
    {
        if (!$oid) {
            $oid = $this->id;
        }
        $this->db->setQuery("UPDATE $this->tableName SET published=0 WHERE id=" . $this->db->quote($oid));
        $this->db->query();
    }

    /**
     * Get a count of categories in a section
     *
     * @param   integer  $section  Section ID
     * @return  integer
     */
    public function getCategoryCount($section = null)
    {
        if (!$section) {
            $section = $this->section;
        }
        $this->db->setQuery("SELECT COUNT(*) FROM $this->tableName WHERE extension=" . $this->db->quote($section));
        return $this->db->loadResult();
    }
}
