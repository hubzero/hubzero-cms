<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for feedback image update based on asset relocation
**/
class Migration20130516175532ComFeedback extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $oldPath = '/components/com_feedback/images/contributor.gif';
        $newPath = '/components/com_feedback/assets/img/contributor.gif';

        if ($schema->tableExists('#__components')) {
            $this->db->getQuery(true)
                ->update('#__components')
                ->set(['params' => Expression::replace('params', $oldPath, $newPath)])
                ->where('option', '=', 'com_feedback')
                ->execute();
        } else {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => Expression::replace('params', $oldPath, $newPath)])
                ->where('element', '=', 'com_feedback')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $oldPath = '/components/com_feedback/assets/img/contributor.gif';
        $newPath = '/components/com_feedback/images/contributor.gif';

        if ($schema->tableExists('#__components')) {
            $this->db->getQuery(true)
                ->update('#__components')
                ->set(['params' => Expression::replace('params', $oldPath, $newPath)])
                ->where('option', '=', 'com_feedback')
                ->execute();
        } else {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => Expression::replace('params', $oldPath, $newPath)])
                ->where('element', '=', 'com_feedback')
                ->execute();
        }
    }
}
