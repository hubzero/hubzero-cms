<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! udpate notification plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Quickicon.Joomla
 * @since		2.5
 */
class plgQuickiconJoomlaupdate extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 *
	 * @since       2.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
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
		$script = "var plg_quickicon_joomlaupdate_ajax_url = '$ajax_url';\n";
		$script .= 'var plg_quickicon_jupdatecheck_jversion = "'.JVERSION.'";'."\n";
		$script .= 'var plg_quickicon_joomlaupdate_text = {"UPTODATE" : "'.
			Lang::txt('PLG_QUICKICON_JOOMLAUPDATE_UPTODATE', true).'", "UPDATEFOUND": "'.
			Lang::txt('PLG_QUICKICON_JOOMLAUPDATE_UPDATEFOUND', true).'", "ERROR": "'.
			Lang::txt('PLG_QUICKICON_JOOMLAUPDATE_ERROR', true)."\"};\n";
		$script .= 'var plg_quickicon_joomlaupdate_img = {"UPTODATE" : "'.
			Request::base(true) .'/templates/'. $cur_template .'/images/header/icon-48-jupdate-uptodate.png'.'", "ERROR": "'.
			Request::base(true) .'/templates/'. $cur_template .'/images/header/icon-48-deny.png'.'", "UPDATEFOUND": "'.
			Request::base(true) .'/templates/'. $cur_template .'/images/header/icon-48-jupdate-updatefound.png'."\"};\n";

		Document::addScriptDeclaration($script);
		Document::addScript(Request::base().'../media/plg_quickicon_joomlaupdate/jupdatecheck.js');

		return array(array(
			'link' => 'index.php?option=com_joomlaupdate',
			'image' => 'header/icon-48-download.png',
			'text' => Lang::txt('PLG_QUICKICON_JOOMLAUPDATE_CHECKING'),
			'id' => 'plg_quickicon_joomlaupdate'
		));
	}
}
