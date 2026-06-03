<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script that explicitly persists the default value for the
 * pucas "passive_sso" plugin param.
 *
 * passive_sso=0 keeps the plugin from issuing a CAS gateway (SAML
 * IsPassive) request when probing login status. Purdue's Entra ID IdP
 * answers IsPassive with a "Stale Request" error page instead of silently
 * returning, which breaks login. The plugin already treats a missing param
 * as 0 at runtime; this migration writes the default into the stored params
 * so the setting is explicit and visible in the admin.
 **/
class Migration20260603000000PlgAuthenticationPucas extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__extensions'))
		{
			return;
		}

		$params = $this->currentParams();

		if ($params === false)
		{
			// Plugin row not present yet; its manifest default applies.
			return;
		}

		// Only set the default when the admin hasn't already configured it,
		// so this is idempotent and never overrides an explicit choice.
		if (!array_key_exists('passive_sso', $params))
		{
			$params['passive_sso'] = '0';
			$this->saveParams('plg_authentication_pucas', $params);
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__extensions'))
		{
			return;
		}

		$params = $this->currentParams();

		if (is_array($params) && array_key_exists('passive_sso', $params))
		{
			unset($params['passive_sso']);
			$this->saveParams('plg_authentication_pucas', $params);
		}
	}

	/**
	 * Load the current pucas plugin params as an associative array.
	 *
	 * @return  array|false  params array, or false if the plugin row is absent
	 **/
	protected function currentParams()
	{
		$query = "SELECT `params` FROM `#__extensions`
			WHERE `type` = 'plugin'
			  AND `folder` = 'authentication'
			  AND `element` = 'pucas'
			LIMIT 1";
		$this->db->setQuery($query);
		$current = $this->db->loadResult();

		if ($current === null)
		{
			return false;
		}

		$params = json_decode($current, true);

		return is_array($params) ? $params : array();
	}
}
