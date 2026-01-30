<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_search
 **/
class Migration20170831000000ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('search', null, 1, '', false);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('search');
    }
}
