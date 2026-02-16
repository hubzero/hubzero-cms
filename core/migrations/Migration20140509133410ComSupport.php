<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating support categories field names
 *
*/
class Migration20140509133410ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_categories')) {
            if (!$schema->hasColumn('#__support_categories', 'section_id')) {
                $schema->renameColumn('section', 'section_id')->integer()->notNull()->default(0);
            }
            if ($schema->hasColumn('#__support_categories', 'category')) {
                $schema->renameColumn('category', 'alias')->string(250)->notNull()->default('');
            }
            if (!$schema->hasColumn('#__support_categories', 'title')) {
                $schema->addColumn('title')->string(255)->notNull()->default('');
            }
            if (!$schema->hasColumn('#__support_categories', 'created')) {
                $schema->addColumn('created')->datetime()->notNull()->default('0000-00-00 00:00:00');
            }
            if (!$schema->hasColumn('#__support_categories', 'created_by')) {
                $schema->addColumn('created_by')->integer()->notNull()->default(0);
            }
            if (!$schema->hasColumn('#__support_categories', 'modified')) {
                $schema->addColumn('modified')->datetime()->notNull()->default('0000-00-00 00:00:00');
            }
            if (!$schema->hasColumn('#__support_categories', 'modified_by')) {
                $schema->addColumn('modified_by')->integer()->notNull()->default(0);
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__support_categories')) {
            if ($schema->hasColumn('#__support_categories', 'section_id')) {
                $schema->renameColumn('section_id', 'section')->integer()->notNull()->default(0);
            }
            if ($schema->hasColumn('#__support_categories', 'alias')) {
                $schema->renameColumn('alias', 'category')->string(50)->notNull()->default('');
            }
            if ($schema->hasColumn('#__support_categories', 'title')) {
                $schema->dropColumn('#__support_categories', 'title');
            }
            if ($schema->hasColumn('#__support_categories', 'created')) {
                $schema->dropColumn('#__support_categories', 'created');
            }
            if ($schema->hasColumn('#__support_categories', 'created_by')) {
                $schema->dropColumn('#__support_categories', 'created_by');
            }
            if ($schema->hasColumn('#__support_categories', 'modified')) {
                $schema->dropColumn('#__support_categories', 'modified');
            }
            if ($schema->hasColumn('#__support_categories', 'modified_by')) {
                $schema->dropColumn('#__support_categories', 'modified_by');
            }
        }
    }
}
