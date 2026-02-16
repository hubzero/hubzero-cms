<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting com_wrapper
 *
 */
class Migration20140110125436ComWrapper extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_wrapper')
            ->value('extension_id');

        if ($id) {
            $this->deleteComponentEntry('wrapper');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_wrapper')
            ->value('extension_id');

        if (!$id) {
            $this->addComponentEntry('wrapper');
        }
    }
}
