<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for upping access values to be consistent with #__viewlevels
  *
**/
class Migration20150216140100ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__faq') && $schema->hasColumn('#__faq', 'access')) {
            $exists = $this->db->getQuery(true)
                ->from('#__faq')
                ->where('access', '=', 0)
                ->exists();

            if ($exists) {
                $this->db->getQuery(true)
                    ->update('#__faq')
                    ->set('access = access + 1')
                    ->execute();
            }
        }

        if ($schema->tableExists('#__faq_categories') && $schema->hasColumn('#__faq_categories', 'access')) {
            $exists = $this->db->getQuery(true)
                ->from('#__faq_categories')
                ->where('access', '=', 0)
                ->exists();

            if ($exists) {
                $this->db->getQuery(true)
                    ->update('#__faq_categories')
                    ->set('access = access + 1')
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__faq') && $schema->hasColumn('#__faq', 'access')) {
            $exists = $this->db->getQuery(true)
                ->from('#__faq')
                ->where('access', '=', 0)
                ->exists();

            if (!$exists) {
                $this->db->getQuery(true)
                    ->update('#__faq')
                    ->set('access = access - 1')
                    ->execute();
            }
        }

        if ($schema->tableExists('#__faq_categories') && $schema->hasColumn('#__faq_categories', 'access')) {
            $exists = $this->db->getQuery(true)
                ->from('#__faq_categories')
                ->where('access', '=', 0)
                ->exists();

            if (!$exists) {
                $this->db->getQuery(true)
                    ->update('#__faq_categories')
                    ->set('access = access - 1')
                    ->execute();
            }
        }
    }
}
