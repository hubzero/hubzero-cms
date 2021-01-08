<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating Collections params.
 **/
class Migration20210105000001ComCollections extends Base
{
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			// Get the current params
			$params = Component::params('com_collections');
			$allow_comments = $params->get('allow_comments');

			// If the comments param is not set, set it to 1-Yes
			if (!isset($allow_comments))
			{
				$params->set('allow_comments', 1);
				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `name`= 'com_collections' AND `element` = 'com_collections'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	public function down()
	{
		// No need to do anything, just leave the unused param.
	}
}
