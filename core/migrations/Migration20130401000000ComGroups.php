<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting groups userenrollment plugin
  *
**/
class Migration20130401000000ComGroups extends Base
{
    public function up()
    {
        $this->deletePluginEntry('groups', 'userenrollment');
    }
}
