<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding fulltext index to com_citations_authors table
  *
**/
class Migration20170615151021ComCitation extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations_authors')) {
            $schema->addFulltextIndex(
                '#__citations_authors',
                'ftidx_jos_citations_authors_author_givenName_surname',
                ['author', 'givenName', 'surname']
            );
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__citations_authors')) {
            $schema->dropIndex('#__citations_authors', 'ftidx_jos_citations_authors_author_givenName_surname');
        }
    }
}
