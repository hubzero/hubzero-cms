<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration to add an index on #__resources.alias
 *
 * com_tools forgeTools() runs a correlated subquery that counts #__resources
 * rows by alias once per tool-version row. Without an index on `alias` each of
 * those subqueries is a full table scan of #__resources (tens of thousands of
 * rows), which makes the /tools listing hang. This index turns those scans into
 * single-row lookups.
 **/
class Migration20261101114458ComResources extends Base
{
    private function addIndex($table, $key, $fields)
    {
        if (is_array($fields)) {
            $indexfields = '';

            foreach ($fields as $f) {
                $indexfields .= "`" . $f . "`,";
            }
        } else {
            $indexfields = "`" . $fields . "`";
        }

        $indexfields = trim($indexfields, ",");

        if (!$this->db->tableHasKey($table, $key)) {
            $query = "ALTER TABLE `" . $table . "` ADD INDEX `" . $key . "` (" . $indexfields . ");";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    private function dropIndex($table, $key)
    {
        if ($this->db->tableHasKey($table, $key)) {
            $query = "DROP INDEX `" . $key . "` ON `" . $table . "`;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    public function up()
    {
        $this->addIndex('#__resources', 'idx_alias', 'alias');
    }

    public function down()
    {
        $this->dropIndex('#__resources', 'idx_alias');
    }
}
