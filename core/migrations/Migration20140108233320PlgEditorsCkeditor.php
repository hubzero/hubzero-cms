<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding ckeditor plugin entry
**/
class Migration20140108233320PlgEditorsCkeditor extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'ckeditor');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'ckeditor');
    }
}
