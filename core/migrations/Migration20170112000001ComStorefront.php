<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add #__cart_downloads table
  *
**/
class Migration20170112000001ComStorefront extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__storefront_serials', 'srNumber')) {
            $schema->modifyColumn('#__storefront_serials', 'srNumber')->string(255)->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__storefront_serials', 'srNumber')) {
            $schema
                ->modifyColumn('#__storefront_serials', 'srNumber')
                ->string(32)
                ->execute();
        }
    }
}
