<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Models;

use Hubzero\Base\Obj;
use Filesystem;
use Config;

/**
 * Model class for getting system information
 */
class Info extends Obj
{
	/**
	 * @var  array  some php settings
	 */
	protected $php_settings = null;

	/**
	 * @var  array config values
	 */
	protected $config = null;

	/**
	 * @var  array  somme system values
	 */
	protected $info = null;

	/**
	 * @var  string  php info
	 */
	protected $php_info = null;

	/**
	 * @var  array  informations about writable state of directories
	 */
	protected $directories = null;

	/**
	 * @var  string  The current editor.
	 */
	protected $editor = null;

	/**
	 * Method to get the ChangeLog
	 *
	 * @return  array  some php settings
	 */
	public function getPhpSettings()
	{
		if (is_null($this->php_settings))
		{
			$this->php_settings = array();
			$this->php_settings['safe_mode']          = ini_get('safe_mode') == '1';
			$this->php_settings['display_errors']     = ini_get('display_errors') == '1';
			$this->php_settings['short_open_tag']     = ini_get('short_open_tag') == '1';
			$this->php_settings['file_uploads']       = ini_get('file_uploads') == '1';
			$this->php_settings['magic_quotes_gpc']   = ini_get('magic_quotes_gpc') == '1';
			$this->php_settings['register_globals']   = ini_get('register_globals') == '1';
			$this->php_settings['output_buffering']   = (bool) ini_get('output_buffering');
			$this->php_settings['open_basedir']       = ini_get('open_basedir');
			$this->php_settings['session.save_path']  = ini_get('session.save_path');
			$this->php_settings['session.auto_start'] = ini_get('session.auto_start');
			$this->php_settings['disable_functions']  = ini_get('disable_functions');
			$this->php_settings['xml']                = extension_loaded('xml');
			$this->php_settings['zlib']               = extension_loaded('zlib');
			$this->php_settings['zip']                = function_exists('zip_open') && function_exists('zip_read');
			$this->php_settings['mbstring']           = extension_loaded('mbstring');
			$this->php_settings['iconv']              = function_exists('iconv');
		}
		return $this->php_settings;
	}

	/**
	 * Method to get the config
	 *
	 * @return  array  Config values
	 */
	public function getConfig()
	{
		if (is_null($this->config))
		{
			$config = Config::toArray();
			$this->config = array();

			$blur = array('host', 'user', 'password', 'ftp_user', 'ftp_pass', 'smtpuser', 'smtppass', 'secret');

			foreach ($config as $section => $data)
			{
				if (is_array($data))
				{
					foreach ($data as $key => $value)
					{
						if (in_array($key, $blur)
						 || substr($key, -strlen('password')) == 'password'
						 || substr($key, -strlen('secret')) == 'secret')
						{
							$value = 'xxxxxx';
						}

						$this->config[$section . '.' . $key] = $value;
					}
				}
				else
				{
					if (in_array($data, $blur)
					 || substr($data, -strlen('password')) == 'password'
					 || substr($data, -strlen('secret')) == 'secret')
					{
						$value = 'xxxxxx';
					}

					$this->config[$section] = $value;
				}
			}
		}
		return $this->config;
	}

	/**
	 * Method to get the system information
	 *
	 * @return  array  System information values
	 */
	public function getInfo()
	{
		if (is_null($this->info))
		{
			$db = \App::get('db');

			if (isset($_SERVER['SERVER_SOFTWARE']))
			{
				$sf = $_SERVER['SERVER_SOFTWARE'];
			}
			else
			{
				$sf = getenv('SERVER_SOFTWARE');
			}

			$this->info = array();
			$this->info['php']         = php_uname();
			$this->info['dbversion']   = $db->getVersion();
			$this->info['dbcollation'] = $db->getCollation();
			$this->info['phpversion']  = phpversion();
			$this->info['server']      = $sf;
			$this->info['sapi_name']   = php_sapi_name();
			$this->info['version']     = HVERSION;
			$this->info['platform']    = 'HUBzero CMS';
			$this->info['useragent']   = $_SERVER['HTTP_USER_AGENT'];
		}
		return $this->info;
	}

