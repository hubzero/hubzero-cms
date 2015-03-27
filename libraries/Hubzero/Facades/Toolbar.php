<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Facades;

/**
 * Request facade
 */
class Toolbar extends Facade
{
	/**
	 * Get the registered name.
	 *
	 * @return string
	 */
	protected static function getAccessor()
	{
		return 'toolbar';
	}

	/**
	 * Title cell.
	 * For the title and toolbar to be rendered correctly,
	 * this title fucntion must be called before the starttable function and the toolbars icons
	 * this is due to the nature of how the css has been used to postion the title in respect to the toolbar.
	 *
	 * @param   string  $title  The title.
	 * @param   string  $icon   The space-separated names of the image.
	 * @return  void
	 */
	public static function title($title, $icon = 'generic.png')
	{
		// Strip the extension.
		$icons = explode(' ', $icon);
		foreach ($icons as &$icon)
		{
			$icon = 'icon-48-' . preg_replace('#\.[^.]*$#', '', $icon);
		}

		$html = '<div class="pagetitle ' . htmlspecialchars(implode(' ', $icons)) . '"><h2>' . $title . '</h2></div>';

		$app = \JFactory::getApplication();
		$app->JComponentTitle = $html;

		$doc = \JFactory::getDocument();
		$doc->setTitle($app->getCfg('sitename') . ' - ' . \Lang::txt('JADMINISTRATION') . ' - ' . $title);
	}

	/**
	 * Writes a spacer cell.
	 *
	 * @param   string  $width  The width for the cell
	 * @return  void
	 */
	public static function spacer($width = '')
	{
		static::getRoot()->appendButton('Separator', 'spacer', $width);
	}

	/**
	 * Writes a divider between menu buttons
	 *
	 * @return  void
	 */
	public static function divider()
	{
		static::getRoot()->appendButton('Separator', 'divider');
	}

