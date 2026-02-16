<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Publications - Forks plugin
 *
*/
class Migration20170623162652PlgPublicationsForks extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->addPluginEntry('publications', 'forks', 0);

        if ($schema->tableExists('#__publication_versions')) {
            if (!$schema->hasColumn('#__publication_versions', 'forked_from')) {
                $schema->addColumn('#__publication_versions', 'forked_from')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->deletePluginEntry('publications', 'forks');

        if ($schema->tableExists('#__publication_versions')) {
            if ($schema->hasColumn('#__publication_versions', 'forked_from')) {
                $schema->dropColumn('#__publication_versions', 'forked_from');
            }
        }
    }
}
