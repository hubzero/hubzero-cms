<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * The com_members "Mass mail users" screen (option=com_members&controller=mail)
 * has been retired in favor of the dedicated, rate-limited com_massmail
 * component. This migration repoints any data-driven menu entries that still
 * link to the old screen at our new component.
 *
 * (On installs whose admin menu is code-driven via mod_adminmenu, the link is
 * repointed in that template instead and this is a no-op.)
 **/
class Migration20260707000002ComMembersMassmail extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__menu'))
		{
			$this->db->setQuery(
				"UPDATE `#__menu`
				 SET `link` = REPLACE(`link`, 'option=com_members&controller=mail', 'option=com_massmail')
				 WHERE `link` LIKE '%option=com_members&controller=mail%';"
			);
			$this->db->query();
		}
	}

	/**
	 * Down
	 *
	 * Intentionally a no-op: a blind reverse would rewrite every
	 * option=com_massmail menu link -- including the com_massmail component's
	 * own admin menu entry -- so we cannot safely distinguish rows this
	 * migration changed. Retiring the old screen is forward-only.
	 **/
	public function down()
	{
		// No-op (see note above).
	}
}
