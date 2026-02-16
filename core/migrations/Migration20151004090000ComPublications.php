<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding citations block and fixing block order in #_publication_blocks
**/
class Migration20151004090000ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__publication_blocks')) {
            $query = $this->db->getQuery(true)
                ->select('block')
                ->from('#__publication_blocks')
                ->where('block', '=', 'citations');

            if ($query->doesntExist()) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__publication_blocks')
                    ->values([
                        'block'    => 'citations',
                        'label'    => 'Citations',
                        'title'    => 'Publication Citations',
                        'status'   => 1,
                        'minimum'  => 1,
                        'maximum'  => 0,
                        'ordering' => 7,
                        'params'   => '',
                        'manifest' => ''
                    ])
                    ->execute();
            }

            $this->db->getQuery(true)
                ->update('#__publication_blocks')
                ->set(['ordering' => 8])
                ->where('block', '=', 'notes')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__publication_blocks')
                ->set(['ordering' => 9])
                ->where('block', '=', 'review')
                ->execute();
        }
    }
}
