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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for handling/cleaning cached data
 */
class plgCronCache extends \Hubzero\Plugin\Plugin
{
	/**
	 * Path to cache directory
	 *
	 * @var  string
	 */
	protected $_path = null;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->_path = PATH_APP . DS . 'cache';
	}

	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'cleanSystemCss',
				'label'  => Lang::txt('PLG_CRON_CACHE_REMOVE_SYSTEM_CSS'),
				'params' => ''
			),
			array(
				'name'   => 'trashExpiredData',
				'label'  => Lang::txt('PLG_CRON_CACHE_TRASH_EXPIRED_DATA'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Trash all expired cache data
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function trashExpiredData(\Components\Cron\Models\Job $job)
	{
		if (!is_dir($this->_path))
		{
			return;
		}

		Cache::gc();

		return true;
	}

	/**
	 * Clean out old system CSS files
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function cleanSystemCss(\Components\Cron\Models\Job $job)
	{
		if (!is_dir($this->_path))
		{
			return;
		}

		$docs = array();

		$dirIterator = new DirectoryIterator($this->_path);
		foreach ($dirIterator as $file)
		{
			if ($file->isDot() || $file->isDir())
			{
				continue;
			}

			if ($file->isFile())
			{
				$name = $file->getFilename();

				$ext = Filesystem::extension($name);

				if (('cvs' == strtolower($name))
				 || ('.svn' == strtolower($name))
				 || ($ext != 'css'))
				{
					continue;
				}

				if (substr($name, 0, strlen('system-')) != 'system-')
				{
					continue;
				}

				$docs[$this->_path . DS . $name] = $name;
			}
		}

		if (count($docs) > 1)
		{
			foreach ($docs as $p => $n)
			{
				Filesystem::delete($p);
			}
		}

		return true;
	}
}

