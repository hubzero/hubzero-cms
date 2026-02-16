<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices and setting default field value for #__abuse_reports
**/
class Migration20140818160800ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__abuse_reports')) {
            // Standardize column types
            $schema->modifyColumn('#__abuse_reports', 'id')
                ->integer()
                ->unsigned()
                ->notNull()
                ->autoIncrement()
                ->execute();
            $schema->modifyColumn('#__abuse_reports', 'created_by')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
            $schema->modifyColumn('#__abuse_reports', 'reviewed_by')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
            $schema->modifyColumn('#__abuse_reports', 'referenceid')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->execute();
            $schema->modifyColumn('#__abuse_reports', 'state')->tinyInteger()->notNull()->default(0)->execute();

            $schema->addIndex('#__abuse_reports', 'idx_created_by', 'created_by');

            $schema->addIndex('#__abuse_reports', 'idx_reviewed_by', 'reviewed_by');

            $schema->addIndex('#__abuse_reports', 'idx_state', 'state');

            $schema->addIndex('#__abuse_reports', 'idx_category_referenceid', ['category', 'referenceid']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__abuse_reports')) {
            $schema->dropIndex('#__abuse_reports', 'idx_created_by');

            $schema->dropIndex('#__abuse_reports', 'idx_reviewed_by');

            $schema->dropIndex('#__abuse_reports', 'idx_state');

            $schema->dropIndex('#__abuse_reports', 'idx_reference');
        }
    }
}
