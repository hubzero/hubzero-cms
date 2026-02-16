<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding citations sponsers
 *
*/
class Migration20130404000000ComCitations extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__citations_sponsors', 'image')) {
            $schema->addColumn('#__citations_sponsors', 'image')
                ->string(200)
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__citations_sponsors', 'image')) {
            $schema->dropColumn('#__citations_sponsors', 'image');
        }
    }
}
