<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a state field to resource reviews
 *
 */
class Migration20140627140011PlgResourcesReviews extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__resource_ratings', 'state')) {
            $schema->addColumn('#__resource_ratings', 'state')->tinyInteger(2)->notNull()->default(0);

            $this->db->getQuery(true)
                ->update('#__resource_ratings')
                ->set(['state' => 1])
                ->where('resource_id', '!=', 0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__resource_ratings', 'state')) {
            $schema->dropColumn('#__resource_ratings', 'state');
        }
    }
}
