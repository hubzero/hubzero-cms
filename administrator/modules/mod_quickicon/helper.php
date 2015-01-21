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

namespace Modules\QuickIcon;

use Hubzero\Module\Module;
use JFactory;
use JRoute;
use JText;
use JPluginHelper;

/**
 * Module class for displaying shortcut idons for common tasks
 */
class Helper extends Module
{
	/**
	 * Stack to hold buttons
	 *
	 * @var  array
	 */
	protected static $buttons = array();

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		$buttons = self::getButtons($this->params);

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Helper method to return button list.
	 *
	 * This method returns the array by reference so it can be
	 * used to add custom buttons or remove default ones.
	 *
	 * @param   object  $params  JRegistry
	 * @return  array   An array of buttons
	 */
	public static function &getButtons($params)
	{
		$key = (string) $params;

		if (!isset(self::$buttons[$key]))
		{
			$context = $params->get('context', 'mod_quickicon');
			if ($context == 'mod_quickicon')
			{
				// Load mod_quickicon language file in case this method is called before rendering the module
				JFactory::getLanguage()->load('mod_quickicon');

				self::$buttons[$key] = array(
					array(
						'link'   => JRoute::_('index.php?option=com_content&task=article.add'),
						//'image'  => 'header/icon-48-article-add.png',
						'id'     => 'icon-article-add',
						'text'   => JText::_('MOD_QUICKICON_ADD_NEW_ARTICLE'),
						'access' => array('core.manage', 'com_content', 'core.create', 'com_content', )
					),
					array(
						'link'   => JRoute::_('index.php?option=com_content'),
						//'image'  => 'header/icon-48-article.png',
						'id'     => 'icon-article',
						'text'   => JText::_('MOD_QUICKICON_ARTICLE_MANAGER'),
						'access' => array('core.manage', 'com_content')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_categories&extension=com_content'),
						//'image'  => 'header/icon-48-category.png',
						'id'     => 'icon-category',
						'text'   => JText::_('MOD_QUICKICON_CATEGORY_MANAGER'),
						'access' => array('core.manage', 'com_content')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_media'),
						//'image'  => 'header/icon-48-media.png',
						'id'     => 'icon-media',
						'text'   => JText::_('MOD_QUICKICON_MEDIA_MANAGER'),
						'access' => array('core.manage', 'com_media')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_menus'),
						//'image'  => 'header/icon-48-menumgr.png',
						'id'     => 'icon-menumgr',
						'text'   => JText::_('MOD_QUICKICON_MENU_MANAGER'),
						'access' => array('core.manage', 'com_menus')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_users'),
						//'image'  => 'header/icon-48-user.png',
						'id'     => 'icon-user',
						'text'   => JText::_('MOD_QUICKICON_USER_MANAGER'),
						'access' => array('core.manage', 'com_users')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_modules'),
						//'image'  => 'header/icon-48-module.png',
						'id'     => 'icon-module',
						'text'   => JText::_('MOD_QUICKICON_MODULE_MANAGER'),
						'access' => array('core.manage', 'com_modules')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_installer'),
						//'image'  => 'header/icon-48-extension.png',
						'id'     => 'icon-extension',
						'text'   => JText::_('MOD_QUICKICON_EXTENSION_MANAGER'),
						'access' => array('core.manage', 'com_installer')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_languages'),
						//'image'  => 'header/icon-48-language.png',
						'id'     => 'icon-language',
						'text'   => JText::_('MOD_QUICKICON_LANGUAGE_MANAGER'),
						'access' => array('core.manage', 'com_languages')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_config'),
						//'image'  => 'header/icon-48-config.png',
						'id'     => 'icon-config',
						'text'   => JText::_('MOD_QUICKICON_GLOBAL_CONFIGURATION'),
						'access' => array('core.manage', 'com_config', 'core.admin', 'com_config')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_templates'),
						//'image'  => 'header/icon-48-themes.png',
						'id'     => 'icon-themes',
						'text'   => JText::_('MOD_QUICKICON_TEMPLATE_MANAGER'),
						'access' => array('core.manage', 'com_templates')
					),
					array(
						'link'   => JRoute::_('index.php?option=com_admin&task=profile.edit&id=' . JFactory::getUser()->id),
						//'image'  => 'header/icon-48-user-profile.png',
						'id'     => 'icon-user-profile',
						'text'   => JText::_('MOD_QUICKICON_PROFILE'),
						'access' => true
					),
				);
			}
			else
			{
				self::$buttons[$key] = array();
			}

			// Include buttons defined by published quickicon plugins
			JPluginHelper::importPlugin('quickicon');
			$app = JFactory::getApplication();
			$arrays = (array) $app->triggerEvent('onGetIcons', array($context));

			foreach ($arrays as $response)
			{
				foreach ($response as $icon)
				{
					$default = array(
						'link'   => null,
						'image'  => 'header/icon-48-config.png',
						'text'   => null,
						'access' => true
					);
					$icon = array_merge($default, $icon);
					if (!is_null($icon['link']) && !is_null($icon['text']))
					{
						self::$buttons[$key][] = $icon;
					}
				}
			}
		}

		return self::$buttons[$key];
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param   object  $params  The module parameters. JRegistry
	 * @param   object  $module  The module.
	 * @return  string  The alternate title for the module.
	 */
	public static function getTitle($params, $module)
	{
		$key = $params->get('context', 'mod_quickicon') . '_title';

		if (JFactory::getLanguage()->hasKey($key))
		{
			return JText::_($key);
		}
		else
		{
			return $module->title;
		}
	}
}
