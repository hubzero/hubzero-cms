<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for migrating old com_sef data into com_redirect
  *
**/
class Migration20150206191525ComRedirect extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__redirection')) {
            $links = $this->db
                ->getQuery()
                ->select('*')
                ->from('#__redirection')
                ->loadObjectList();

            if ($links) {
                foreach ($links as $link) {
                    $exists = $this->db
                        ->getQuery()
                        ->select('id')
                        ->from('#__redirect_links')
                        ->where('old_url', '=', $link->oldurl)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $this->db
                        ->getQuery()
                        ->insert('#__redirect_links')
                        ->set([
                            'old_url'      => $link->oldurl,
                            'new_url'      => $link->newurl,
                            'created_date' => $link->dateadd,
                        ])
                        ->execute();
                }
            }

            $schema->dropTable('#__redirection');
        }

        $this->deleteComponentEntry('com_sef');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__redirection')) {
            $schema
                ->createTable('#__redirection')
                ->id()
                ->integer('cpt')->default(0)
                ->string('oldurl', 100)->default('')
                ->string('newurl', 150)->default('')
                ->date('dateadd')->default('0000-00-00')
                ->index('newurl', 'newurl')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
