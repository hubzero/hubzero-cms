<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for defaulting citations field to 0 rather than null
  *
**/
class Migration20131031124923ComCitations extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->db
            ->schema()
            ->modifyColumn('#__citations', 'affiliated')
            ->integer()
            ->notNull()
            ->default(0)
            ->execute();
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->db->schema()->modifyColumn('#__citations', 'affiliated')->integer()->nullable()->execute();
    }
}
