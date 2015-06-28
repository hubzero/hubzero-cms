<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to move existing antispam plugins and add a couple more
 **/
class Migration20150202134839PlgAntispam extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = '';

		if ($this->db->tableExists('#__extensions'))
		{
			// Move the existing plugin entries when possible to preserve params,
			// otherwise add the entry
			foreach (array('akismet', 'mollom', 'spamassassin') as $plg)
			{
				$this->db->setQuery("SELECT extension_id FROM `#__extensions` WHERE `type`='plugin' AND `folder`='content' AND `element`=" . $this->db->quote($plg));
				if ($id = $this->db->loadResult())
				{
					$this->db->setQuery("UPDATE `#__extensions` SET `folder`='antispam' AND `name`=" . $this->db->quote('plg_antispam_' . $plg) . " WHERE `extension_id`=" . $this->db->quote($id));
					$this->db->query();
				}
				else
				{
					$this->addPluginEntry('antispam', $plg, 0);
				}
			}

			// Get the params from the old antispam plugin. We need the badwords list for the 'blacklist' plugin.
			$this->db->setQuery("SELECT params FROM `#__extensions` WHERE `type`='plugin' AND `folder`='content' AND `element`='antispam'");
			$params = $this->db->loadResult();
		}

		$this->addPluginEntry('antispam', 'blacklist', 0, $params);
		$this->addPluginEntry('antispam', 'linkrife', 0);
		$this->addPluginEntry('antispam', 'bayesian', 0);

		if (!$this->db->tableExists('#__antispam_token_probs'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__antispam_token_probs` (
				  `id` int(11) NOT NULL auto_increment,
				  `token` varchar(256) NOT NULL,
				  `prob` float DEFAULT '0.00',
				  `prev_prob` float DEFAULT '0.00',
				  `in_ham` int DEFAULT '0',
				  `in_spam` int DEFAULT '0',
				  `provider` varchar(256),
				  `param1` varchar(256) NOT NULL,
				  `param2` varchar(256) NOT NULL,
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__antispam_token_counts'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__antispam_token_counts` (
				  `id` int(11) NOT NULL auto_increment,
				  `good_count` int(11) DEFAULT '0',
				  `bad_count` int(11) DEFAULT '0',
				  PRIMARY KEY  (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__antispam_message_hashes'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__antispam_message_hashes` (
				  `id` int(11) NOT NULL auto_increment,
				  `hash` varchar(256) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			// Move the existing plugin entries when possible to preserve params,
			// otherwise add the entry
			foreach (array('akismet', 'mollom', 'spamassassin') as $plg)
			{
				$this->db->setQuery("SELECT extension_id FROM `#__extensions` WHERE `type`='plugin' AND `folder`='antispam' AND `element`=" . $this->db->quote($plg));
				if ($id = $this->db->loadResult())
				{
					$this->db->setQuery("UPDATE `#__extensions` SET `folder`='content' AND `name`=" . $this->db->quote('plg_content_' . $plg) . " WHERE `extension_id`=" . $this->db->quote($id));
					$this->db->query();
				}
				else
				{
					$this->addPluginEntry('content', $plg, 0);
				}
			}
		}

		$this->deletePluginEntry('antispam', 'blacklist');
		$this->deletePluginEntry('antispam', 'linkrife');
		$this->deletePluginEntry('antispam', 'bayesian');

		if ($this->db->tableExists('#__antispam_token_probs'))
		{
			$query = "DROP TABLE `#__antispam_token_probs`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__antispam_token_counts'))
		{
			$query = "DROP TABLE `#__antispam_token_counts`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__antispam_message_hashes'))
		{
			$query = "DROP TABLE `#__antispam_message_hashes`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}