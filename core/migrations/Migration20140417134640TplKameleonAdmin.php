<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding kameleon template
 *
*/
class Migration20140417134640TplKameleonAdmin extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $styles = array(
            'header' => 'dark',
            'theme'  => 'salmon'
        );

        $this->addTemplateEntry('kameleon', 'kameleon (admin)', 1, 1, 0, $styles);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('kameleon', 1);
    }
}
