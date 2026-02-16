<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding length and effort fields to courses. Fixes fulltext index name.
 *
*/
class Migration20141112142733ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses')) {
            if (!$schema->hasColumn('#__courses', 'length')) {
                $schema->addColumn('#__courses', 'length')
                    ->string(255)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }

            if (!$schema->hasColumn('#__courses', 'effort')) {
                $schema->addColumn('#__courses', 'effort')
                    ->string(255)
                    ->nullable()
                    ->default(null)
                    ->execute();
            }

            $schema->dropIndex('#__courses', 'jos_xgroups_cn_description_public_desc_ftidx');

            $schema->addFulltextIndex('#__courses', 'ftidx_alias_title_blurb', ['alias', 'title', 'blurb']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses')) {
            if ($schema->hasColumn('#__courses', 'length')) {
                $schema->dropColumn('#__courses', 'length');
            }

            if ($schema->hasColumn('#__courses', 'effort')) {
                $schema->dropColumn('#__courses', 'effort');
            }
        }
    }
}
