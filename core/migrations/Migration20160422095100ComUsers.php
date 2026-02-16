<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding 'access' and 'registerIP' columns to users table
 *
*/
class Migration20160422095100ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__users', 'access')) {
            $schema->addColumn('#__users', 'access')->integer(10)->notNull()->default(0);

            if ($schema->tableExists('#__xprofiles')) {
                $public  = 1;
                $private = 5;

                if ($schema->tableExists('#__viewlevels')) {
                    $levels = $this->db->getQuery(true)
                        ->select('*')
                        ->from('#__viewlevels')
                        ->order('ordering', 'ASC')
                        ->loadObjectList();

                    if ($levels) {
                        foreach ($levels as $level) {
                            if ($level->title == 'Public') {
                                $public = $level->id;
                            }

                            if ($level->title == 'Private') {
                                $private = $level->id;
                            }
                        }
                    }
                }

                $this->db->getQuery(true)
                    ->update('#__users')
                    ->leftJoin('#__xprofiles', 'u.id', 'x.uidNumber')
                    ->set(['u.access' => $public])
                    ->where('x.public', '=', 1)
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__users')
                    ->leftJoin('#__xprofiles', 'u.id', 'x.uidNumber')
                    ->set(['u.access' => $private])
                    ->where('x.public', '=', 0)
                    ->execute();
            }
        }

        if (!$schema->hasColumn('#__users', 'registerIP')) {
            $schema->addColumn('#__users', 'registerIP')
                ->string(40)
                ->notNull()
                ->default('')
                ->execute();

            if ($schema->tableExists('#__xprofiles') && $schema->hasColumn('#__xprofiles', 'regIP')) {
                $this->db->getQuery(true)
                    ->update('#__users')
                    ->leftJoin('#__xprofiles', 'u.id', 'x.uidNumber')
                    ->setColumn('u.registerIP', 'x.regIP')
                    ->execute();
            }
        }

        if (
            $schema->hasColumn('#__users', 'sendEmail')
            && $schema->hasColumn('#__xprofiles', 'mailPreferenceOption')
        ) {
            $this->db->getQuery(true)
                ->update('#__users')
                ->leftJoin('#__xprofiles', 'u.id', 'x.uidNumber')
                ->setColumn('u.sendEmail', 'x.mailPreferenceOption')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__users', 'access')) {
            $schema->dropColumn('#__users', 'access');
        }

        if ($schema->hasColumn('#__users', 'registerIP')) {
            $schema->dropColumn('#__users', 'registerIP');
        }
    }
}
