<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
