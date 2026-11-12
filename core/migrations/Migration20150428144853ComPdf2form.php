<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing com_pdf2form
 *
*/
class Migration20150428144853ComPdf2form extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deleteComponentEntry('com_pdf2form');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addComponentEntry('com_pdf2form', 'com_pdf2form');
    }
}
