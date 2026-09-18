<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Turn on formathtml's sanitiser for hubs that were installed before it existed.
 *
 * The render-time Sanitize::html() pass is gated on the plugin's sanitizeBefore
 * parameter, whose code default is 1. That default never applies on an upgraded
 * hub: sanitizeBefore is an existing stored parameter and the old seed row wrote
 * it explicitly as 0, so Registry::get('sanitizeBefore', 1) returns the stored
 * 0 and the sanitiser stays off. Flipping the value in the install seed data
 * only helps a fresh install.
 *
 * Only a stored 0 is changed. A hub that has set the value deliberately -- to 1
 * already, or to anything else -- is left alone, and a plugin row carrying no
 * sanitizeBefore key at all is given one so the intent is explicit rather than
 * resting on the code default.
 */
class Migration20260921170000PlgContentFormathtml extends Base
{
	/**
	 * Up
	 *
	 * @return  void
	 */
	public function up()
	{
		$this->db->setQuery(
			"SELECT `extension_id`, `params` FROM `#__extensions`
			  WHERE `type` = 'plugin' AND `folder` = 'content' AND `element` = 'formathtml'"
		);

		$rows = $this->db->loadObjectList();

		if (!$rows)
		{
			return;
		}

		foreach ($rows as $row)
		{
			$params = json_decode((string) $row->params, true);

			if (!is_array($params))
			{
				$params = array();
			}

			if (array_key_exists('sanitizeBefore', $params)
			 && (string) $params['sanitizeBefore'] !== '0')
			{
				// set deliberately to something other than off -- leave it
				continue;
			}

			$params['sanitizeBefore'] = 1;

			$this->db->setQuery(
				"UPDATE `#__extensions` SET `params` = " . $this->db->quote(json_encode($params)) .
				" WHERE `extension_id` = " . (int) $row->extension_id
			);
			$this->db->query();

			$this->log('Enabled sanitizeBefore on plg_content_formathtml (extension ' . (int) $row->extension_id . ')');
		}
	}

	/**
	 * Down
	 *
	 * Deliberately does not restore 0: switching the sanitiser back off would
	 * reopen the stored-XSS path it closes.
	 *
	 * @return  void
	 */
	public function down()
	{
	}
}
