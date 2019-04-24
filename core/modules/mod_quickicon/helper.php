<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\QuickIcon;

use Hubzero\Module\Module;
use Plugin;
use Route;
use Event;
use Lang;
use User;

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
		if (!\App::isAdmin())
		{
			return;
		}

		$buttons = self::getButtons($this->params);

		include_once __DIR__ . DS . 'icons.php';

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Helper method to return button list.
	 *
	 * This method returns the array by reference so it can be
	 * used to add custom buttons or remove default ones.
	 *
	 * @param   object  $params  Registry
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
				Lang::load('mod_quickicon');

				self::$buttons[$key] = array(
					array(
						'link'   => Route::url('index.php?option=com_content&task=article.add'),
						'id'     => 'icon-article-add',
						'text'   => Lang::txt('MOD_QUICKICON_ADD_NEW_ARTICLE'),
						'access' => array('core.manage', 'com_content', 'core.create', 'com_content', )
					),
					array(
						'link'   => Route::url('index.php?option=com_content'),
						'id'     => 'icon-article',
						'text'   => Lang::txt('MOD_QUICKICON_ARTICLE_MANAGER'),
						'access' => array('core.manage', 'com_content')
					),
					array(
						'link'   => Route::url('index.php?option=com_categories&extension=com_content'),
						'id'     => 'icon-category',
						'text'   => Lang::txt('MOD_QUICKICON_CATEGORY_MANAGER'),
						'access' => array('core.manage', 'com_content')
					),
					array(
						'link'   => Route::url('index.php?option=com_media'),
						'id'     => 'icon-media',
						'text'   => Lang::txt('MOD_QUICKICON_MEDIA_MANAGER'),
						'access' => array('core.manage', 'com_media')
					),
					array(
						'link'   => Route::url('index.php?option=com_menus'),
						'id'     => 'icon-menumgr',
						'text'   => Lang::txt('MOD_QUICKICON_MENU_MANAGER'),
						'access' => array('core.manage', 'com_menus')
					),
					array(
						'link'   => Route::url('index.php?option=com_users'),
						'id'     => 'icon-user',
						'text'   => Lang::txt('MOD_QUICKICON_USER_MANAGER'),
						'access' => array('core.manage', 'com_users')
					),
					array(
						'link'   => Route::url('index.php?option=com_modules'),
						'id'     => 'icon-module',
						'text'   => Lang::txt('MOD_QUICKICON_MODULE_MANAGER'),
						'access' => array('core.manage', 'com_modules')
					),
					array(
						'link'   => Route::url('index.php?option=com_installer'),
						'id'     => 'icon-extension',
						'text'   => Lang::txt('MOD_QUICKICON_EXTENSION_MANAGER'),
						'access' => array('core.manage', 'com_installer')
					),
					array(
						'link'   => Route::url('index.php?option=com_languages'),
						'id'     => 'icon-language',
						'text'   => Lang::txt('MOD_QUICKICON_LANGUAGE_MANAGER'),
						'access' => array('core.manage', 'com_languages')
					),
					array(
						'link'   => Route::url('index.php?option=com_config'),
						'id'     => 'icon-config',
						'text'   => Lang::txt('MOD_QUICKICON_GLOBAL_CONFIGURATION'),
						'access' => array('core.manage', 'com_config', 'core.admin', 'com_config')
					),
					array(
						'link'   => Route::url('index.php?option=com_templates'),
						'id'     => 'icon-themes',
						'text'   => Lang::txt('MOD_QUICKICON_TEMPLATE_MANAGER'),
						'access' => array('core.manage', 'com_templates')
					),
					array(
						'link'   => Route::url('index.php?option=com_members&task=edit&id=' . User::get('id')),
						'id'     => 'icon-user-profile',
						'text'   => Lang::txt('MOD_QUICKICON_PROFILE'),
						'access' => true
					),
				);
			}
			else
			{
				self::$buttons[$key] = array();
			}

			// Include buttons defined by published quickicon plugins
			Plugin::import('quickicon');

			$arrays = (array) Event::trigger('onGetIcons', array($context));

			foreach ($arrays as $response)
			{
				foreach ($response as $icon)
				{
					$default = array(
						'link'   => null,
						'id'     => '',
						'class'  => 'icon-config',
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
	 * @param   object  $params  The module parameters.
	 * @param   object  $module  The module.
	 * @return  string  The alternate title for the module.
	 */
	public static function getTitle($params, $module)
	{
		$key = $params->get('context', 'mod_quickicon') . '_title';

		if (Lang::hasKey($key))
		{
			return Lang::txt($key);
		}

		return $module->title;
	}
}
