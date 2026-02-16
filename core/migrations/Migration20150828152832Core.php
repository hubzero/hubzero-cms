<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for removing embedded default passwords and excess escaping
  *
**/
class Migration20150828152832Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $query = $this->db->getQuery(true); // Helper variable for reuse

            $query->update('#__extensions')
                ->set([
                    'manifest_cache' => Expression::replace(
                        'manifest_cache',
                        '2013 Open Source Matters',
                        '2014 Open Source Matters'
                    ),
                ])
                ->where('extension_id', '<', 10000)
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', '_HUB0_nW_', '')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_system')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', 'hubzero_network', '')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_system')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', 'hubzero.org', '')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_system')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set([
                    'params' => Expression::replace(
                        'params',
                        'ABQIAAAAPq8QOefNUw20Lc6RX2gKqhQkcPnh--THxGDMaCLza-8u_rvH7hQmdZgwooOYuoIkEqFAtrnkoY4ElA',
                        ''
                    ),
                ])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_usage')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['manifest_cache' => ''])
                ->where('type', '=', 'file')
                ->where('element', '=', 'joomla')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', ':10,', ':"10",')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_media')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', 'site\\/media', 'site/media')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_media')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', 'media\\/images', 'media/images')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_media')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', 'image\\/', 'image/')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_media')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', 'application\\/', 'application/')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_media')
                ->execute();

            $query = $this->db->getQuery(true);
            $query->update('#__extensions')
                ->set(['params' => Expression::replace('params', 'text\\/', 'text/')])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_media')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
    }
}
