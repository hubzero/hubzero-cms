<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * Migration script for adding Editors - CKeditor5 plugin
 **/
class Migration20180504000000PlgEditorsCkeditor5 extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'ckeditor5');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'ckeditor5');
    }
}
