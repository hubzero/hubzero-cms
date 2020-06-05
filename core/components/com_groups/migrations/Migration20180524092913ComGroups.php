<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;
use Components\Groups\Models\Orm\Field;
use Components\Groups\Models\Orm\Answer;
use Components\Groups\Models\Orm\Group;

require_once \Component::path('com_groups') . '/models/orm/group.php';
require_once \Component::path('com_groups') . '/models/orm/field.php';

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding custom fields
 **/
class Migration20180524092913ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xgroups_description_fields'))
		{
			$query = "CREATE TABLE `#__xgroups_description_fields` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `type` varchar(255) NOT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `placeholder` varchar(255) DEFAULT NULL,
			  `description` mediumtext,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(3) NOT NULL DEFAULT '0',
			  `option_other` tinyint(2) NOT NULL DEFAULT '0',
			  `option_blank` tinyint(2) NOT NULL DEFAULT '0',
			  `required` tinyint(2) NOT NULL DEFAULT '0',
			  `readonly` tinyint(2) NOT NULL DEFAULT '0',
			  `disabled` tinyint(2) NOT NULL DEFAULT '0',
			  `multiple` int(11) NOT NULL DEFAULT '0',
			  `min` int(11) DEFAULT NULL,
			  `max` int(11) DEFAULT NULL,
			  `rows` tinyint(3) DEFAULT NULL,
			  `cols` tinyint(3) DEFAULT NULL,
			  `default_value` varchar(255) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `parent_option` int(11) NOT NULL DEFAULT '0',
			  `validate` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_type` (`type`),
			  KEY `idx_access` (`access`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_description_options'))
		{
			$query = "CREATE TABLE `#__xgroups_description_options` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `field_id` int(11) NOT NULL DEFAULT '0',
			  `value` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `checked` tinyint(2) NOT NULL DEFAULT '0',
			  `dependents` tinytext,
			  PRIMARY KEY (`id`),
			  KEY `idx_field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_description_answers'))
		{
			$query = "CREATE TABLE `#__xgroups_description_answers` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `group_id` int(11) NOT NULL DEFAULT '0',
			  `field_id` int(11) NOT NULL DEFAULT '0',
			  `value` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_group_id` (`group_id`),
			  KEY `idx_field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$groups = Group::all()
			->where('public_desc', '!=', '', 'or')
			->where('private_desc', '!=', '', 'or')
			->rows();

		$publicDescriptionAnswers = array();
		$privateDescriptionAnswers = array();

		foreach ($groups as $group)
		{
			if (!$group->get('gidNumber'))
			{
				continue;
			}
			if ($group->get('public_desc'))
			{
				$publicDescriptionAnswers[] = array(
					'group_id' => $group->get('gidNumber'),
					'value' => $group->get('public_desc')
				);
			}

			if ($group->get('private_desc'))
			{
				$privateDescriptionAnswers[] = array(
					'group_id' => $group->get('gidNumber'),
					'value' => $group->get('private_desc')
				);
			}
		}
		$publicDescField = Field::blank()
			->set(
				array(
					'type' => 'textarea',
					'name' => 'public_desc',
					'label' => 'Public Description',
					'access' => 0,
					'ordering' => 1,
					'rows' => 10
				)
			);
		if ($publicDescField->save())
		{
			if (!empty($publicDescriptionAnswers))
			{
				$publicDescField->answers()->save($publicDescriptionAnswers);
			}
		}
		$privateDescField = Field::blank()
			->set(
				array(
					'type' => 'textarea',
					'name' => 'private_desc',
					'label' => 'Private Description',
					'access' => 2,
					'ordering' => 1,
					'rows' => 10
				)
			);
		if ($privateDescField->save())
		{
			if (!empty($privateDescriptionAnswers))
			{
				$privateDescField->answers()->save($privateDescriptionAnswers);
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xgroups_description_fields'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_description_fields`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_description_options'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_description_options`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_description_answers'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_description_answers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
