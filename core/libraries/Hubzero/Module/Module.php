<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

		if (!App::has('cache.store') || !$this->params->get('cache'))
		{
			return $content;
		}

		$debug = App::get('config')->get('debug');
		$key   = 'modules.' . $this->module->id;
		$ttl   = intval($this->params->get('cache_time', 0));

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

			// Module time is in seconds, cache time is in minutes
			// Some module times may have been set in minutes so we
			// need to account for that.
			$ttl = $ttl <= 120 ? $ttl : ($ttl / 60);

			$foo = App::get('cache.store')->put($key, $content, $ttl);
		}

		return $content;
	}
}

