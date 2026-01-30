<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Hubzero\Autocompleter\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Hubzero - Autocompleter plugin
 **/
class Migration20170831000000PlgHubzeroAutocompleter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('hubzero', 'autocompleter');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('hubzero', 'autocompleter');
    }
}
