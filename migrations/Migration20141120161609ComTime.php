<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing time to track start and end times of entries
 **/
class Migration20141120161609ComTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__time_records') && $this->db->tableHasField('#__time_records', 'date'))
		{
			$cols = $this->db->getTableColumns('#__time_records', false);

			if ($cols['date']->Type == 'date')
			{
				$query = "ALTER TABLE `#__time_records` CHANGE `date` `date` DATETIME NOT NULL";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "SELECT `id`, `date` FROM `#__time_records`";
				$this->db->setQuery($query);
				$results = $this->db->loadObjectList();

				if ($results && count($results) > 0)
				{
					$total = count($results);
					$i     = 0;
					$this->callback('progress', 'init', array('Updating existing start dates to include time:'));

					foreach ($results as $result)
					{
						$date  = \JFactory::getDate($result->date, \JFactory::getConfig()->get('offset'))->toSql();
						$query = "UPDATE `#__time_records` SET `date` = " . $this->db->quote($date) . " WHERE `id` = " . (int)$result->id;
						$this->db->setQuery($query);
						$this->db->query();

						$i++;
						$progress = round($i/$total*100);
						$this->callback('progress', 'setProgress', array($progress));
					}

					$this->callback('progress', 'done');
				}
			}
		}

		if ($this->db->tableExists('#__time_records')
			&& $this->db->tableHasField('#__time_records', 'date')
			&& !$this->db->tableHasField('#__time_records', 'end'))
		{
			$query = "ALTER TABLE `#__time_records` ADD COLUMN `end` DATETIME NOT NULL AFTER `date`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT `id`, `date`, `time` FROM `#__time_records`";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if ($results && count($results) > 0)
			{
				$total = count($results);
				$i     = 0;
				$this->callback('progress', 'init', array('Adding end dates based on record duration:'));

				foreach ($results as $result)
				{
					// Using PHP date because we're just doing a relative calculation
					$date  = date('Y-m-d H:i:s', strtotime($result->date) + $result->time*3600);
					$query = "UPDATE `#__time_records` SET `end` = " . $this->db->quote($date) . " WHERE `id` = " . (int)$result->id;
					$this->db->setQuery($query);
					$this->db->query();

					$i++;
					$progress = round($i/$total*100);
					$this->callback('progress', 'setProgress', array($progress));
				}

				$this->callback('progress', 'done');
			}
		}
	}
}