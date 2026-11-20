<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Googlegtag\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing google gtag module
 *
*/
class Migration20220109000000ModGooglegtag extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_googlegtag');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_googlegtag');
    }
}
