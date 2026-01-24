<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * Migration script for adding Editors - Tinymce plugin
 **/
class Migration20170831000000PlgEditorsTinymce extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'tinymce', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'tinymce');
    }
}
