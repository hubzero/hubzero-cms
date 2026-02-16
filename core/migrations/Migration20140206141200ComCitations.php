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
class Migration20140206141200ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__citations', 'format')) {
            $schema->addColumn('#__citations', 'format')
                ->string(11)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__citations', 'format')) {
            $schema->dropColumn('#__citations', 'format');
        }
    }
}
