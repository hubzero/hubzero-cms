<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

