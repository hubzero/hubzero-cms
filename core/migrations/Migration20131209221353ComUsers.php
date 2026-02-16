<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $query = $this->db->getQuery(true)
            ->select(['id', 'content'])
            ->from('#__modules')
            ->where('position', '=', 'footer')
            ->where('module', '=', 'mod_custom');
        $results = $query->loadObjectList();

        if ($results && count($results) > 0) {
            foreach ($results as $r) {
                $look_for     = array('/user/remind', '/user/reset');
                $replace_with = array('/users/remind', '/users/reset');
                $new_content  = str_replace($look_for, $replace_with, $r->content);

                if ($new_content != $r->content) {
                    $this->db->getQuery(true)
                        ->update('#__modules')
                        ->set(['content' => $new_content])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }
        }
    }
}
