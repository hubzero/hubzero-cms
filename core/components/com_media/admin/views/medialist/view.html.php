<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * HTML View class for the Media component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.0
 */
class MediaViewMediaList extends JViewLegacy
{
	function display($tpl = null)
	{
		// Do not allow cache
		App::get('response')->headers->set('Cache-Control', 'no-cache', false);
		App::get('response')->headers->set('Pragma', 'no-cache');

		$style = Request::getState('media.list.layout', 'layout', 'thumbs', 'word');

		Html::behavior('framework', true);

		//Document::addStyleSheet('../media/media/css/medialist-'.$style.'.css');
		\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'medialist-'.$style.'.css');
		if (Lang::isRTL()) :
			//Document::addStyleSheet('../media/media/css/medialist-'.$style.'_rtl.css');
			\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'medialist-'.$style.'_rtl.css');
		endif;

		Document::addScriptDeclaration("
		jQuery(document).ready(function($){
			window.parent.document.updateUploader();
			$('a.img-preview').on('click', function(e) {
				e.preventDefault();
				window.top.document.preview.open($(this).attr('href'));
			});
		});");

		$images    = $this->get('images');
		$documents = $this->get('documents');
		$folders   = $this->get('folders');
		$state     = $this->get('state');

		// Check for invalid folder name
		if (empty($state->folder))
		{
			$dirname = Request::getVar('folder', '', '', 'string');
			if (!empty($dirname))
			{
				$dirname = htmlspecialchars($dirname, ENT_COMPAT, 'UTF-8');
				if (Lang::hasKey('COM_MEDIA_ERROR_UNABLE_TO_BROWSE_FOLDER_WARNDIRNAME'))
				{
					throw new Exception(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_BROWSE_FOLDER_WARNDIRNAME', $dirname), 100);
				}
				else
				{
					throw new Exception(sprintf('Unable to browse:&#160;%s. Directory name must only contain alphanumeric characters and no spaces.', $dirname), 100);
				}
			}
		}

		$this->baseURL = Request::root();
		$this->assignRef('images', $images);
		$this->assignRef('documents', $documents);
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

	function setDoc($index = 0)
	{
		if (isset($this->documents[$index]))
		{
			$this->_tmp_doc = &$this->documents[$index];
		}
		else
		{
			$this->_tmp_doc = new \Hubzero\Base\Object;
		}
	}
}
