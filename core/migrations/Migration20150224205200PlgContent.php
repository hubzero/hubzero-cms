<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for forcing preferred settings for content handlers and jQuery plugins
 **/
class Migration20150224205200PlgContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT * FROM `#__extensions` WHERE `type`='plugin' AND `folder`='system' AND `element`='jquery' LIMIT 1;";
			$this->db->setQuery($query);
			if ($plugin = $this->db->loadObject())
			{
				$params = new \Hubzero\Config\Registry($plugin->params);
				$params->set('jquery', 1);
				$params->set('jqueryui', 1);
				$params->set('jqueryfb', 1);
				$params->set('activateAdmin', 0);
				$params->set('noconflictAdmin', 0);
				$params->set('activateSite', 1);
				$params->set('noconflictSite', 0);

				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `extension_id`=" . $this->db->quote($plugin->extension_id);
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "SELECT * FROM `#__extensions` WHERE `type`='plugin' AND `folder`='content' AND `element`='formatwiki' LIMIT 1;";
			$this->db->setQuery($query);
			if ($plugin = $this->db->loadObject())
			{
				$params = new \Hubzero\Config\Registry($plugin->params);
				$params->set('applyFormat', 1);
				$params->set('convertFormat', 1);

				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `extension_id`=" . $this->db->quote($plugin->extension_id);
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "SELECT * FROM `#__extensions` WHERE `type`='plugin' AND `folder`='content' AND `element`='formathtml' LIMIT 1;";
			$this->db->setQuery($query);
			if ($plugin = $this->db->loadObject())
			{
				$params = new \Hubzero\Config\Registry($plugin->params);
				$params->set('applyFormat', 1);
				$params->set('convertFormat', 0);
				$params->set('sanitizeBefore', 0);

				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `extension_id`=" . $this->db->quote($plugin->extension_id);
				$this->db->setQuery($query);
				$this->db->query();

				if (!$plugin->enabled)
				{
					$this->enablePlugin('content', 'formathtml');
				}
			}
			else
			{
				$params = new \Hubzero\Config\Registry;
				$params->set('applyFormat', 1);
				$params->set('convertFormat', 0);
				$params->set('sanitizeBefore', 0);

				$this->addPluginEntry('content', 'formathtml', 1, $params->toString());
			}
		}
	}
}
