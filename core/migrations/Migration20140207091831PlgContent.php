<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding content format plugins
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20140207091831PlgContent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'formatwiki', 1, '{"applyFormat":"1","convertFormat":"0"}');
        $this->addPluginEntry('content', 'formathtml', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'formatwiki');
        $this->deletePluginEntry('content', 'formathtml');
    }
}