	/**
	 * Writes a custom option and task button for the button bar.
	 *
	 * @param   string  $task        The task to perform (picked up by the switch($task) blocks.
	 * @param   string  $icon        The image to display.
	 * @param   string  $iconOver    The image to display when moused over.
	 * @param   string  $alt         The alt text for the icon image.
	 * @param   bool    $listSelect  True if required to check that a standard list item is checked.
	 * @return  void
	 */
	public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		// Strip extension.
		$icon = preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button.
		static::getRoot()->appendButton('Standard', $icon, $alt, $task, $listSelect);
	}

	/**
	 * Writes a preview button for a given option (opens a popup window).
	 *
	 * @param   string  $url            The name of the popup file (excluding the file extension)
	 * @param   bool    $updateEditors
	 * @return  void
	 */
	public static function preview($url = '', $updateEditors = false)
	{
		static::getRoot()->appendButton('Popup', 'preview', 'Preview', $url.'&task=preview');
	}

	/**
	 * Writes a preview button for a given option (opens a popup window).
	 *
	 * @param   string  $ref        The name of the popup file (excluding the file extension for an xml file).
	 * @param   bool    $com        Use the help file in the component directory.
	 * @param   string  $override   Use this URL instead of any other
	 * @param   string  $component  Name of component to get Help (null for current component)
	 * @return  void
	 */
	public static function help($url, $width = 700, $height = 500)
	{
		static::getRoot()->appendButton('Help', $url, $width, $height);
	}

	/**
	 * Writes a cancel button that will go back to the previous page without doing
	 * any other operation.
	 *
	 * @param   string  $alt   Alternative text.
	 * @param   string  $href  URL of the href attribute.
	 * @return  void
	 */
	public static function back($alt = 'JTOOLBAR_BACK', $href = 'javascript:history.back();')
	{
		static::getRoot()->appendButton('Link', 'back', $alt, $href);
	}

	/**
	 * Writes a media_manager button.
	 *
	 * @param   string  $directory  The sub-drectory to upload the media to.
	 * @param   string  $alt        An override for the alt text.
	 * @return  void
	 */
	public static function media_manager($directory = '', $alt = 'JTOOLBAR_UPLOAD')
	{
		static::getRoot()->appendButton('Popup', 'upload', $alt, \Route::url('index.php?option=com_media&tmpl=component&task=popupUpload&folder=' . $directory), 800, 520);
	}

	/**
	 * Writes a common 'default' button for a record.
	 *
	 * @param   string  $task  An override for the task.
	 * @param   string  $alt   An override for the alt text.
	 * @return  void
	 */
	public static function makeDefault($task = 'default', $alt = 'JTOOLBAR_DEFAULT')
	{
		static::getRoot()->appendButton('Standard', 'default', $alt, $task, true);
	}

	/**
	 * Writes a common 'assign' button for a record.
	 *
	 * @param   string  $task  An override for the task.
	 * @param   string  $alt   An override for the alt text.
	 * @return  void
	 */
	public static function assign($task = 'assign', $alt = 'JTOOLBAR_ASSIGN')
	{
		static::getRoot()->appendButton('Standard', 'assign', $alt, $task, true);
	}

	/**
	 * Writes the common 'new' icon for the button bar.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @param	boolean	$check	True if required to check that a standard list item is checked.
	 * @since	1.0
	 */
	public static function addNew($task = 'add', $alt = 'JTOOLBAR_NEW', $check = false)
	{
		static::getRoot()->appendButton('Standard', 'new', $alt, $task, $check);
	}

	/**
	 * Writes a common 'publish' button.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @param	boolean	$check	True if required to check that a standard list item is checked.
	 * @since	1.0
	 */
	public static function publish($task = 'publish', $alt = 'JTOOLBAR_PUBLISH', $check = false)
	{
		static::getRoot()->appendButton('Standard', 'publish', $alt, $task, $check);
	}

	/**
	 * Writes a common 'publish' button for a list of records.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function publishList($task = 'publish', $alt = 'JTOOLBAR_PUBLISH')
	{
		static::getRoot()->appendButton('Standard', 'publish', $alt, $task, true);
	}

	/**
	 * Writes a common 'unpublish' button.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @param	boolean	$check	True if required to check that a standard list item is checked.
	 * @since	1.0
	 */
	public static function unpublish($task = 'unpublish', $alt = 'JTOOLBAR_UNPUBLISH', $check = false)
	{
		static::getRoot()->appendButton('Standard', 'unpublish', $alt, $task, $check);
	}

	/**
	 * Writes a common 'unpublish' button for a list of records.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function unpublishList($task = 'unpublish', $alt = 'JTOOLBAR_UNPUBLISH')
	{
		static::getRoot()->appendButton('Standard', 'unpublish', $alt, $task, true);
	}

	/**
	 * Writes a common 'archive' button for a list of records.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function archiveList($task = 'archive', $alt = 'JTOOLBAR_ARCHIVE')
	{
		static::getRoot()->appendButton('Standard', 'archive', $alt, $task, true);
	}

	/**
	 * Writes an unarchive button for a list of records.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function unarchiveList($task = 'unarchive', $alt = 'JTOOLBAR_UNARCHIVE')
	{
		static::getRoot()->appendButton('Standard', 'unarchive', $alt, $task, true);
	}

	/**
	 * Writes a common 'edit' button for a list of records.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function editList($task = 'edit', $alt = 'JTOOLBAR_EDIT')
	{
		static::getRoot()->appendButton('Standard', 'edit', $alt, $task, true);
	}

	/**
	 * Writes a common 'edit' button for a template html.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function editHtml($task = 'edit_source', $alt = 'JTOOLBAR_EDIT_HTML')
	{
		static::getRoot()->appendButton('Standard', 'edithtml', $alt, $task, true);
	}

	/**
	 * Writes a common 'edit' button for a template css.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function editCss($task = 'edit_css', $alt = 'JTOOLBAR_EDIT_CSS')
	{
		static::getRoot()->appendButton('Standard', 'editcss', $alt, $task, true);
	}

	/**
	 * Writes a common 'delete' button for a list of records.
	 *
	 * @param	string	$msg	Postscript for the 'are you sure' message.
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function deleteList($msg = '', $task = 'remove', $alt = 'JTOOLBAR_DELETE')
	{
		$bar = static::getRoot();
		// Add a delete button.
		if ($msg)
		{
			$bar->appendButton('Confirm', $msg, 'delete', $alt, $task, true);
		}
		else
		{
			$bar->appendButton('Standard', 'delete', $alt, $task, true);
		}
	}

	/**
	 * Write a trash button that will move items to Trash Manager.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @param	bool	$check
	 * @since	1.0
	 */
	public static function trash($task = 'remove', $alt = 'JTOOLBAR_TRASH', $check = true)
	{
		static::getRoot()->appendButton('Standard', 'trash', $alt, $task, $check, false);
	}

	/**
	 * Writes a save button for a given option.
	 * Apply operation leads to a save action only (does not leave edit mode).
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function apply($task = 'apply', $alt = 'JTOOLBAR_APPLY')
	{
		static::getRoot()->appendButton('Standard', 'apply', $alt, $task, false);
	}

	/**
	 * Writes a save button for a given option.
	 * Save operation leads to a save and then close action.
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function save($task = 'save', $alt = 'JTOOLBAR_SAVE')
	{
		static::getRoot()->appendButton('Standard', 'save', $alt, $task, false);
	}

	/**
	 * Writes a save and create new button for a given option.
	 * Save and create operation leads to a save and then add action.
	 *
	 * @param string $task
	 * @param string $alt
	 * @since 1.6
	 */
	public static function save2new($task = 'save2new', $alt = 'JTOOLBAR_SAVE_AND_NEW')
	{
		static::getRoot()->appendButton('Standard', 'save-new', $alt, $task, false);
	}

	/**
	 * Writes a save as copy button for a given option.
	 * Save as copy operation leads to a save after clearing the key,
	 * then returns user to edit mode with new key.
	 *
	 * @param string $task
	 * @param string $alt
	 * @since 1.6
	 */
	public static function save2copy($task = 'save2copy', $alt = 'JTOOLBAR_SAVE_AS_COPY')
	{
		static::getRoot()->appendButton('Standard', 'save-copy', $alt, $task, false);
	}

	/**
	 * Writes a checkin button for a given option.
	 *
	 * @param string $task
	 * @param string $alt
	 * @param boolean $check True if required to check that a standard list item is checked.
	 * @since 1.7
	 */
	public static function checkin($task = 'checkin', $alt = 'JTOOLBAR_CHECKIN', $check = true)
	{
		static::getRoot()->appendButton('Standard', 'checkin', $alt, $task, $check);
	}

	/**
	 * Writes a cancel button and invokes a cancel operation (eg a checkin).
	 *
	 * @param	string	$task	An override for the task.
	 * @param	string	$alt	An override for the alt text.
	 * @since	1.0
	 */
	public static function cancel($task = 'cancel', $alt = 'JTOOLBAR_CANCEL')
	{
		static::getRoot()->appendButton('Standard', 'cancel', $alt, $task, false);
	}

	/**
	 * Writes a configuration button and invokes a cancel operation (eg a checkin).
	 *
	 * @param	string	$component	The name of the component, eg, com_content.
	 * @param	int		$height		The height of the popup.
	 * @param	int		$width		The width of the popup.
	 * @param	string	$alt		The name of the button.
	 * @param	string	$path		An alternative path for the configuation xml relative to JPATH_SITE.
	 * @since	1.0
	 */
	public static function preferences($component, $height = '550', $width = '875', $alt = 'JToolbar_Options', $path = '', $onClose = '')
	{
		$component = urlencode($component);
		$path = urlencode($path);
		$top  = 0;
		$left = 0;

		static::getRoot()->appendButton('Popup', 'options', $alt, 'index.php?option=com_config&view=component&component=' . $component . '&path=' . $path . '&tmpl=component', $width, $height, $top, $left, $onClose);
	}
}