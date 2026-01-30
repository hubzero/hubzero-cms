<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding feedaggregator component entry
 *
*/
class Migration20140311160400ComFeedaggregator extends Base
{
    public function up()
    {
        $this->addComponentEntry('Feedaggregator');
    }

    public function down()
    {
        $this->deleteComponentEntry('Feedaggregator');
    }
}
