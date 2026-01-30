<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        if ($this->db->tableExists('#__menu')) {
            $query = "UPDATE `#__menu` SET `ordering`=0;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
