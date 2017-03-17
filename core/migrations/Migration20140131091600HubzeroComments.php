<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding params field to asset groups
 **/
class Migration20140131091600HubzeroComments extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__comments'))
		{
			$query = "SELECT * FROM `#__comments`";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

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
					$record = new $cls($this->db);

					if ($record instanceof \Hubzero\Database\Relational)
					{
						$data = array(
							'content'    => $r->comment,
							'created'    => $r->added,
							'created_by' => $r->added_by,
							'state'      => $r->state,
							'anonymous'  => $r->anonymous,
							'notify'     => $r->email,
							'item_id'    => $r->referenceid,
							'item_type'  => $r->category
						);

						if (substr($r->category, -7) == 'comment')
						{
							if (isset($parents[$r->referenceid]))
							{
								$data['parent']    = $parents[$r->referenceid]['id'];
								$data['item_id']   = $parents[$r->referenceid]['referenceid'];
								$data['item_type'] = $parents[$r->referenceid]['category'];
							}
						}

						$record
							->set($data)
							->save();
					}
					else
					{
						$record->item_id    = $r->referenceid;
						$record->item_type  = $r->category;
						$record->content    = $r->comment;
						$record->created    = $r->added;
						$record->created_by = $r->added_by;
						$record->state      = $r->state;
						$record->anonymous  = $r->anonymous;
						$record->notify     = $r->email;

						if (substr($r->category, -7) == 'comment')
						{
							if (isset($parents[$r->referenceid]))
							{
								$record->parent    = $parents[$r->referenceid]['id'];
								$record->item_id   = $parents[$r->referenceid]['referenceid'];
								$record->item_type = $parents[$r->referenceid]['category'];
							}
						}

						$record->store();
					}

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
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__comments'))
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
				) ENGINE=MYISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
