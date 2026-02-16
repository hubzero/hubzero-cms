<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing up some support ticket field data types
 *
*/
class Migration20141009204207ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'status')
            ->tinyInteger(3)
            ->notNull()
            ->default(0)
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'created')
            ->datetime()
            ->notNull()
            ->default('0000-00-00 00:00:00')
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'login')
            ->string(200)
            ->notNull()
            ->default('')
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'severity')
            ->string(30)
            ->notNull()
            ->default('')
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'category')
            ->string(50)
            ->notNull()
            ->default('')
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'summary')
            ->string(250)
            ->notNull()
            ->default('')
            ->execute();
        $this->db->schema()->modifyColumn('#__support_tickets', 'report')->text()->notNull()->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'resolved')
            ->string(50)
            ->notNull()
            ->default('')
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'email')
            ->string(200)
            ->notNull()
            ->default('')
            ->execute();
        $this->db->schema()->modifyColumn('#__support_tickets', 'name')->string(200)->notNull()->default('')->execute();
        $this->db->schema()->modifyColumn('#__support_tickets', 'os')->string(50)->notNull()->default('')->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'browser')
            ->string(50)
            ->notNull()
            ->default('')
            ->execute();
        $this->db->schema()->modifyColumn('#__support_tickets', 'ip')->string(200)->notNull()->default('')->execute();
        $this->db->schema()->modifyColumn('#__support_tickets', 'uas')->string(250)->notNull()->default('')->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'hostname')
            ->string(200)
            ->notNull()
            ->default('')
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'referrer')
            ->string(250)
            ->notNull()
            ->default('')
            ->execute();
        $this->db
            ->schema()
            ->modifyColumn('#__support_tickets', 'group')
            ->string(250)
            ->notNull()
            ->default('')
            ->execute();
    }
}
