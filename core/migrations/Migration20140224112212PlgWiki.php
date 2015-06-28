<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for getting rid of duplicate section date entries
 **/
class Migration20140224112212PlgWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		/*
		$this->deletePluginEntry('hubzero', 'wikiparser');
		$this->deletePluginEntry('hubzero', 'wikieditortoolbar');
		$this->deletePluginEntry('hubzero', 'wikieditorwykiwyg');

		$this->addPluginEntry('wiki', 'parserdefault');
		$this->addPluginEntry('wiki', 'editortoolbar');
		$this->addPluginEntry('wiki', 'editorwykiwyg');
		*/

		/* We do an update instead of the above remove/add so as to preserve state and params */
		$query = "UPDATE `#__extensions` SET `folder`='wiki', `element`='parserdefault', `name`='plg_wiki_parserdefault' WHERE `folder`='hubzero' AND `type`='plugin' AND `element`='wikiparser'";

		$this->db->setQuery($query);
		$this->db->query();

		$query = "UPDATE `#__extensions` SET `folder`='wiki', `element`='editortoolbar', `name`='plg_wiki_editortoolbar' WHERE `folder`='hubzero' AND `type`='plugin' AND `element`='wikieditortoolbar'";

		$this->db->setQuery($query);
		$this->db->query();

		$query = "UPDATE `#__extensions` SET `folder`='wiki', `element`='editorwykiwyg', `name`='plg_wiki_editorwykiwyg' WHERE `folder`='hubzero' AND `type`='plugin' AND `element`='wikieditorwykiwyg'";

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Up
	 **/
	public function down()
	{
		/*
		$this->addPluginEntry('hubzero', 'wikiparser');
		$this->addPluginEntry('hubzero', 'wikieditortoolbar');
		$this->addPluginEntry('hubzero', 'wikieditorwykiwyg');

		$this->deletePluginEntry('wiki', 'parserdefault');
		$this->deletePluginEntry('wiki', 'editortoolbar');
		$this->deletePluginEntry('wiki', 'editorwykiwyg');
		*/

		/* We do an update instead of the above remove/add so as to preserve state and params */
		$query = "UPDATE `#__extensions` SET `folder`='hubzero', `element`='wikiparser', `name`='plg_hubzero_wikiparser' WHERE `folder`='wiki' AND `type`='plugin' AND `element`='parserdefault'";

		$this->db->setQuery($query);
		$this->db->query();

		$query = "UPDATE `#__extensions` SET `folder`='hubzero', `element`='wikieditortoolbar', `name`='plg_hubzero_wikieditortoolbar' WHERE `folder`='wiki' AND `type`='plugin' AND `element`='editortoolbar'";

		$this->db->setQuery($query);
		$this->db->query();

		$query = "UPDATE `#__extensions` SET `folder`='hubzero', `element`='wikieditorwykiwyg', `name`='plg_hubzero_wikieditorwykiwyg' WHERE `folder`='wiki' AND `type`='plugin' AND `element`='editorwykiwyg'";

		$this->db->setQuery($query);
		$this->db->query();
	}
}