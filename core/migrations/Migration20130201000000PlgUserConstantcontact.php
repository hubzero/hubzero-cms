<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding constant contact plugin entry
 *
*/
class Migration20130201000000PlgUserConstantcontact extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $this->addPluginEntry('user', 'constantcontact');
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $this->deletePluginEntry('user', 'constantcontact');
    }
}
