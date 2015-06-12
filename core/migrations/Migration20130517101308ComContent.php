<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for getting rid of show_pdf_icon in extension params
 **/
class Migration20130517101308ComContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__components'))
		{
			$query  = "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=\n','') WHERE `option` = 'com_content';";
			$query .= "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=0\n','') WHERE `option` = 'com_content';";
			$query .= "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=1\n','') WHERE `option` = 'com_content';";
			$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=\n','') WHERE `link` LIKE '%com_content%';";
			$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=0\n','') WHERE `link` LIKE '%com_content%';";
			$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=1\n','') WHERE `link` LIKE '%com_content%';";

			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			$query = "SELECT `extension_id`, `params` from `#__extensions` WHERE `element` = 'com_content'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$params = json_decode($r->params);
					unset($params->show_pdf_icon);

					$query = "UPDATE `#__extensions` SET `params` = " . $this->db->quote(json_encode($params)) . " WHERE `extension_id` = " . $this->db->quote($r->extension_id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			$query = "SELECT `id`, `params` from `#__menu` WHERE `link` LIKE '%com_content%'";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$params = json_decode($r->params);
					unset($params->show_pdf_icon);

					$query = "UPDATE `#__menu` SET `params` = " . $this->db->quote(json_encode($params)) . " WHERE `id` = " . $this->db->quote($r->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
