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
    **/
    public function up()
    {
        $this->db->schema()->modifyColumn('#__extensions', 'params')->longText()->notNull();
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            if ($schema->hasColumn('#__extensions', 'params')) {
                $schema->modifyColumn('#__extensions', 'params')->text()->notNull();
            }
        }
    }
}
