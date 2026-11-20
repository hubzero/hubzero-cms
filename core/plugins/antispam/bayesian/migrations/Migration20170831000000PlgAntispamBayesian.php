<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Bayesian\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Antispam - Bayesian plugin
 **/
class Migration20170831000000PlgAntispamBayesian extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'bayesian');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'bayesian');
    }
}
