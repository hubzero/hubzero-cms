<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to html entity decode each event title.
 **/
class Migration20140826120656ComEvents extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// select each event
		$query = "SELECT id, title FROM `#__events`";
		$this->db->setQuery($query);
		$events = $this->db->loadObjectList();

		// update each event
		foreach ($events as $event)
		{
			$fixedTitle = html_entity_decode($event->title);
			$query = "UPDATE `#__events` SET `title`=" . $this->db->quote($fixedTitle) . " WHERE `id`=" . $this->db->quote($event->id);
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}