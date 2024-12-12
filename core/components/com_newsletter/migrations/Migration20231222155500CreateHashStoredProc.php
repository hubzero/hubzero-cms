<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

class Migration20231222155500CreateHashStoredProc extends Base
{

	public static $campaign = '#__campaign';
	public static $config = '#__config';
	public static $users = '#__users';

	public function up()
	{
		$campaign = self::$campaign;
		$config = self::$config;
		$users = self::$users;

                $found = false;

                $this->db->setQuery("SHOW FUNCTION STATUS");
                $results = $this->db->loadObjectList();
                if ($results)
                {
                        foreach ($results as $result)
                        {
                                if ($result->Db == \App::get('config')->get('db') && $result->Name == 'hash_access_code')
                                {
                                        $found = true;
                                        break;
                                }
                        }
                }

		if (!$found)
		{
			$createSP = "CREATE FUNCTION hash_access_code (campaign_id INT(11), user_name VARCHAR(150)) ".
			  "RETURNS CHAR(64) ".
			  "DETERMINISTIC ".
			  "READS SQL DATA ".
			  "BEGIN ".
			  "    SET @cs = (SELECT secret FROM `$campaign` WHERE id=campaign_id); ".
			  "    SET @hs = (SELECT `value` FROM `$config` WHERE `key`='secret' AND `scope`='hub'); ".
			  "    SET @us = (SELECT secret FROM `$users` WHERE username=user_name); ".
			  "RETURN SHA2(CONCAT(@cs,@hs,@us), 256); ".
			  "END;";

			$this->db->setQuery($createSP);
			$this->db->query();
		}
	}

	public function down()
	{

		$dropSP = "DROP FUNCTION IF EXISTS hash_access_code;";

		$this->db->setQuery($dropSP);
		$this->db->query();
	}

}
