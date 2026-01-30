<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Whatsnew\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Whatsnew module
 *
*/
class Migration20190109000000ModWhatsnew extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addModuleEntry('mod_whatsnew');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_whatsnew');
    }
}
