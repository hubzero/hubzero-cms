<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to more fix index naming conventions in CNS tables (prev migration also fixed to do these)
 **/
class Migration2016090711260000Core extends Base
{
	private function dropIndex($table,$key)
	{
                if ($this->db->tableHasKey($table,$key))
                {
                        $query = "DROP INDEX `" . $key . "` ON `" . $table . "`;";
			$this->db->setQuery($query);
			$this->db->query();
                }
	}

	public function up()
	{
		$this->dropIndex('#__wishlist_implementation','pagetext');
		$this->dropIndex('#__tool_version_hostreq','idx_tool_version_id_hostreq');
		$this->dropIndex('#__publication_categories', 'type');
		$this->dropIndex('#__courses_form_respondents','jos_pdf_form_responses_respondent_id_idx');
	}

	public function down()
	{
	}
}
