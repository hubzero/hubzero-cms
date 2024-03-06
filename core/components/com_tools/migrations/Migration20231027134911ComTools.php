<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing DATETIME fields default to NULL for recent_tools table
 **/
class Migration20231027134911ComTools extends Base
{
        /**
         * Up
         **/
        public function up()
        {
                if (!$mwdb = $this->getMWDBO())
                {
                        $this->setError('Failed to connect to the middleware database', 'warning');
                        return false;
                }

                // ADD COLUMN first_display to table host
                if ($mwdb->tableExists('host') && !$mwdb->tableHasField('host', 'service_host'))
                {
                        $query = "ALTER TABLE host ADD COLUMN service_host varchar(40) DEFAULT NULL AFTER first_display;";
                        $mwdb->setQuery($query);
                        $mwdb->query();
                }
        }

        /**
         * Down
         **/
        public function down()
        {
                if (!$mwdb = $this->getMWDBO())
                {
                        $this->setError('Failed to connect to the middleware database', 'warning');
                        return false;
                }

                // Drop column first_display
                if ($mwdb->tableExists('host') && $mwdb->tableHasField('host', 'service_host'))
                {
                        $query = "ALTER TABLE host DROP COLUMN service_host;";
                        $mwdb->setQuery($query);
                        $mwdb->query();
                }
        }
}
