<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'unique' and 'unfiltered' fields to publication logs table
 *
*/
class Migration20141119164113ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_logs')) {
            if (!$schema->hasColumn('#__publication_logs', 'page_views_unfiltered')) {
                $schema->addColumn('#__publication_logs', 'page_views_unfiltered')->integer(11);
            }
            if (!$schema->hasColumn('#__publication_logs', 'primary_accesses_unfiltered')) {
                $schema->addColumn('#__publication_logs', 'primary_accesses_unfiltered')->integer(11);
            }
            if (!$schema->hasColumn('#__publication_logs', 'page_views_unique')) {
                $schema->addColumn('#__publication_logs', 'page_views_unique')->integer(11);
            }
            if (!$schema->hasColumn('#__publication_logs', 'primary_accesses_unique')) {
                $schema->addColumn('#__publication_logs', 'primary_accesses_unique')->integer(11);
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_logs')) {
            if ($schema->hasColumn('#__publication_logs', 'page_views_unfiltered')) {
                $schema->dropColumn('#__publication_logs', 'page_views_unfiltered');
            }
            if ($schema->hasColumn('#__publication_logs', 'primary_accesses_unfiltered')) {
                $schema->dropColumn('#__publication_logs', 'primary_accesses_unfiltered');
            }
            if ($schema->hasColumn('#__publication_logs', 'page_views_unique')) {
                $schema->dropColumn('#__publication_logs', 'page_views_unique');
            }
            if ($schema->hasColumn('#__publication_logs', 'primary_accesses_unique')) {
                $schema->dropColumn('#__publication_logs', 'primary_accesses_unique');
            }
        }
    }
}
