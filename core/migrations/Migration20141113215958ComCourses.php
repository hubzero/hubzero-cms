<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for purging courses form start/end times from form deployments table
 *
*/
class Migration20141113215958ComCourses extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_form_deployments')) {
            $this->db
                ->getQuery()
                ->update('#__courses_form_deployments')
                ->set(['start_time' => null, 'end_time' => null])
                ->execute();
        }
    }
}
