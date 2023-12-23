<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20231222155500CreateHashStoredProc extends Base
{

	public function up()
	{
		$createSP = "CREATE FUNCTION hash_access_code (campaign_id INT(11), user_name VARCHAR(150)) ".
                "RETURNS CHAR(64) ".
                "BEGIN ".
                "    SET @cs = (SELECT secret FROM campaign WHERE id=campaign_id); ".
                "    SET @hs = (SELECT secret FROM campaign_hub); ".
                "    SET @us = (SELECT secret FROM jos_users WHERE username=user_name); ".
                "RETURN SHA2(CONCAT(@cs,@hs,@us), 256); ".
                "END;";


		$this->db->setQuery($createSP);
		$this->db->query();
	}

	public function down()
	{

		$dropSP = "DROP FUNCTION IF EXISTS hash_access_code;";

		$this->db->setQuery($dropSP);
		$this->db->query();
	}

}
