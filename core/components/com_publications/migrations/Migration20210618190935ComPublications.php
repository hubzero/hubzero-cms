<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_HZEXEC_') or die();

/**
 * Migration script for ...
 **/
class Migration20210618190935ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__publication_authors') && !$this->db->tableHasField('#__publication_authors', 'orgid'))
        {
            $query = "ALTER TABLE `#__publication_authors` ADD COLUMN `orgid` TEXT DEFAULT NULL AFTER `organization`";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists('#__publication_authors') && !$this->db->tableHasField('#__publication_authors', 'orgid'))
        {
            $query = "ALTER TABLE `#__publication_authors` DROP COLUMN `orgid`";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}