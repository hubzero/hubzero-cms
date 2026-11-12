<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
  *
**/
class Migration20220624143229PlgAuthenticationShibboleth extends Base
{
    /**
    * Up
    *
    * @return  void
    **/
    public function up()
    {
        $this->db->schema()->modifyColumn('#__extensions', 'params')->longText()->notNull();
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        // Deliberately left as it is. Shrinking #__extensions.params back to
        // TEXT would clip any extension's params that have outgrown it, leaving
        // them unparseable and the administrator screens broken (2.4 disabled
        // the statement for the same reason).
    }
}
