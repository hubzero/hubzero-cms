<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for getting rid of member favorites plugin
 **/
class Migration20140506104910PlgMembersFavorites extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// add comment ID
		if ($this->db->tableExists('#__xfavorites'))
		{
			$this->callback('progress', 'init', array('Running ' . __CLASS__ . '.php:'));

			// Check if there are any favorites
			$query = "SELECT * FROM `#__xfavorites` ORDER BY uid ASC;";
			$this->db->setQuery($query);
			if ($results = $this->db->loadObjectList())
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collections.php');

				$objs  = array();
				$usrs  = array();
				$total = count($results);
				$i     = 1;

				foreach ($results as $result)
				{
					// Does this user already have this favorite as a collection item?
					$query = "SELECT p.id
							FROM `#__collections_posts` AS p
							JOIN `#__collections_items` AS i ON p.`item_id`=i.`id`
							WHERE p.`created_by`=" . $this->db->quote($result->uid) . " AND i.`type`='resource' AND i.`object_id`=" . $this->db->quote($result->oid);

					$this->db->setQuery($query);
					if (!$this->db->loadResult())
					{
						// No collection item

						// Do we have a collection ID for this user?
						if (!isset($usrs[$result->uid]))
						{
							// No ID yet. Check if the user has a default collection
							$query = "SELECT p.id FROM `#__collections` AS p WHERE p.`object_id`=" . $this->db->quote($result->uid) . " AND p.`object_type`='member' AND p.`is_default`=1";
							$this->db->setQuery($query);
							if (!($collection_id = $this->db->loadResult()))
							{
								// No default collection.
								// So, we make one.
								$tbl = new CollectionsTableCollection($this->db);
								$tbl->setup($result->uid, 'member');
								$usrs[$result->uid] = $tbl->id;
							}
							else
							{
								$usrs[$result->uid] = $collection_id;
							}
						}

						// Check if we already have an item_id
						if (!isset($objs[$result->oid]))
						{
							// Check if an item entry exists
							$b = new CollectionsTableItem($this->db);
							$b->loadType($result->oid, 'resource');
							if (!$b->id)
							{
								// No item entry

								// Get some resource data
								$query = "SELECT id, title, introtext FROM `#__resources` WHERE id=" . $this->db->quote($result->oid);
								$this->db->setQuery($query);
								$resource = $this->db->loadObject();
								if (!$resource || !$resource->id)
								{
									continue;
								}

								// Create the item
								$b->type        = 'resource';
								$b->object_id   = $resource->id;
								$b->title       = $resource->title;
								$b->description = $resource->introtext;
								$b->url         = 'index.php?option=com_resources&id=' . $resource->id;
								if (!$b->check())
								{
									continue;
								}
								if (!$b->store())
								{
									continue;
								}
							}
							// Set the item_id for thsi resource
							// as it's most likely to be needed again
							$objs[$result->oid] = $b->id;
							unset($b);
						}

						// Create a post associating the item to a collection
						$stick = new CollectionsTablePost($this->db);
						$stick->item_id       = $objs[$result->oid];
						$stick->collection_id = $usrs[$result->uid];
						if ($stick->check())
						{
							// Store new content
							if (!$stick->store())
							{
								continue;
							}
						}
					}

					$progress = round($i/$total*100);
					$this->callback('progress', 'setProgress', array($progress));
					$i++;
				}
			}

			$this->callback('progress', 'done');

			$query = "DROP TABLE IF EXISTS `#__xfavorites`;";
			$this->db->setQuery($query);
			$this->db->query();

			$this->deletePluginEntry('members', 'favorites');
			$this->deletePluginEntry('resources', 'favorite');
			$this->deletePluginEntry('publications', 'favorite');
			$this->deleteModuleEntry('mod_myfavorites');
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__xfavorites'))
		{
			$query = "CREATE TABLE `#__xfavorites` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `uid` int(11) DEFAULT '0',
				  `oid` int(11) DEFAULT '0',
				  `tbl` varchar(250) DEFAULT NULL,
				  `faved` datetime DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$this->addPluginEntry('members', 'favorites');
			$this->addPluginEntry('resources', 'favorite');
			$this->addPluginEntry('publications', 'favorite');
			$this->addModuleEntry('mod_myfavorites');
		}
	}
}
