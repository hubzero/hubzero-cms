<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing a typo in default support message
 *
*/
class Migration20140813132614ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__support_messages')) {
            $oldMessage = "We haven not heard back from you so we will assume that you are no\r\n"
                . "longer experiencing the problem, or that you\'ve worked around it, and we\'ll "
                . "consider the matter closed.  You can reopen the matter at any time by sending "
                . "email or by submitting another problem report on our web site.\r\n\r\n"
                . "Thanks again for your support!\r\n--the {sitename} team";
            $query = 'SELECT id FROM `#__support_messages` WHERE `message`='
                . $this->db->quote($oldMessage);
            $this->db->setQuery($query);
            if ($id = $this->db->loadResult()) {
                $newMessage = "We have not heard back from you so we will assume that you are no "
                    . "longer experiencing the problem, or that you\'ve worked around it, and "
                    . "we\'ll consider the matter closed.  You can reopen the matter at any time "
                    . "by sending email or by submitting another problem report on our web "
                    . "site.\r\n\r\nThanks again for your support!\r\n--the {sitename} team";
                $query = 'UPDATE `#__support_messages` SET `message`='
                    . $this->db->quote($newMessage) . ' WHERE `id`=' . $this->db->quote($id);
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
        // No down.
    }
}
