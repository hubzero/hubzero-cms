<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Filesystem - AWS S3 plugin
 **/
class Migration20170831000000PlgFilesystemAwss3 extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('filesystem', 'awss3', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('filesystem', 'awss3');
    }
}
