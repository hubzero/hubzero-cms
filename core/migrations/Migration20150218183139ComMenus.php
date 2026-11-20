<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for re-setting deprecated 'ordering' field on menu table
  *
**/
class Migration20150218183139ComMenus extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__menu')) {
            $this->db
                ->getQuery()
                ->update('#__menu')
                ->set(['ordering' => 0])
                ->execute();
        }
    }
}
