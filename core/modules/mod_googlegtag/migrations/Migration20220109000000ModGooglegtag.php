<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
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
