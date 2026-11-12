<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding feedaggregator entry in disabled state
 *
*/
class Migration20140417132300ComFeedaggregator extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $this->deleteComponentEntry('feedaggregator');
        $this->addComponentEntry('feedaggregator', null, 1, '', false);
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $this->deleteComponentEntry('feedaggregator');
    }
}
