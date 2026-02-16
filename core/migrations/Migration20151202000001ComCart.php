<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding projects tables to support filesystem connections
  *
**/
class Migration20151202000001ComCart extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cart_downloads') && !$schema->hasColumn('#__cart_downloads', 'dIp')) {
            $schema->addColumn('#__cart_downloads', 'dIp')->integer()->unsigned()->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__cart_downloads') && $schema->hasColumn('#__cart_downloads', 'dIp')) {
            $schema->dropColumn('#__cart_downloads', 'dIp');
        }
    }
}
