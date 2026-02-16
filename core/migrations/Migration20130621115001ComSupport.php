<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing wrong datatype on column
 *
*/
class Migration20130621115001ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__abuse_reports')) {
            if (!$schema->hasColumn('#__abuse_reports', 'reviewed')) {
                $schema->alterTable('#__abuse_reports')->addColumn('reviewed')
                    ->datetime()
                    ->notNull()
                    ->default('0000-00-00 00:00:00')
                    ->execute();
            }

            if (!$schema->hasColumn('#__abuse_reports', 'reviewed_by')) {
                $schema->alterTable('#__abuse_reports')->addColumn('reviewed_by')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->hasColumn('#__abuse_reports', 'note')) {
                $schema->alterTable('#__abuse_reports')->addColumn('note')
                    ->text()
                    ->notNull()
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

        if ($schema->hasColumn('#__abuse_reports', 'reviewed')) {
            $schema->dropColumn('#__abuse_reports', 'reviewed');
        }

        if ($schema->hasColumn('#__abuse_reports', 'reviewed_by')) {
            $schema->dropColumn('#__abuse_reports', 'reviewed_by');
        }

        if ($schema->hasColumn('#__abuse_reports', 'note')) {
            $schema->dropColumn('#__abuse_reports', 'note');
        }
    }
}
