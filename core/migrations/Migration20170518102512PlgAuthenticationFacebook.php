<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving registerDate data from #__xprofiles to #__users
 *
*/
class Migration20170518102512PlgAuthenticationFacebook extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions') && $schema->hasColumn('#__extensions', 'params')) {
            $params = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('element', '=', 'facebook')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'authentication')
                ->value('params');

            if (!empty($params)) {
                $params = (array) json_decode($params);
                $params['graph_version'] = 'v2.9';

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => json_encode($params)])
                    ->where('element', '=', 'facebook')
                    ->where('type', '=', 'plugin')
                    ->where('folder', '=', 'authentication')
                    ->execute();
            }
        }
    }
}
