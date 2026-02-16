<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding anonymous flag to xmessage table
 *
*/
class Migration20170208180603PlgXmessageHandler extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage') && !$schema->hasColumn('#__xmessage', 'anonymous')) {
            $schema->addColumn('#__xmessage', 'anonymous')->tinyInteger(2)->notNull()->default('0');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xmessage') && $schema->hasColumn('#__xmessage', 'anonymous')) {
            $schema->dropColumn('#__xmessage', 'anonymous');
        }
    }
}
