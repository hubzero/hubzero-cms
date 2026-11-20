<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\IncrementalRegistration\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing incremental_registration module
 *
*/
class Migration20190109000000ModIncrementalRegistration extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_incremental_registration');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_incremental_registration');
    }
}
