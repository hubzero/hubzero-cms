<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authfactors\Certificate\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authfactors - Certificate plugin
 **/
class Migration20170831000000PlgAuthfactorsCertificate extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('authfactors', 'certificate', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('authfactors', 'certificate');
    }
}
