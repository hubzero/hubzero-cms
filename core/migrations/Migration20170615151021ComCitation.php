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
        if ($this->db->tableExists('#__citations_authors')) {
            $key = 'ftidx_jos_citations_authors_author_givenName_surname';
            if (!$this->db->tableHasKey('#__citations_authors', $key)) {
                $query = "ALTER TABLE `#__citations_authors` ADD FULLTEXT "
                    . "`ftidx_jos_citations_authors_author_givenName_surname` (`author`, `givenName`, `surname`);";
                $this->db->setQuery($query);
                $this->db->query();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists('#__citations_authors')) {
            $key = 'ftidx_jos_citations_authors_author_givenName_surname';
            if ($this->db->tableHasKey('#__citations_authors', $key)) {
                $query = "ALTER TABLE `#__citations_authors` DROP INDEX "
                    . "`ftidx_jos_citations_authors_author_givenName_surname`;";
                $this->db->setQuery($query);
                $this->db->query();
            }
        }
    }
}
