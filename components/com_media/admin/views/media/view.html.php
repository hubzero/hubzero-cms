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
class MediaViewMedia extends JViewLegacy
{
	function display($tpl = null)
	{
		$config = Component::params('com_media');

		$style  = Request::getState('media.list.layout', 'layout', 'thumbs', 'word');

		Document::setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');

		Html::behavior('framework', true);

		JHtml::_('script', 'media/mediamanager.js', true, true);
		JHtml::_('stylesheet', 'media/mediamanager.css', array(), true);
		if (Lang::isRTL()) :
			JHtml::_('stylesheet', 'media/mediamanager_rtl.css', array(), true);
		endif;

		Html::behavior('modal');
		Document::addScriptDeclaration("
		jQuery(document).ready(function($){
			document.preview = $.fancybox;
		});");

		JHtml::_('script', 'system/jquery.treeview.js', true, true, false, false);
		JHtml::_('stylesheet', 'system/jquery.treeview.css', array(), true);
		if (Lang::isRTL()) :
			JHtml::_('stylesheet', 'media/jquery.treeview_rtl.css', array(), true);
		endif;

		if (DIRECTORY_SEPARATOR == '\\')
		{
			$base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_MEDIA_BASE);
		} else {
			$base = COM_MEDIA_BASE;
		}

		$js = "
			var basepath = '".$base."';
			var viewstyle = '".$style."';
		" ;
		Document::addScriptDeclaration($js);

		// Display form for FTP credentials?
		// Don't set them here, as there are other functions called before this one if there is any file write operation
		$ftp = !JClientHelper::hasCredentials('ftp');

		$session = App::get('session');
		$state   = $this->get('state');
		$this->assignRef('session', $session);
		$this->assignRef('config', $config);
		$this->assignRef('state', $state);
		$this->require_ftp = $ftp;
		$this->folders_id = ' id="media-tree"';
		$this->folders = $this->get('folderTree');

		// Set the toolbar
		$this->addToolbar();

		parent::display($tpl);

		echo Html::behavior('keepalive');
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Set the titlebar text
		Toolbar::title(Lang::txt('COM_MEDIA'), 'mediamanager.png');

		// Add a delete button
		if (User::authorise('core.delete', 'com_media'))
		{
			$title = Lang::txt('JTOOLBAR_DELETE');
			$dhtml = "<a href=\"#\" onclick=\"MediaManager.submit('folder.delete')\" data-title=\"$title\">
						<span class=\"icon-32-delete\">$title</span>
					</a>";
			Toolbar::appendButton('Custom', $dhtml, 'delete');
			Toolbar::divider();
		}
		// Add a delete button
		if (User::authorise('core.admin', 'com_media'))
		{
			Toolbar::preferences('com_media', 450, 800, 'JToolbar_Options', '', 'window.location.reload()');
			Toolbar::divider();
		}
		Toolbar::help('media');
	}

	function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt = null;
		if (isset($folder['children']) && count($folder['children']))
		{
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}
}
