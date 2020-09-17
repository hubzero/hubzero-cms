<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration202000171000000AuthDomainIndex extends Base
{
    public function up() {
        if ($this->db->tableExists('#__auth_domain')) {
            $query = "ALTER TABLE `#__auth_domain` ADD INDEX authenticator_idx (authenticator);";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    public function down() {
        if ($this->db->tableExists('#__auth_domain')) {
            $query = "ALTER TABLE `#__auth_domain` INDEX authenticator_idx;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

}
