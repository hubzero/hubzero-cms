<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating closed timestamp on support tickets
 *
*/
class Migration20150122165523ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__support_tickets')) {
            $query = "UPDATE `#__support_tickets` AS t SET t.`closed`=(SELECT c.created FROM `#__support_comments` "
                . "AS c WHERE c.ticket=t.id ORDER BY c.created DESC LIMIT 1) WHERE t.`closed`='0000-00-00 "
                . "00:00:00' AND t.`open`=0";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
