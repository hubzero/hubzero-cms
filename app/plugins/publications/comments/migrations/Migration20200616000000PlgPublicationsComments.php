<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding publication comment message setting
 **/
class Migration20200616000000PlgPublicationsComments extends Base
{
    /**
	 * Up
	 **/
    public function up()
    {
        if ($this->db->tableExists('#__xmessage_component'))
		{
            // Add only if not there already
            $query = "SELECT COUNT(1) FROM `#__xmessage_component` WHERE `action` = 'publications_new_comment'";
            if (!$this->db->setQuery($query)->loadResult()) {
                $query = "INSERT INTO `#__xmessage_component` VALUES (0,'com_publications','publications_new_comment','Someone adds a comment to one of my contributions or replies to my comment.')";
                $this->db->setQuery($query)->query();
                $this->log("Added 'publications_new_comment' as a message setting.");
            } else {
                $this->log("Message setting 'publications_new_comment' already exists!");
            }
		}
    }

	/**
	 * Down
	 **/
    public function down()
    {
        if ($this->db->tableExists('#__xmessage_component'))
        {
            $query = "DELETE FROM `#__xmessage_component` WHERE `action` = 'publications_new_comment'";
            $this->db->setQuery($query)->query();
            $this->log("Removed 'publications_new_comment' as a message setting.");
        }
    }
}