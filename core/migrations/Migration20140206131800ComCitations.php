<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Add a column to store formatted citation in citations table
 *
*/
class Migration20140206131800ComCitations extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__citations', 'formatted')) {
            $schema->addColumn('#__citations', 'formatted')->text();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__citations', 'formatted')) {
            $schema->dropColumn('#__citations', 'formatted');
        }
    }
}
