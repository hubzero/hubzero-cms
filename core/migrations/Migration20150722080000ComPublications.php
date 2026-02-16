<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'delivered' and 'digest' columns to  table #__item_watch
**/
class Migration20150722080000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__item_watch') && !$schema->hasColumn('#__item_watch', 'digest')) {
            /* 0 = on-action delivery, 1 = daily digest, 2 = weekly digest, 3 = monthly digest */
            $schema->addColumn('#__item_watch', 'digest')->integer()->notNull()->default(0)->after('state')->execute();
        }

        if ($schema->tableExists('#__item_watch') && !$schema->hasColumn('#__item_watch', 'delivered')) {
            $schema->addColumn('#__item_watch', 'delivered')->datetime()->nullable()->after('digest')->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__item_watch') && $schema->hasColumn('#__item_watch', 'digest')) {
            $schema->dropColumn('#__item_watch', 'digest');
        }

        if ($schema->tableExists('#__item_watch') && $schema->hasColumn('#__item_watch', 'delivered')) {
            $schema->dropColumn('#__item_watch', 'delivered');
        }
    }
}
