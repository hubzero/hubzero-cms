<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for forcefully setting plg_content_formathtml's params and enabled state
 **/
class Migration20150413155222PlgContentFormathtml extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'content' AND `element` = 'formathtml'";
		$this->db->setQuery($query);
		if ($this->db->loadResult())
		{
			$query = "SELECT `enabled` FROM `#__extensions` WHERE `element` = " . $this->db->quote('plg_content_formathtml');

			$this->db->setQuery($query);
			$enabled = $this->db->loadResult();

			if (!$enabled)
			{
				$params = $this->getParams('plg_content_formatwiki');
				$params->set('applyFormat', 1);
				$params->set('convertFormat', 1);

				$this->saveParams('plg_content_formatwiki', $params);

				$params = $this->getParams('plg_content_formathtml');
				$params->set('applyFormat', 1);
				$params->set('convertFormat', 0);
				$params->set('sanitizeBefore', 0);

				$this->saveParams('plg_content_formathtml', $params);
				$this->enablePlugin('content', 'formathtml');
			}
		}
		else
		{
			$params = new \JRegistry;
			$params->set('applyFormat', 1);
			$params->set('convertFormat', 0);
			$params->set('sanitizeBefore', 0);

			$this->addPluginEntry('content', 'formathtml', 1, $params->toString());
		}
	}
}