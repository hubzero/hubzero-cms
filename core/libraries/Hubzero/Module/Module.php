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

namespace Hubzero\Module;

use Hubzero\Base\Object;
use Hubzero\Document\Assets;
use Hubzero\Utility\Date;
use App;

/**
 * Base class for modules
 */
class Module extends Object
{
	use \Hubzero\Base\Traits\AssetAware;
	use \Hubzero\Base\Traits\Escapable;

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $params = null;

	/**
	 * Database row
	 *
	 * @var  object
	 */
	public $module = null;

	/**
	 * Constructor
	 *
	 * @param   object  $params  Registry
	 * @param   object  $module  Database row
	 * @return  void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Get the path of a layout for this module
	 *
	 * @param   string  $layout  The layout name
	 * @return  string
	 */
	public function getLayoutPath($layout='default')
	{
		return App::get('module')->getLayoutPath($this->module->module, $layout);
	}

	/**
	 * Get the cached contents of a module
	 * caching it, if it doesn't already exist
	 *
	 * @return  string
	 */
	public function getCacheContent()
	{
		$content = '';

		if (!App::has('cache.store'))
		{
			return $content;
		}

		$debug = App::get('config')->get('debug');
		$key   = 'modules.' . $this->module->id;
		$ttl   = intval($this->params->get('cache', 0));

		if ($debug || !$ttl)
		{
			return $content;
		}

		if (!($content = App::get('cache.store')->get($key)))
		{
			ob_start();
			$this->run();
			$content = ob_get_contents();
			ob_end_clean();

			$content .= '<!-- cached ' . with(new Date('now'))->toSql() . ' -->';

			// Module time is in seconds, setLifeTime() is in minutes
			// Some module times may have been set in minutes so we
			// need to account for that.
			$ttl = (!$ttl || $ttl == 15 ?: $ttl / 60);

			App::get('cache.store')->put($key, $content, $ttl);
		}

		return $content;
	}
}

