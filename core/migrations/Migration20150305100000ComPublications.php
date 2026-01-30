<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding master_doi field to #__publications
  *
**/
class Migration20150305100000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__publications')) {
            if (!$this->db->tableHasField('#__publications', 'master_doi')) {
                $query = "ALTER TABLE `#__publications` ADD COLUMN master_doi varchar(255) DEFAULT '';";
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
        if ($this->db->tableExists('#__publications')) {
            if ($this->db->tableHasField('#__publications', 'master_doi')) {
                $query = "ALTER TABLE `#__publications` DROP `master_doi`;";
                $this->db->setQuery($query);
                $this->db->query();
            }
        }
    }
}
