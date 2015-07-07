<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
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
class MediaViewImages extends JViewLegacy
{
	function display($tpl = null)
	{
		Html::behavior('framework', true);

		\Hubzero\Document\Assets::addComponentScript('com_media', 'popup-imagemanager.js');
		\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'popup-imagemanager.css');

		// Display form for FTP credentials?
		// Don't set them here, as there are other functions called before this one if there is any file write operation
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session     = App::get('session');
		$this->config      = Component::params('com_media');
		$this->state       = $this->get('state');
		$this->folderList  = $this->get('folderList');
		$this->require_ftp = $ftp;

		parent::display($tpl);
	}
}
