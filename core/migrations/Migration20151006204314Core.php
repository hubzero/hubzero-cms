<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing scope of custom migrations
 *
*/
class Migration20151006204314Core extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (is_dir(PATH_APP . DS . 'migrations') && $schema->tableExists('#__migrations')) {
            // Get any migrations that have been moved to app
            $exclude = [".", "..", "index.html"];
            $files   = array_diff(scandir(PATH_APP . DS . 'migrations'), $exclude);

            if ($files) {
                foreach ($files as $file) {
                    $this->db
                        ->getQuery()
                        ->update('#__migrations')
                        ->set(['scope' => 'app/migrations'])
                        ->where('file', '=', $file)
                        ->execute();
                }
            }
        }
    }
}
