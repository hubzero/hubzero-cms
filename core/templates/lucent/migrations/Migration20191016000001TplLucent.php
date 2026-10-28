<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding entry for Template
 **/
class Migration20191016000001TplLucent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addTemplateEntry('lucent', 'Lucent, the template that inspires', 0, 1, 0, null, 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('lucent', 0);
    }
}
