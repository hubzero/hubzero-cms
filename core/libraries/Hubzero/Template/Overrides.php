<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template;

use Hubzero\User\Group;

/**
 * Where template overrides of extension views and assets may come from
 *
 * A template overrides an extension's layouts and assets from its
 * html/<extension>/ directory. Inside a supergroup's pages the group's own
 * template (app/site/groups/<gid>/template) may do the same, and takes
 * precedence over the site template. This is the one place that decides
 * which template roots apply to the current request, so component and
 * plugin views, module layouts and assets all agree.
 */
class Overrides
{
	/**
	 * The supergroup whose pages are being rendered, once resolved
	 *
	 * @var  Group|false|null  null until resolved, false for none
	 */
	private static $superGroup = null;

	/**
	 * Template roots whose html/ directories may hold overrides, highest priority first
	 *
	 * @return  array
	 */
	public static function roots()
	{
		$roots = array();

		if ($path = self::superGroupTemplatePath())
		{
			$roots[] = $path;
		}

		if (\App::has('template') && !empty(\App::get('template')->path))
		{
			$roots[] = \App::get('template')->path;
		}

		return $roots;
	}

	/**
	 * The active supergroup's template directory, if it has one
	 *
	 * @return  string|null
	 */
	public static function superGroupTemplatePath()
	{
		$group = self::superGroup();

		if (!$group)
		{
			return null;
		}

		$path = PATH_APP . $group->getBasePath() . DS . 'template';

		return is_dir($path) ? $path : null;
	}

	/**
	 * The supergroup whose pages are being rendered
	 *
	 * Resolved once per request from the group the request's cn names,
	 * unless a caller that knows better has set it.
	 *
	 * @return  Group|false
	 */
	public static function superGroup()
	{
		if (self::$superGroup === null)
		{
			self::$superGroup = false;

			$cn = \App::has('request') ? \App::get('request')->getCmd('cn') : '';

			if ($cn)
			{
				$group = Group::getInstance($cn);

				if ($group && $group->isSuperGroup())
				{
					self::$superGroup = $group;
				}
			}
		}

		return self::$superGroup;
	}

	/**
	 * Set the supergroup whose pages are being rendered
	 *
	 * @param   Group|false  $group  A supergroup, or false for none
	 * @return  void
	 */
	public static function setSuperGroup($group)
	{
		self::$superGroup = $group ?: false;
	}

	/**
	 * Forget the resolved supergroup so the next lookup resolves it again
	 *
	 * @return  void
	 */
	public static function reset()
	{
		self::$superGroup = null;
	}
}
