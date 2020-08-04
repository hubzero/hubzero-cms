<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;
use Hubzero\Filesystem\Util;
use App;

/**
 * Parameter to display a list of the layouts for a component view from the extension or default template overrides.
 */
class ComponentLayouts extends Select
{
	/**
	 * @var  string
	 */
	protected $_name = 'ComponentLayouts';

	/**
	 * Get the options for the list.
	 *
	 * @param   object  &$node  XMLElement node object containing the settings for the element
	 * @return  array
	 */
	protected function _getOptions(&$node)
	{
		$options = array();
		$path1 = null;
		$path2 = null;

		// Load template entries for each menuid
		$db = App::get('db');

		$query = $db->getQuery()
			->select('template')
			->from('#__template_styles')
			->whereEquals('client_id', 0)
			->whereEquals('home', 1)
			->limit(1);
		$db->setQuery($query->toString());
		$template = $db->loadResult();

		$view = (string) $node['view'];
		$extn = (string) $node['extension'];
		if ($view && $extn)
		{
			$view = preg_replace('#\W#', '', $view);
			$extn = preg_replace('#\W#', '', $extn);
			$path1 = PATH_CORE . '/components/' . $extn . '/site/views/' . $view . '/tmpl';
			$path2 = PATH_ROOT . '/templates/' . $template . '/html/' . $extn . '/' . $view;
			$options[] = Builder\Select::option('', App::get('language')->txt('JOPTION_USE_MENU_REQUEST_SETTING'));
		}

		if ($path1 && $path2)
		{
			$path1 = Util::normalizePath($path1);
			$path2 = Util::normalizePath($path2);

			$files = App::get('filesystem')->files($path1, '^[^_]*\.php$');
			foreach ($files as $file)
			{
				$options[] = Builder\Select::option(App::get('filesystem')->extension($file));
			}

			if (is_dir($path2) && $files = App::get('filesystem')->files($path2, '^[^_]*\.php$'))
			{
				$options[] = Builder\Select::optgroup(App::get('language')->txt('JOPTION_FROM_DEFAULT_TEMPLATE'));
				foreach ($files as $file)
				{
					$options[] = Builder\Select::option(App::get('filesystem')->extension($file));
				}
				$options[] = Builder\Select::optgroup(App::get('language')->txt('JOPTION_FROM_DEFAULT_TEMPLATE'));
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::_getOptions($node), $options);

		return $options;
	}
}
