<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for defaulting the option to Basic search
  *
**/
class Migration20160606162915ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $parameter = Component::params('com_search')->get('engine');
        if ($parameter == 'hubgraph') {
            $result = $this->db->getQuery(true)
                ->select('extension_id, params')
                ->from('#__extensions')
                ->where('name', '=', 'com_search')
                ->loadAssoc();
            if (isset($result)) {
                $parameters = json_decode($result['params']);
                $parameters->engine = 'basic';
                $parameters = json_encode($parameters);

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $parameters])
                    ->where('extension_id', '=', (int)$result['extension_id'])
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // No down method applicable.
    }
}