	/**
	 * Method to get the PHP info
	 *
	 * @return  string  PHP info
	 */
	public function getPHPInfo()
	{
		if (is_null($this->php_info))
		{
			ob_start();
			date_default_timezone_set('UTC');
			phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
			$phpinfo = ob_get_contents();
			ob_end_clean();

			preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
			$output = preg_replace('#<table[^>]*>#', '<table class="adminlist">', $output[1][0]);
			$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
			$output = preg_replace('#<hr />#', '', $output);
			$output = str_replace('<div class="center">', '', $output);
			$output = preg_replace('#<tr class="h">(.*)<\/tr>#', '<thead><tr class="h">$1</tr></thead><tbody>', $output);
			$output = str_replace('</table>', '</tbody></table>', $output);
			$output = str_replace('</div>', '', $output);

			$this->php_info = $output;
		}
		return $this->php_info;
	}

	/**
	 * Method to get the directory states
	 *
	 * @return  array  states of directories
	 */
	public function getDirectory()
	{
		if (is_null($this->directories))
		{
			$this->directories = array();

			$cparams = \Component::params('com_media');

			$app = '/' . basename(PATH_APP) . '/';

			$this->_addDirectory($app . 'components', PATH_APP . '/components');
			$this->_addDirectory($app . 'config', PATH_APP . '/config');

			$this->_addDirectory($app . $cparams->get('image_path'), PATH_APP . '/' . $cparams->get('image_path'));

			$image_folders = Filesystem::directories(PATH_APP . '/' . $cparams->get('image_path'));
			// List all images folders
			foreach ($image_folders as $folder)
			{
				$this->_addDirectory($app . $cparams->get('image_path') . $folder, PATH_APP . '/' . $cparams->get('image_path') . '/' . $folder);
			}

			$this->_addDirectory($app . 'language', PATH_APP . '/language');
			// List all site languages
			$site_langs = Filesystem::directories(PATH_APP . '/language');
			foreach ($site_langs as $slang)
			{
				$this->_addDirectory($app . 'language/' . $slang, PATH_APP . '/language/' . $slang);
			}

			$this->_addDirectory($app . 'libraries', PATH_APP . '/libraries');

			//$this->_addDirectory('media', PATH_APP . '/media');
			$this->_addDirectory($app . 'modules', PATH_APP . '/modules');
			$this->_addDirectory($app . 'plugins', PATH_APP . '/plugins');

			$plugin_groups = Filesystem::directories(PATH_APP . '/plugins');
			foreach ($plugin_groups as $folder)
			{
				$this->_addDirectory($app . 'plugins' . $folder, PATH_APP . '/plugins' . $folder);
			}

			$this->_addDirectory($app . 'templates', PATH_APP . '/templates');
			$this->_addDirectory($app . 'cache/site', PATH_APP . '/app/cache/site', 'COM_SYSTEM_INFO_CACHE_DIRECTORY');
			$this->_addDirectory($app . 'cache/admin', PATH_APP . '/app/cache/admin', 'COM_SYSTEM_INFO_CACHE_DIRECTORY');

			$this->_addDirectory(Config::get('log_path', PATH_APP . '/app/log'), Config::get('log_path', PATH_APP . '/app/log'), 'COM_SYSTEM_INFO_LOG_DIRECTORY');
			$this->_addDirectory(Config::get('tmp_path', PATH_APP . '/app/tmp'), Config::get('tmp_path', PATH_APP . '/app/tmp'), 'COM_SYSTEM_INFO_TEMP_DIRECTORY');
		}
		return $this->directories;
	}

	/**
	 * Add a directory to the list
	 *
	 * @param   string  $name
	 * @param   string  $path
	 * @param   string  $message
	 * @return  void
	 */
	private function _addDirectory($name, $path, $message = '')
	{
		$this->directories[$name] = array(
			'writable' => is_writable($path),
			'message'  => $message
		);
	}

	/**
	 * Method to get the editor
	 * has to be removed (it is present in the config...)
	 *
	 * @return  string  The default editor
	 */
	public function getEditor()
	{
		if (is_null($this->editor))
		{
			$this->editor = Config::get('editor');
		}
		return $this->editor;
	}
}
