<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing citation field data type
 *
*/
class Migration20131021225942ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->modifyColumn('#__citations', 'volume')
            ->string(11)
            ->execute();
        $schema->modifyColumn('#__citations', 'year')
            ->string(4)
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->modifyColumn('#__citations', 'volume')->integer(11);
        $schema->modifyColumn('#__citations', 'year')->integer(4);
    }
}
