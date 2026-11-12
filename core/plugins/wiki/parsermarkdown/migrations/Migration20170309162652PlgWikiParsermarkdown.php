<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parsermarkdown\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Markdown parser for wiki
 **/
class Migration20170309162652PlgWikiParsermarkdown extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('wiki', 'parsermarkdown', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('wiki', 'parsermarkdown');
    }
}
