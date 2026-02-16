<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding venue_id to host table
 *
*/
class Migration20130214000000Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('host', 'venue_id')) {
            $schema->addColumn('host', 'venue_id')->integer(11)->after('portbase');
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('host', 'venue_id')) {
            $schema->dropColumn('host', 'venue_id');
        }
    }
}
