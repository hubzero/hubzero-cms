<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Tools (Windows) resource type
 **/
class Migration20160321145900ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');

			$query = "SELECT id FROM `#__resource_types` WHERE alias=" . $this->db->quote('windowstools');
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$row = new \Components\Resources\Tables\Type($this->db);
				$row->alias = 'windowstools';
				$row->type = 'Tools (Windows)';
				$row->category = 27;
				$row->description = '<p>A simulation tool is software that allows users to run a specific type of calculation. These are (MS) Windows-based.</p>';
				$row->customFields = '{"fields":[{"default":"","name":"credits","label":"Credits","type":"textarea","required":"0"},{"default":"","name":"sponsoredby","label":"Sponsors","type":"textarea","required":"0"},{"default":"","name":"references","label":"References","type":"textarea","required":"0"}]}';
				$row->contributable = 0;
				$row->params = '{"plg_about":"1","plg_citations":"0","plg_findthistext":"0","plg_groups":"1","plg_questions":"1","plg_related":"0","plg_reviews":"1","plg_share":"1","plg_sponsors":"1","plg_supportingdocs":"0","plg_usage":"0","plg_versions":"0","plg_wishlist":"1"}';

				if ($row->check())
				{
					$row->store();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');

			$query = "SELECT id FROM `#__resource_types` WHERE alias=" . $this->db->quote('windowstools');
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if ($id)
			{
				$row = new \Components\Resources\Tables\Type($this->db);
				$row->delete($id);
			}
		}
	}
}