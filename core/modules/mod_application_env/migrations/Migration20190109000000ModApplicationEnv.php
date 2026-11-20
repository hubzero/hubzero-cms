<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ApplicationEnv\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Application Env module
 *
*/
class Migration20190109000000ModApplicationEnv extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_application_env');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_application_env');
    }
}
