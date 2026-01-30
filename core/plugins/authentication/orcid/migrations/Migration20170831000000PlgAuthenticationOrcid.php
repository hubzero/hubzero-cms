<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Orcid\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Orcid plugin
 **/
class Migration20170831000000PlgAuthenticationOrcid extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'orcid', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'orcid');
    }
}
