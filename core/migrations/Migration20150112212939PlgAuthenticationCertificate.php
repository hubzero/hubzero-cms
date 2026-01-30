<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a plugin entry for the certificate authentication plugin
 *
*/
class Migration20150112212939PlgAuthenticationCertificate extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'certificate', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'certificate');
    }
}
