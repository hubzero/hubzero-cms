<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Plugin\Plugin;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for file Jupyter notebooks
 */
class plgHandlersIpynb extends Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Determines if the given collection can be handled by this plugin
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to assess
	 * @return  boolean
	 **/
	public function canHandle(Hubzero\Filesystem\Collection $collection)
	{
		// We can handle 1 file
		$need = [
			'ipynb' => 1
		];

		// Check extension to make sure we can proceed
		if (!$collection->hasExtensions($need))
		{
			return false;
		}

		return true;
	}

	/**
	 * Handles view events for files
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to view
	 * @return  mixed
	 **/
	public function onHandleView(Hubzero\Filesystem\Collection $collection)
	{
		if (!$this->canHandle($collection))
		{
			return false;
		}

		$file = $collection->findFirstWithExtension('ipynb');

		if (!$file || !($file instanceof Hubzero\Filesystem\File))
		{
			return false;
		}

		// Create view
		$view = $this->view('view', 'ipynb');

		$view->file = $file;

		return $view;
	}
}
