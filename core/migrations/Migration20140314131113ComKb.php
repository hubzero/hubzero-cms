<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding faq state field
 *
*/
class Migration20140314131113ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__faq_comments', 'state')) {
            $schema->addColumn('#__faq_comments', 'state')->tinyInteger(2)->notNull()->default(0);

            $this->db->getQuery(true)
                ->update('#__faq_comments')
                ->set(['state' => 1])
                ->where('state', '=', '0')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__faq_comments', 'state')) {
            $schema->dropColumn('#__faq_comments', 'state');
        }
    }
}
