<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for incorrect link in default footer content
 *
*/
class Migration20190327000000Footer extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__modules')) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['content' => Expression::replace('content', '/about/dmcapolicy', '/aboutus/dmcapolicy')])
                ->where('title', '=', 'Hub Footer')
                ->where('module', '=', 'mod_custom')
                ->whereLike('content', '/about/dmcapolicy')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__modules')) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['content' => Expression::replace('content', '/aboutus/dmcapolicy', '/about/dmcapolicy')])
                ->where('title', '=', 'Hub Footer')
                ->where('module', '=', 'mod_custom')
                ->whereLike('content', '/aboutus/dmcapolicy')
                ->execute();
        }
    }
}
