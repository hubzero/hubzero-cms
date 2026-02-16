<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating kb article text
  *
**/
class Migration20140904174546ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $results = $this->db->getQuery(true)
            ->select('*')
            ->from('#__faq')
            ->whereIn('alias', ['login2', 'pwreset'])
            ->loadObjectList();

        if ($results && count($results) > 0) {
            foreach ($results as $result) {
                $result->fulltxt = str_replace('/lostpassword', '/login/reset', $result->fulltxt);
                $result->fulltxt = str_replace('/change_password', '/members/myaccount/account', $result->fulltxt);
                $this->db->queryBuilder()->alterObject('#__faq', $result, 'id');
            }
        }
    }
}
