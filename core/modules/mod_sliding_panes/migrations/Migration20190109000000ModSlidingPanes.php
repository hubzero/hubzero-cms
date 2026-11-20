<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\SlidingPanes\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing sliding_panes module
 *
*/
class Migration20190109000000ModSlidingPanes extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_sliding_panes');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_sliding_panes');
    }
}
