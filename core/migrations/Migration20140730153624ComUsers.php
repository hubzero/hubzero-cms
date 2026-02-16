<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing bad users parameter from older versions of joomla
**/
class Migration20140730153624ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $params    = $this->getParams('com_users');
        $user_type = $params->get('new_usertype');

        if (is_string($user_type) && strlen($user_type) > 2) {
            $id = $this->db->getQuery(true)
                ->select('id')
                ->from('#__usergroups')
                ->where('title', '=', $user_type)
                ->value('id');

            if ($id) {
                $params->set('new_usertype', $id);
                $this->saveParams('com_users', $params);
            } else {
                $this->setError(
                    'Failed to convert new user type paramter of "' . $user_type . '" to an ID.',
                    'warning'
                );
                return false;
            }
        }
    }
}
