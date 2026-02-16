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
 * Migration script for replacing HUBADDRESS references in KB article
 *
*/
class Migration20151026173235ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__faq')) {
            $this->db->getQuery(true)
                ->update('#__faq')
                ->set(['fulltxt' => Expression::replace('fulltxt', 'HUBADDRESS', '{xhub:getcfg hubHostname}')])
                ->execute();
        }

        if ($schema->tableExists('#__kb_articles')) {
            $this->db->getQuery(true)
                ->update('#__kb_articles')
                ->set(['fulltxt' => Expression::replace('fulltxt', 'HUBADDRESS', '{xhub:getcfg hubHostname}')])
                ->execute();
        }
    }
}
