<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for disabling courses store plugin for the time being
**/
class Migration20131112134513PlgCoursesStore extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->disablePlugin('courses', 'store');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->enablePlugin('courses', 'store');
    }
}
