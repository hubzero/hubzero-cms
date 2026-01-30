<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for feedback image update based on asset relocation
**/
class Migration20130516175532ComFeedback extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $oldPath = '/components/com_feedback/images/contributor.gif';
        $newPath = '/components/com_feedback/assets/img/contributor.gif';

        if ($this->db->tableExists('#__components')) {
            $query = "UPDATE `#__components` SET `params` ="
                . "REPLACE(`params`," . $this->db->quote($oldPath) . "," . $this->db->quote($newPath) . ")"
                . "WHERE `option` = 'com_feedback';";
        } else {
            $query = "UPDATE `#__extensions` SET `params` ="
                . "REPLACE(`params`," . $this->db->quote($oldPath) . "," . $this->db->quote($newPath) . ")"
                . "WHERE `element` = 'com_feedback';";
        }

        if (!empty($query)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $oldPath = '/components/com_feedback/assets/img/contributor.gif';
        $newPath = '/components/com_feedback/images/contributor.gif';

        if ($this->db->tableExists('#__components')) {
            $query = "UPDATE `#__components` SET `params` ="
                . "REPLACE(`params`," . $this->db->quote($oldPath) . "," . $this->db->quote($newPath) . ")"
                . "WHERE `option` = 'com_feedback';";
        } else {
            $query = "UPDATE `#__extensions` SET `params` ="
                . "REPLACE(`params`," . $this->db->quote($oldPath) . "," . $this->db->quote($newPath) . ")"
                . "WHERE `element` = 'com_feedback';";
        }

        if (!empty($query)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
