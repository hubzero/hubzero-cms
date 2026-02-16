<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to more fix index naming conventions in CNS tables (prev migration also fixed to do these)
 *
*/
class Migration20160907112600Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__wishlist_implementation', 'pagetext');
        $schema->dropIndex('#__tool_version_hostreq', 'idx_tool_version_id_hostreq');
        $schema->dropIndex('#__publication_categories', 'type');
        $schema->dropIndex('#__courses_form_respondents', 'jos_pdf_form_responses_respondent_id_idx');
    }

    public function down()
    {
    }
}
