<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing unused com_admin component
 *
*/
class Migration20170313151515ComAdmin extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deleteComponentEntry('admin');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addComponentEntry('admin');
    }
}
