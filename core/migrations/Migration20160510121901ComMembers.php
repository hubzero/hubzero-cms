<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding tables and data for profile schema
 **/
class Migration20160510121901ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__user_profile_fields'))
		{
			$query = "CREATE TABLE `#__user_profile_fields` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `type` varchar(255) NOT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `placeholder` varchar(255) DEFAULT NULL,
			  `description` mediumtext,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `access` int(10) NOT NULL DEFAULT '0',
			  `option_other` tinyint(2) NOT NULL DEFAULT '0',
			  `option_blank` tinyint(2) NOT NULL DEFAULT '0',
			  `action_create` tinyint(2) NOT NULL DEFAULT '1',
			  `action_update` tinyint(2) NOT NULL DEFAULT '1',
			  `action_edit` tinyint(2) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`id`),
			  KEY `idx_type` (`type`),
			  KEY `idx_access` (`access`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$params = \Component::params('com_members');

			$query = "INSERT INTO `#__user_profile_fields` (`id`, `type`, `name`, `label`, `placeholder`, `description`, `ordering`, `access`, `option_other`, `option_blank`, `action_create`, `action_update`, `action_edit`)
					VALUES
						(1,'select','organization','Organization',NULL,NULL,1,1,1,1," . self::state($params->get('registrationOrganization'), 'HHHH', 'create') . "," . self::state($params->get('registrationOrganization'), 'HHHH', 'update') . "," . self::state($params->get('registrationOrganization'), 'HHHH', 'edit') . "),
						(2,'select','orgtype','Employment Status',NULL,NULL,2,5,0,1," . self::state($params->get('registrationEmployment'), 'HHHH', 'create') . "," . self::state($params->get('registrationEmployment'), 'HHHH', 'update') . "," . self::state($params->get('registrationEmployment'), 'HHHH', 'edit') . "),
						(3,'country','countryresident','Residency',NULL,'Select your Country of Residency.',3,5,0,1," . self::state($params->get('registrationResidency'), 'HHHH', 'create') . "," . self::state($params->get('registrationResidency'), 'HHHH', 'update') . "," . self::state($params->get('registrationResidency'), 'HHHH', 'edit') . "),
						(4,'country','countryorigin','Citizenship',NULL,'Select your Country of Citizenship.',4,5,0,1," . self::state($params->get('registrationCitizenship'), 'HHHH', 'create') . "," . self::state($params->get('registrationCitizenship'), 'HHHH', 'update') . "," . self::state($params->get('registrationCitizenship'), 'HHHH', 'edit') . "),
						(5,'url','url','Website',NULL,NULL,5,1,0,0," . self::state($params->get('registrationURL'), 'HHHH', 'create') . "," . self::state($params->get('registrationURL'), 'HHHH', 'update') . "," . self::state($params->get('registrationURL'), 'HHHH', 'edit') . "),
						(6,'text','phone','Telephone',NULL,NULL,6,5,0,0," . self::state($params->get('registrationPhone'), 'HHHH', 'create') . "," . self::state($params->get('registrationPhone'), 'HHHH', 'update') . "," . self::state($params->get('registrationPhone'), 'HHHH', 'edit') . "),
						(7,'orcid','orcid','ORCID',NULL,'Open Researcher and Contributor ID (ORCID) provides a persistent digital identifier that distinguishes you from every other researcher and supports automated linkages between you and your professional activities ensuring that your work is recognized.',7,1,0,0," . self::state($params->get('registrationORCID'), 'HHHH', 'create') . "," . self::state($params->get('registrationORCID'), 'HHHH', 'update') . "," . self::state($params->get('registrationORCID'), 'HHHH', 'edit') . "),
						(8,'radio','gender','Gender',NULL,NULL,8,5,0,0," . self::state($params->get('registrationSex'), 'HHHH', 'create') . "," . self::state($params->get('registrationSex'), 'HHHH', 'update') . "," . self::state($params->get('registrationSex'), 'HHHH', 'edit') . "),
						(9,'checkboxes','race','Racial Background',NULL,NULL,9,5,0,0," . self::state($params->get('registrationRace'), 'HHHH', 'create') . "," . self::state($params->get('registrationRace'), 'HHHH', 'update') . "," . self::state($params->get('registrationRace'), 'HHHH', 'edit') . "),
						(10,'checkboxes','disability','Disability',NULL,NULL,10,5,0,0," . self::state($params->get('registrationDisability'), 'HHHH', 'create') . "," . self::state($params->get('registrationDisability'), 'HHHH', 'update') . "," . self::state($params->get('registrationDisability'), 'HHHH', 'edit') . "),
						(11,'select','reason','Reason',NULL,NULL,11,5,0,1," . self::state($params->get('registrationReason'), 'HHHH', 'create') . "," . self::state($params->get('registrationReason'), 'HHHH', 'update') . "," . self::state($params->get('registrationReason'), 'HHHH', 'edit') . "),
						(12,'tags','tags','Interests',NULL,NULL,12,1,0,0," . self::state($params->get('registrationInterests'), 'HHHH', 'create') . "," . self::state($params->get('registrationInterests'), 'HHHH', 'update') . "," . self::state($params->get('registrationInterests'), 'HHHH', 'edit') . "),
						(13,'address','address','Address',NULL,NULL,13,5,0,0," . self::state($params->get('registrationAddress'), 'HHHH', 'create') . "," . self::state($params->get('registrationAddress'), 'HHHH', 'update') . "," . self::state($params->get('registrationAddress'), 'HHHH', 'edit') . "),
						(14,'radio','hispanic','Hispanic Heritage',NULL,NULL,14,5,0,0," . self::state($params->get('registrationHispanic'), 'HHHH', 'create') . "," . self::state($params->get('registrationHispanic'), 'HHHH', 'update') . "," . self::state($params->get('registrationHispanic'), 'HHHH', 'edit') . "),
						(15,'editor','bio','Biography',NULL,NULL,15,1,0,0,0,0,1);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_profile_options'))
		{
			$query = "CREATE TABLE `#__user_profile_options` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `field_id` int(11) NOT NULL DEFAULT '0',
				  `value` varchar(255) NOT NULL DEFAULT '',
				  `label` varchar(255) NOT NULL DEFAULT '',
				  `ordering` int(11) NOT NULL DEFAULT '0',
				  `checked` tinyint(2) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			// Gender
			$query = "INSERT INTO `#__user_profile_options` (`id`, `field_id`, `value`, `label`, `ordering`, `checked`)
						VALUES (null,8,'male','Male',1,0),
								(null,8,'female','Female',2,0),
								(null,8,'refused','Do not with to reveal',3,0);";
			$this->db->setQuery($query);
			$this->db->query();

			// Race
			$query = "INSERT INTO `#__user_profile_options` (`id`, `field_id`, `value`, `label`, `ordering`, `checked`)
						VALUES (null,9,'nativeamerican','American Indian or Alaska Native',1,0),
								(null,9,'asian','Asian',2,0),
								(null,9,'black','Black or African American',3,0),
								(null,9,'hawaiian','Native Hawaiian or Other Pacific Islander',4,0),
								(null,9,'white','White',5,0),
								(null,9,'refused','Do not wish to reveal',6,0);";
			$this->db->setQuery($query);
			$this->db->query();

			// Hispanic
			$query = "INSERT INTO `#__user_profile_options` (`id`, `field_id`, `value`, `label`, `ordering`, `checked`)
						VALUES (null,14,'cuban','Cuban',1,0),
								(null,14,'mexican','Mexican American or Chicano',2,0),
								(null,14,'puertorican','Puerto Rican',3,0),
								(null,14,'no','No (not Hispanic or Latino)',4,0),
								(null,14,'refused','Do not wish to reveal',5,0);";
			$this->db->setQuery($query);
			$this->db->query();

			// Disability
			$query = "INSERT INTO `#__user_profile_options` (`id`, `field_id`, `value`, `label`, `ordering`, `checked`)
						VALUES (null,10,'blind','Blind / Visually Impaired',1,0),
								(null,10,'deaf','Deaf / Hard of Hearing',2,0),
								(null,10,'physical','Physical / Orthopedic Disability',3,0),
								(null,10,'learning','Learning / Cognitive Disability',4,0),
								(null,10,'vocal','Vocal / Speech Disability',5,0),
								(null,10,'no','No (none)',6,0),
								(null,10,'refused','Do not wish to reveal',7,0);";
			$this->db->setQuery($query);
			$this->db->query();

			// Organizations
			if ($this->db->tableExists('#__xorganizations'))
			{
				$query = "SELECT * FROM `#__xorganizations`";
				$this->db->setQuery($query);
				$organizations = $this->db->loadObjectList();

				if (count($organizations) > 0)
				{
					$query = "INSERT INTO `#__user_profile_options` (`id`, `field_id`, `value`, `label`, `ordering`, `checked`)
							VALUES ";

					$queries = array();
					foreach ($organizations as $i => $organization)
					{
						$queries[] = "(null,1," . $this->db->quote($organization->organization) . "," . $this->db->quote($organization->organization) . "," . ($i + 1) . ",0)";
					}

					$this->db->setQuery($query . implode(',', $queries) . ';');
					$this->db->query();
				}

				$query = "DROP TABLE IF EXISTS `#__xorganizations`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Organization types
			if ($this->db->tableExists('#__xorganization_types'))
			{
				$query = "SELECT * FROM `#__xorganization_types`";
				$this->db->setQuery($query);
				$types = $this->db->loadObjectList();

				if (count($types) > 0)
				{
					$query = "INSERT INTO `#__user_profile_options` (`id`, `field_id`, `value`, `label`, `ordering`, `checked`)
							VALUES ";

					$queries = array();
					foreach ($types as $i => $type)
					{
						$queries[] = "(null,2," . $this->db->quote($type->type) . "," . $this->db->quote($type->title) . "," . ($i + 1) . ",0)";
					}

					$this->db->setQuery($query . implode(',', $queries) . ';');
					$this->db->query();
				}

				$query = "DROP TABLE IF EXISTS `#__xorganization_types`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Reasons
			if ($this->db->tableExists('#__xprofiles_reasons'))
			{
				$query = "SELECT * FROM `#__xprofiles_reasons`";
				$this->db->setQuery($query);
				$reasons = $this->db->loadObjectList();

				if (count($reasons) > 0)
				{
					$query = "INSERT INTO `#__user_profile_options` (`id`, `field_id`, `value`, `label`, `ordering`, `checked`)
							VALUES ";

					$queries = array();
					foreach ($reasons as $i => $reason)
					{
						$queries[] = "(null,11," . $this->db->quote($reason->reason) . "," . $this->db->quote($reason->reason) . "," . ($i + 1) . ",0)";
					}

					$this->db->setQuery($query . implode(',', $queries) . ';');
					$this->db->query();
				}

				$query = "DROP TABLE IF EXISTS `#__xprofiles_reasons`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__user_profile_fields'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_profile_fields`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// Organization types
		if (!$this->db->tableExists('#__xorganizations'))
		{
			$query = "CREATE TABLE `#__xorganizations` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `organization` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableExists('#__user_profile_options'))
			{
				$query = "SELECT * FROM `#__user_profile_options` WHERE `field_id`=1 ORDER BY `ordering`";
				$this->db->setQuery($query);
				$organizations = $this->db->loadObjectList();

				if (count($organizations) > 0)
				{
					$query = "INSERT INTO `#__xorganizations` (`id`, `organization`)
							VALUES ";

					$queries = array();
					foreach ($organizations as $i => $organization)
					{
						$queries[] = "(null," . $this->db->quote($organization->label) . ")";
					}

					$this->db->setQuery($query . implode(',', $queries) . ';');
					$this->db->query();
				}
			}
		}

		// Organization types
		if (!$this->db->tableExists('#__xorganization_types'))
		{
			$query = "CREATE TABLE `#__xorganization_types` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `type` varchar(150) DEFAULT NULL,
				  `title` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableExists('#__user_profile_options'))
			{
				$query = "SELECT * FROM `#__user_profile_options` WHERE `field_id`=2 ORDER BY `ordering`";
				$this->db->setQuery($query);
				$types = $this->db->loadObjectList();

				if (count($types) > 0)
				{
					$query = "INSERT INTO `#__xorganization_types` (`id`, `type`, `title`)
							VALUES ";

					$queries = array();
					foreach ($types as $i => $type)
					{
						$queries[] = "(null," . $this->db->quote($type->name) . "," . $this->db->quote($type->label) . ")";
					}

					$this->db->setQuery($query . implode(',', $queries) . ';');
					$this->db->query();
				}
			}
		}

		// Reasons
		if (!$this->db->tableExists('#__xprofiles_reasons'))
		{
			$query = "CREATE TABLE `#__xprofiles_reasons` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `reason` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			if ($this->db->tableExists('#__user_profile_options'))
			{
				$query = "SELECT * FROM `#__user_profile_options` WHERE `field_id`=11 ORDER BY `ordering`";
				$this->db->setQuery($query);
				$reasons = $this->db->loadObjectList();

				if (count($reasons) > 0)
				{
					$query = "INSERT INTO `#__xprofiles_reasons` (`id`, `reason`)
							VALUES ";

					$queries = array();
					foreach ($reasons as $i => $reason)
					{
						$queries[] = "(null," . $this->db->quote($reason->label) . ")";
					}

					$this->db->setQuery($query . implode(',', $queries) . ';');
					$this->db->query();
				}
			}
		}

		if ($this->db->tableExists('#__user_profile_options'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_profile_options`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Return if a field is required, option, read only, or hidden
	 *
	 * @param   string  $name     Property name
	 * @param   string  $default  Default property value
	 * @param   string  $task     Task to look up value for
	 * @return  string
	 */
	public static function state($configured, $default = 'OOOO', $task = 'create')
	{
		switch ($task)
		{
			case 'register':
			case 'create': $index = 0; break;
			case 'proxy':  $index = 1; break;
			case 'update': $index = 2; break;
			case 'edit':   $index = 3; break;
			default:       $index = 0; break;
		}

		$default = str_pad($default, 4, '-');

		if (empty($configured))
		{
			$configured = $default;
		}

		$length = strlen($configured);

		if ($length <= $index)
		{
			$configured = $default;
		}

		$key = substr($configured, $index, 1);

		switch ($key)
		{
			case 'R': $val = 2; break;
			case 'O': $val = 1; break;
			case 'U': $val = 4; break;
			case 'H':
			case '-':
			default : $val = 0; break;
		}

		return $val;
	}
}
