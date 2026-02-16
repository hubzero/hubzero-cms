<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'username', 'email', 'disability' columns to profile_completion_award table
 *
*/
class Migration20171106183501ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__profile_completion_awards')) {
            if (!$schema->hasColumn('#__profile_completion_awards', 'username')) {
                $schema->addColumn('#__profile_completion_awards', 'username')->tinyInteger(4)->notNull()->default('0');
            }

            if (!$schema->hasColumn('#__profile_completion_awards', 'email')) {
                $schema->addColumn('#__profile_completion_awards', 'email')->tinyInteger(4)->notNull()->default('0');
            }

            if (!$schema->hasColumn('#__profile_completion_awards', 'disability')) {
                $schema->addColumn('#__profile_completion_awards', 'disability')
                    ->tinyInteger(4)
                    ->notNull()
                    ->default('0');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__profile_completion_awards')) {
            if ($schema->hasColumn('#__profile_completion_awards', 'username')) {
                $schema->dropColumn('#__profile_completion_awards', 'username');
            }

            if ($schema->hasColumn('#__profile_completion_awards', 'email')) {
                $schema->dropColumn('#__profile_completion_awards', 'email');
            }

            if ($schema->hasColumn('#__profile_completion_awards', 'disability')) {
                $schema->dropColumn('#__profile_completion_awards', 'disability');
            }
        }
    }
}
