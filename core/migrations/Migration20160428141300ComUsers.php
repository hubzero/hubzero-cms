<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding name parts and usageAgreement columns to users table
  *
**/
class Migration20160428141300ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->hasColumn('#__users', 'activation')
            && $schema->hasColumn('#__xprofiles', 'emailConfirmed')
        ) {
            $schema->modifyColumn('#__users', 'activation')->integer()->notNull()->default(0)->execute();

            // Update activation from xprofiles
            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->setColumn('u.activation', 'x.emailConfirmed')
                ->execute();
        }

        if (
            !$schema->hasColumn('#__users', 'givenName')
            && $schema->hasColumn('#__xprofiles', 'givenName')
        ) {
            $schema->addColumn('#__users', 'givenName')->string()->notNull()->execute();

            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->setColumn('u.givenName', 'x.givenName')
                ->execute();
        }

        if (
            !$schema->hasColumn('#__users', 'middleName')
            && $schema->hasColumn('#__xprofiles', 'middleName')
        ) {
            $schema->addColumn('#__users', 'middleName')->string()->notNull()->execute();

            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->setColumn('u.middleName', 'x.middleName')
                ->execute();
        }

        if (!$schema->hasColumn('#__users', 'surname') && $schema->hasColumn('#__xprofiles', 'surname')) {
            $schema->addColumn('#__users', 'surname')->string()->notNull()->execute();

            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->setColumn('u.surname', 'x.surname')
                ->execute();
        }

        if (
            !$schema->hasColumn('#__users', 'usageAgreement')
            && $schema->hasColumn('#__xprofiles', 'usageAgreement')
        ) {
            $schema->addColumn('#__users', 'usageAgreement')->tinyInteger()->notNull()->default(0)->execute();

            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->setColumn('u.usageAgreement', 'x.usageAgreement')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__users', 'activation')) {
            $schema->modifyColumn('#__users', 'activation')
                ->string(100)
                ->notNull()
                ->execute();
        }

        if ($schema->hasColumn('#__users', 'givenName')) {
            $schema->dropColumn('#__users', 'givenName');
        }

        if ($schema->hasColumn('#__users', 'middleName')) {
            $schema->dropColumn('#__users', 'middleName');
        }

        if ($schema->hasColumn('#__users', 'givenName')) {
            $schema->dropColumn('#__users', 'surname');
        }

        if ($schema->hasColumn('#__users', 'usageAgreement')) {
            $schema->dropColumn('#__users', 'usageAgreement');
        }
    }
}
