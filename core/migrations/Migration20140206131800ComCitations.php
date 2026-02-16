<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Add a column to store formatted citation in citations table
 *
*/
class Migration20140206131800ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__citations', 'formatted')) {
            $schema->addColumn('#__citations', 'formatted')->text();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__citations', 'formatted')) {
            $schema->dropColumn('#__citations', 'formatted');
        }
    }
}
