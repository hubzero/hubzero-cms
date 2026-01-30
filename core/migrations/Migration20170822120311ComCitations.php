<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to set context for existing resource citations
 *
*/
class Migration20170822120311ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__citations_assoc') && $this->db->tableHasField('#__citations_assoc', 'type')) {
            $query = "UPDATE `#__citations_assoc` SET `type`='referencedby' WHERE `tbl`='resource' AND (`type`='' "
                . "OR `type` IS NULL)";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists('#__citations_assoc') && $this->db->tableHasField('#__citations_assoc', 'type')) {
            $query = "UPDATE `#__citations_assoc` SET `type`='' WHERE `tbl`='resource' AND `type`='referencedby'";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
