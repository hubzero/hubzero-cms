<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Editor Image buton
 */
class plgButtonImage extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 * @since   1.5
	 * @return  void
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @param   string   $name
	 * @param   string   $asset
	 * @param   integer  $author
	 * @return  array    A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name, $asset, $author)
	{
		$params = Component::params('com_media');
		$extension = Request::getCmd('option');

		if ($asset == '')
		{
			$asset = $extension;
		}

		if (User::authorise('core.edit', $asset)
			|| User::authorise('core.create', $asset)
			|| (count(User::getAuthorisedCategories($asset, 'core.create')) > 0)
			|| (User::authorise('core.edit.own', $asset) && $author == User::get('id'))
			|| (count(User::getAuthorisedCategories($extension, 'core.edit')) > 0)
			|| (count(User::getAuthorisedCategories($extension, 'core.edit.own')) > 0 && $author == User::get('id'))
		)
		{
			$link = 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name=' . $name . '&amp;asset=' . $asset . '&amp;author=' . $author;
			Html::behavior('modal');

			$button = new \Hubzero\Base\Object;
			$button->set('modal', true);
			$button->set('link', $link);
			$button->set('text', Lang::txt('PLG_IMAGE_BUTTON_IMAGE'));
			$button->set('name', 'image');
			$button->set('options', "{handler: 'iframe', size: {x: 800, y: 500}}");

			return $button;
		}

		return false;
	}
}
