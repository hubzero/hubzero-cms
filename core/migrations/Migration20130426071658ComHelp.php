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
 * Migration script for adding help component
  *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20130426071658ComHelp extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('Help');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('Help');
    }
}
