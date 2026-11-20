<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing old mod_status module.
 *
*/
class Migration20140806144415ModStatus extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deleteModuleEntry('mod_status');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addModuleEntry('mod_status', 1, '', 1);
    }
}
