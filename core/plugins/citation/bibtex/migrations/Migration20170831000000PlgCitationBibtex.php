<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Citation - Bibtex plugin
 **/
class Migration20170831000000PlgCitationBibtex extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('citation', 'bibtex');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('citation', 'bibtex');
    }
}
