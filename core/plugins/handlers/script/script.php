<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Plugin\Plugin;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for script files
 */
class plgHandlersScript extends Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	public static $extensions = array(
		'c',
		'cpp',
		'css',
		'delphi',
		'html',
		'ini',
		'java',
		'js',
		'less',
		'php',
		'py',
		'ruby',
		'sass',
		'scss',
		'sh',
		'sql',
		'vb',
		'xml'
	);

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
			'script' => function ($ext, $files)
			{
				foreach (\plgHandlersScript::$extensions as $canHandle)
				{
					if (array_key_exists($canHandle, $ext))
					{
						return true;
					}
				}

				return false;
			},
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

		// Find the first file in the collection
		$file = null;
		foreach ($collection as $file)
		{
			if ($file instanceof Hubzero\Filesystem\File)
			{
				break;
			}
		}

		if (!$file)
		{
			return false;
		}

		$ext = $file->getExtension();

		switch ($ext)
		{
			case 'less':
			case 'scss':
			case 'css':
				$ext = 'css';
			break;

			default:
			break;
		}

		// Create view
		$view = $this->view('view', 'script');
		$view->file = $file;
		$view->ext  = $ext;

		return $view;
	}
}
