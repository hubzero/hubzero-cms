<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__citations tables
 *
*/
class Migration20170124191209ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations_authors')) {
            $schema->addIndex('#__citations_authors', 'idx_cid', 'cid');

            $schema->addIndex('#__citations_authors', 'idx_authorid', 'authorid');

            $schema->addIndex('#__citations_authors', 'idx_authorid', 'author_uid');

            $schema->addIndex('#__citations_authors', 'idx_uidNumber', 'uidNumber');
        }

        if ($schema->tableExists('#__citations_format')) {
            $schema->addIndex('#__citations_format', 'idx_typeid', 'typeid');
        }

        if ($schema->tableExists('#__citations_links')) {
            $schema->addIndex('#__citations_links', 'idx_citation_id', 'citation_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations_authors')) {
            $schema->dropIndex('#__citations_authors', 'idx_cid');

            $schema->dropIndex('#__citations_authors', 'idx_authorid');

            $schema->dropIndex('#__citations_authors', 'idx_uidNumber');
        }

        if ($schema->tableExists('#__citations_format')) {
            $schema->dropIndex('#__citations_format', 'idx_typeid');
        }

        if ($schema->tableExists('#__citations_links')) {
            $schema->dropIndex('#__citations_links', 'idx_citation_id');
        }
    }
}
