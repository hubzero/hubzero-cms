<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding component entry for com_citations
 **/
class Migration20170831000000ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('citations');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('citations');
    }
}
