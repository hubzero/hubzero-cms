<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Media component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.0
 */
class MediaViewImagesList extends JViewLegacy
{
	function display($tpl = null)
	{
		// Do not allow cache
		App::get('response')->headers->set('Cache-Control', 'no-cache', false);
		App::get('response')->headers->set('Pragma', 'no-cache');

		//Html::asset('stylesheet', 'media/popup-imagelist.css', array(), true);
		\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'popup-imagelist.css');
		if (Lang::isRTL()) :
			//Html::asset('stylesheet', 'media/popup-imagelist_rtl.css', array(), true);
			\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'popup-imagelist_rtl.css');
		endif;

		Document::addScriptDeclaration("var ImageManager = window.parent.ImageManager;");

		$images  = $this->get('images');
		$folders = $this->get('folders');
		$state   = $this->get('state');

		$this->baseURL = COM_MEDIA_BASEURL;
		$this->assignRef('images', $images);
		$this->assignRef('folders', $folders);
		$this->assignRef('state', $state);

		parent::display($tpl);
	}

	function setFolder($index = 0)
	{
		if (isset($this->folders[$index]))
		{
			$this->_tmp_folder = &$this->folders[$index];
		}
		else
		{
			$this->_tmp_folder = new \Hubzero\Base\Object;
		}
	}

	function setImage($index = 0)
	{
		if (isset($this->images[$index]))
		{
			$this->_tmp_img = &$this->images[$index];
		}
		else
		{
			$this->_tmp_img = new \Hubzero\Base\Object;
		}
	}
}
