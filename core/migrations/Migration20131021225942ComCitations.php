<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema
            ->modifyColumn('#__citations', 'volume')
            ->string(11)
            ->execute();
        $schema
            ->modifyColumn('#__citations', 'year')
            ->string(4)
            ->execute();
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->modifyColumn('#__citations', 'volume')->integer(11);
        $schema->modifyColumn('#__citations', 'year')->integer(4);
    }
}
