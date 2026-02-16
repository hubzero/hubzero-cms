<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding FULLTEXT indexes to publication versions
**/
class Migration20141028094100ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__publication_versions', 'title')) {
            $schema->addFulltextIndex('#__publication_versions', 'ftidx_title', ['title']);
        }

        if (
            $schema->hasColumn('#__publication_versions', 'abstract')
            && $schema->hasColumn('#__publication_versions', 'description')
        ) {
            $schema->addFulltextIndex('#__publication_versions', 'ftidx_abstract_description', [
                'abstract',
                'description',
            ]);
        }

        if (
            $schema->hasColumn('#__publication_versions', 'title')
            && $schema->hasColumn('#__publication_versions', 'abstract')
            && $schema->hasColumn('#__publication_versions', 'description')
        ) {
            $schema->addFulltextIndex('#__publication_versions', 'ftidx_title_abstract_description', [
                'title',
                'abstract',
                'description',
            ]);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__publication_versions', 'ftidx_title');

        $schema->dropIndex('#__publication_versions', 'ftidx_abstract_description');

        $schema->dropIndex('#__publication_versions', 'ftidx_title_abstract_description');
    }
}
