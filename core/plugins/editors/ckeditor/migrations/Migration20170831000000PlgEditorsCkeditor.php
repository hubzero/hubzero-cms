<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Editors\Ckeditor\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Editors - CKeditor plugin
 **/
class Migration20170831000000PlgEditorsCkeditor extends Base
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
