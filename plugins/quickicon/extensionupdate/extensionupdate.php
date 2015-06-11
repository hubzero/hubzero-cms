<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Extensions udpate notification plugin
 */
class plgQuickiconExtensionupdate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Returns an icon definition for an icon which looks for extensions updates
	 * via AJAX and displays a notification when such updates are found.
	 *
	 * @param  $context  The calling context
	 *
	 * @return array A list of icon definition associative arrays, consisting of the
	 *				 keys link, image, text and access.
	 *
	 * @since       2.5
	 */
	public function onGetIcons($context)
	{
		if ($context != $this->params->get('context', 'mod_quickicon') || !User::authorise('core.manage', 'com_installer'))
		{
			return;
		}

		$cur_template = App::get('template')->template;
		$ajax_url = Request::base().'index.php?option=com_installer&view=update&task=update.ajax';
		$script = "var plg_quickicon_extensionupdate_ajax_url = '$ajax_url';\n";
		$script .= 'var plg_quickicon_extensionupdate_text = {"UPTODATE" : "'.
			Lang::txt('PLG_QUICKICON_EXTENSIONUPDATE_UPTODATE', true).'", "UPDATEFOUND": "'.
			Lang::txt('PLG_QUICKICON_EXTENSIONUPDATE_UPDATEFOUND', true).'", "ERROR": "'.
			Lang::txt('PLG_QUICKICON_EXTENSIONUPDATE_ERROR', true)."\"};\n";

		$this->js($script);
		$this->js('extensionupdatecheck.js');

		return array(array(
			'link' => 'index.php?option=com_installer&view=update',
			'image' => 'header/icon-48-extension.png',
			'text' => Lang::txt('PLG_QUICKICON_EXTENSIONUPDATE_CHECKING'),
			'id' => 'plg_quickicon_extensionupdate'
		));
	}
}
