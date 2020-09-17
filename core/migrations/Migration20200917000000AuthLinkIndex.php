<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration202000171000000AuthLinkIndex extends Base
{
    public function up() {
        if ($this->db->tableExists('#__auth_link')) {
            $query = "ALTER TABLE `#__auth_link` ADD INDEX auth_domain_id_idx (auth_domain_id);";
            $this->db->setQuery($query);
            $this->db->query();

            $query = "ALTER TABLE `#__auth_link` ADD INDEX user_id_idx (user_id);";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    public function down() {
        if ($this->db->tableExists('#__auth_link')) {
            $query = "ALTER TABLE `#__auth_link` DROP INDEX auth_domain_id_idx;";
            $this->db->setQuery($query);
            $this->db->query();

            $query = "ALTER TABLE `#__auth_link` DROP INDEX user_id_idx;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

}
