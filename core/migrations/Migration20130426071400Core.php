<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing some links in default content
 **/
class Migration20130426071400Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `introtext` FROM `#__content` WHERE alias='licensing' AND title='Intellectual Property Considerations';";
		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		$result = str_replace('<a href="http://www.hubzero.org/topics/middleware">unique middleware</a>', 'unique middleware', $result);

		$query = "UPDATE `#__content` SET introtext=".$this->db->Quote($result)." WHERE alias='licensing' AND title='Intellectual Property Considerations' LIMIT 1;
					UPDATE `#__content` SET introtext=REPLACE(introtext,'/feedback/report_problems/','/support/ticket/new') WHERE alias='licensing' AND title='Intellectual Property Considerations' LIMIT 1;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}