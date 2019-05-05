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
 * Migration script for standardizing some plugin names
 **/
class Migration20141029185515Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->normalizePluginEntry('content', 'collect');
		$this->normalizePluginEntry('resources', 'collect');
		$this->normalizePluginEntry('wiki', 'collect');
		$this->normalizePluginEntry('support', 'forum');
		$this->normalizePluginEntry('courses', 'memberoptions');
		$this->normalizePluginEntry('cron', 'users');
		$this->normalizePluginEntry('groups', 'collections');
		$this->normalizePluginEntry('members', 'collections');
		$this->normalizePluginEntry('projects', 'databases');
		$this->normalizePluginEntry('publications', 'citations');
		$this->normalizePluginEntry('publications', 'questions');
		$this->normalizePluginEntry('publications', 'recommendations');
		$this->normalizePluginEntry('publications', 'related');
		$this->normalizePluginEntry('publications', 'reviews');
		$this->normalizePluginEntry('publications', 'share');
		$this->normalizePluginEntry('publications', 'supportingdocs');
		$this->normalizePluginEntry('publications', 'usage');
		$this->normalizePluginEntry('publications', 'versions');
		$this->normalizePluginEntry('publications', 'wishlist');
		$this->normalizePluginEntry('resources', 'groups');
		$this->normalizePluginEntry('system', 'indent');
		$this->normalizePluginEntry('system', 'mobile');

		$this->deletePluginEntry('members', 'favorites');
		$this->deletePluginEntry('resources', 'favorite');
		$this->deletePluginEntry('publications', 'favorite');
		$this->deleteModuleEntry('mod_myfavorites');
	}
}
