<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating custom footer module links to point to com_users, rather than com_user
**/
class Migration20131209221353ComUsers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $query = $this->db
            ->getQuery()
            ->select(['id', 'content'])
            ->from('#__modules')
            ->where('position', '=', 'footer')
            ->where('module', '=', 'mod_custom');
        $results = $query->loadObjectList();

        if ($results) {
            foreach ($results as $r) {
                $look_for     = ['/user/remind', '/user/reset'];
                $replace_with = ['/users/remind', '/users/reset'];
                $new_content  = str_replace($look_for, $replace_with, $r->content);

                if ($new_content != $r->content) {
                    $this->db
                        ->getQuery()
                        ->update('#__modules')
                        ->set(['content' => $new_content])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }
        }
    }
}
