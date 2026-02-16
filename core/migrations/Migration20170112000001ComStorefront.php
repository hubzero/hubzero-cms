<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__storefront_serials', 'srNumber')) {
            $schema->modifyColumn('#__storefront_serials', 'srNumber')
                ->string(32)
                ->execute();
        }
    }
}
