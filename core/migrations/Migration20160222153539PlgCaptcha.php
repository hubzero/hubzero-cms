<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to move existing captcha plugins to captcha plugin group
 **/
class Migration20160222153539PlgCaptcha extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = '';

		if ($this->db->tableExists('#__extensions'))
		{
			$this->db->setQuery(
				"UPDATE `#__extensions`
				SET `folder`=" . $this->db->quote('captcha') . ", `element`=" . $this->db->quote('math') . ", `name`=" . $this->db->quote('plg_captcha_math') . "
				WHERE `folder`=" . $this->db->quote('hubzero') . " AND `element`=" . $this->db->quote('mathcaptcha')
			);
			$this->db->query();

			$this->db->setQuery(
				"UPDATE `#__extensions`
				SET `folder`=" . $this->db->quote('captcha') . ", `element`=" . $this->db->quote('image') . ", `name`=" . $this->db->quote('plg_captcha_image') . "
				WHERE `folder`=" . $this->db->quote('hubzero') . " AND `element`=" . $this->db->quote('imagecaptcha')
			);
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
			$this->db->setQuery(
				"UPDATE `#__extensions`
				SET `folder`=" . $this->db->quote('hubzero') . ", `element`=" . $this->db->quote('mathcaptcha') . ", `name`=" . $this->db->quote('plg_hubzero_mathcaptcha') . "
				WHERE `folder`=" . $this->db->quote('captcha') . " AND `element`=" . $this->db->quote('math')
			);
			$this->db->query();

			$this->db->setQuery(
				"UPDATE `#__extensions`
				SET `folder`=" . $this->db->quote('hubzero') . ", `element`=" . $this->db->quote('imagecaptcha') . ", `name`=" . $this->db->quote('plg_hubzero_imagecaptcha') . "
				WHERE `folder`=" . $this->db->quote('captcha') . " AND `element`=" . $this->db->quote('image')
			);
			$this->db->query();
		}
	}
}