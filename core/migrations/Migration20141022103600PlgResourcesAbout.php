<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing abouttool resources plugin and updating references
 **/
class Migration20141022103600PlgResourcesAbout extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_types'))
		{
			// Get all the "mine" queries
			$this->db->setQuery("SELECT id, params FROM `#__resource_types` WHERE `category`=27 AND `params` LIKE '%plg_abouttool=1%'");
			if ($records = $this->db->loadObjectList())
			{
				$path = PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php';
				if (!file_exists($path))
				{
					$path = PATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php';
				}
				include_once($path);

				$tbl = '\\Components\\Resources\\Tables\\Type';
				if (class_exists('ResourcesType'))
				{
					$tbl = 'ResourcesType';
				}

				// Update the query
				foreach ($records as $record)
				{
					$row = new $tbl($this->db);
					$row->bind($record);

					$p = new \Hubzero\Config\Registry($row->params);
					$p->set('plg_about', 1);
					$p->set('plg_abouttool', 0);

					$row->params = $p->toString();
					$row->store();
				}
			}
		}

		$this->deletePluginEntry('resources', 'abouttool');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('resources', 'abouttool');

		if ($this->db->tableExists('#__resource_types'))
		{
			// Get all the "mine" queries
			$this->db->setQuery("SELECT id, params FROM `#__resource_types` WHERE `category`=27 AND `alias`='tools'");
			if ($records = $this->db->loadObjectList())
			{
				$path = PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php';
				if (!file_exists($path))
				{
					$path = PATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php';
				}
				include_once($path);

				$tbl = '\\Components\\Resources\\Tables\\Type';
				if (class_exists('ResourcesType'))
				{
					$tbl = 'ResourcesType';
				}

				// Update the query
				foreach ($records as $record)
				{
					$row = new $tbl($this->db);
					$row->bind($record);

					$p = new \Hubzero\Config\Registry($row->params);
					$p->set('plg_about', 0);
					$p->set('plg_abouttool', 1);

					$row->params = $p->toString();
					$row->store();
				}
			}
		}
	}
}
