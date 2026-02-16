<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to move existing antispam plugins if they haven't been already (fixing bad migration)
  *
**/
class Migration20150720105039PlgAntispam extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $params = '';

        if ($schema->tableExists('#__extensions')) {
            // Move the existing plugin entries when possible to preserve params,
            // otherwise add the entry
            foreach (array('akismet', 'mollom', 'spamassassin') as $plg) {
                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('type', '=', 'plugin')
                    ->where('element', '=', $plg);
                $id = $query->value('extension_id');

                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set([
                            'folder' => 'antispam',
                            'name'   => 'plg_antispam_' . $plg
                        ])
                        ->where('extension_id', '=', $id)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            // Move the existing plugin entries when possible to preserve params,
            // otherwise add the entry
            foreach (array('akismet', 'mollom', 'spamassassin') as $plg) {
                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('type', '=', 'plugin')
                    ->where('folder', '=', 'antispam')
                    ->where('element', '=', $plg);
                $id = $query->value('extension_id');

                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set([
                            'folder' => 'content',
                            'name'   => 'plg_content_' . $plg
                        ])
                        ->where('extension_id', '=', $id)
                        ->execute();
                }
            }
        }
    }
}
