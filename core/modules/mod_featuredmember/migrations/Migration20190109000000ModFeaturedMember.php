<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredmember\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing featuredmember module
 *
*/
class Migration20190109000000ModFeaturedMember extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_featuredmember');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_featuredmember');
    }
}
