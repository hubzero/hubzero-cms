<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding params field to asset groups
 **/
class Migration20140131091600HubzeroComments extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__comments'))
		{
			$query = "SELECT * FROM `#__comments`";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if ($results && count($results) > 0)
			{
				$parents = array();
				foreach ($results as $r)
				{
					if (substr($r->category, -7) != 'comment')
					{
						$parents[$r->id] = array(
							'referenceid' => $r->referenceid,
							'category'    => $r->category,
							'id'          => 0
						);
					}
				}

				$cls = 'Hubzero_Item_Comment';
				if (class_exists('\\Hubzero\\Item\\Comment'))
				{
					$cls = '\\Hubzero\\Item\\Comment';
				}

				foreach ($results as $r)
				{
					$record = new $cls($db);
					if (substr($r->category, -7) == 'comment')
					{
						if (isset($parents[$r->referenceid]))
						{
							$record->parent    = $parents[$r->referenceid]['id'];
							$record->item_id   = $parents[$r->referenceid]['referenceid'];
							$record->item_type = $parents[$r->referenceid]['category'];
						}
					}
					else
					{
						$record->item_id = $r->referenceid;
						$record->item_type = $r->category;
					}

					$record->content    = $r->comment;
					$record->created    = $r->added;
					$record->created_by = $r->added_by;
					$record->state      = $r->state;
					$record->anonymous  = $r->anonymous;
					$record->notify     = $r->email;
					$record->store();

					if (substr($r->category, -7) != 'comment' && isset($parents[$r->id]))
					{
						$parents[$r->id] = array(
							'referenceid' => $r->referenceid,
							'category'    => $r->category,
							'id'          => $record->id
						);
					}
				}
			}

			$query = "DROP TABLE IF EXISTS `#__comments`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if (!$db->tableExists('#__comments'))
		{
			$query = "CREATE TABLE `#__comments` (
				  `filter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `title` varchar(255) NOT NULL,
				  `alias` varchar(255) NOT NULL,
				  `state` tinyint(1) NOT NULL DEFAULT '1',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(10) unsigned NOT NULL,
				  `created_by_alias` varchar(255) NOT NULL,
				  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
				  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
				  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `map_count` int(10) unsigned NOT NULL DEFAULT '0',
				  `data` text NOT NULL,
				  `params` mediumtext,
				  PRIMARY KEY (`filter_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$db->setQuery($query);
			$db->query();
		}
	}
}