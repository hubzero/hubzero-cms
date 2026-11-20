<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredquestion\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing featuredquestion module
 *
*/
class Migration20190109000000ModFeaturedQuestion extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_featuredquestion');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_featuredquestion');
    }
}
