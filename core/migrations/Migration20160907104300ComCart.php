<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to change database engine for cart_meta table
 *
*/
class Migration20160907104300ComCart extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__cart_meta')
            && strtolower((string) $schema->getEngine('#__cart_meta')) != 'myisam'
        ) {
            $schema->setTableEngine('#__cart_meta', 'MyISAM');
        }
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down() {}
}
