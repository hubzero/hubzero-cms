<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for converting joomla upload max units
 *
 */
class Migration20131021211223Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $result = $this->db->getQuery(true)
                ->select(['extension_id', 'params'])
                ->from('#__extensions')
                ->where('element', '=', 'com_media')
                ->first();

            if ($result) {
                $params = json_decode($result->params);

                if ($params->upload_maxsize > 1000000) {
                    $params->upload_maxsize = $params->upload_maxsize / 1000000;

                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['params' => json_encode($params)])
                        ->where('extension_id', '=', $result->extension_id)
                        ->execute();
                }
            }
        }
    }
}
