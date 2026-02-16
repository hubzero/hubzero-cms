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
 * Migration script for expire-session daemon, to record end time for jobs automatically with a timestamp
 *
*/
class Migration20161003151102ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // ADD COLUMN end
        if ($schema->tableExists('joblog') && !$schema->hasColumn('joblog', 'end')) {
            $schema->table('joblog')->alter()
                ->addTimestamp('end')
                ->defaultExpression(Expression::currentTimestamp())
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Drop column end
        if ($schema->tableExists('joblog') && $schema->hasColumn('joblog', 'end')) {
            $schema->dropColumn('joblog', 'end');
        }
    }
}
