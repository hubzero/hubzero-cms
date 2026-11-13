<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for leaving each publication with one main version.
 *
 * From July 2015 the main flag was never taken off the previous version when
 * a newer one was published, so a publication collected one main version per
 * release. The newest published one of them is the one meant to be main
 * (the newest of any state where none is published); the others lose the
 * flag.
 **/
class Migration20261113120000ComPublications extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        if (
            !$this->db->tableExists('#__publication_versions')
            || !$this->db->tableHasField('#__publication_versions', 'main')
        ) {
            return;
        }

        $query = 'SELECT ' . $this->db->quoteName('id') . ', ' . $this->db->quoteName('publication_id')
            . ' FROM ' . $this->db->quoteName('#__publication_versions')
            . ' WHERE ' . $this->db->quoteName('main') . ' = 1'
            . ' ORDER BY ' . $this->db->quoteName('publication_id') . ' ASC, '
            . 'CASE WHEN ' . $this->db->quoteName('state') . ' = 1 THEN 0 ELSE 1 END ASC, '
            . $this->db->quoteName('version_number') . ' DESC, '
            . $this->db->quoteName('id') . ' DESC';
        $this->db->setQuery($query);
        $rows = $this->db->loadObjectList();

        // The first row of each publication is the main version it keeps.
        $kept  = [];
        $extra = [];
        foreach ($rows ?: [] as $row) {
            if (isset($kept[$row->publication_id])) {
                $extra[] = (int) $row->id;
                continue;
            }
            $kept[$row->publication_id] = true;
        }

        foreach (array_chunk($extra, 500) as $ids) {
            $query = 'UPDATE ' . $this->db->quoteName('#__publication_versions')
                . ' SET ' . $this->db->quoteName('main') . ' = 0'
                . ' WHERE ' . $this->db->quoteName('id') . ' IN (' . implode(',', $ids) . ')';
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     *
     * Which versions carried the extra flags is not recorded, so there is
     * nothing to put back.
     *
     * @return  void
     **/
    public function down() {}
}
