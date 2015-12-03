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

namespace Hubzero\Config;

use Hubzero\Config\Exception\UnsupportedFormatException;
use Hubzero\Config\Exception\FileNotFoundException;

/**
 * Repository class
 */
class Legacy extends Registry
{
	/**
	 * The current client type (admin, site, api, etc).
	 *
	 * @var  string
	 */
	protected $path;

	/**
	 * The current client type (admin, site, api, etc).
	 *
	 * @var  string
	 */
	protected $map = array(
		'app' => array(
			'application_env',
			'editor',
			'list_limit',
			'helpurl',
			'debug',
			'debug_lang',
			'feed_limit',
			'feed_email',
			'secret',
			'gzip',
			'error_reporting',
			'api_server',
			'xmlrpc_server',
			'log_path',
			'tmp_path',
			'live_site',
			'force_ssl',
			'offset',
			'sitename',
			'robots',
			'captcha',
			'access'
		),
		'cache' => array(
			'caching',
			'cachetime',
			'cache_handler',
			'memcache_settings'
		),
		'database' => array(
			'dbtype',
			'host',
			'user',
			'password',
			'db',
			'dbcharset',
			'dbcollation',
			'dbprefix'
		),
		'ftp' => array(
			'ftp_enabled',
			'ftp_host',
			'ftp_port',
			'ftp_user',
			'ftp_pass',
			'ftp_root'
		),
		'mail' => array(
			'mailer',
			'mailfrom',
			'fromname',
			'smtpauth',
			'smtphost',
			'smtpport',
			'smtpuser',
			'smtppass',
			'smtpsecure',
			'sendmail'
		),
		'meta' => array(
			'MetaAuthor',
			'MetaTitle',
			'MetaDesc',
			'MetaKeys',
			'MetaRights',
			'MetaVersion'
		),
		'offline' => array(
			'display_offline_message',
			'offline_image',
			'offline_message',
			'offline'
		),
		'seo' => array(
			'sef',
			'sef_rewrite',
			'sef_suffix',
			'sef_groups',
			'unicodeslugs',
			'sitename_pagetitles'
		),
		'session' => array(
			'session_handler',
			'lifetime',
			'cookiesubdomains',
			'cookie_path',
			'cookie_domain'
		)
	);

	/**
	 * Create a new configuration repository.
	 *
	 * @param   string  $path
	 * @return  void
	 */
	public function __construct($path = null)
	{
		if (!$path)
		{
			$path = PATH_ROOT;
		}

		$this->file = $path . DS . 'configuration.php';

		if ($this->file)
		{
			$data = $this->read($this->file);
			$data = \Hubzero\Utility\Arr::fromObject($data);

			$config = array();

			foreach ($data as $key => $value)
			{
				foreach ($this->map as $group => $values)
				{
					if (!isset($config[$group]))
					{
						$config[$group] = array();
					}
					if (in_array($key, $values))
					{
						$config[$group][$key] = $value;
					}
				}
			}

			parent::__construct($config);
		}
	}

	/**
	 * Determine if the given configuration exists.
	 *
	 * @return  bool
	 */
	public function exists()
	{
		return file_exists($this->file);
	}

	/**
	 * Determine if the given configuration value exists.
	 *
	 * @param   string  $file     Path to file to load
	 * @return  bool
	 */
	public function read($file)
	{
		if (!file_exists($file) || (filesize($file) < 10))
		{
			throw new FileNotFoundException('No configuration file found and no installation code available.', 500);
		}

		require_once $file;

		if (!class_exists('\\JConfig'))
		{
			throw new UnsupportedFormatException('Invalid configuration file.', 500);
		}

		$config = new \JConfig;

		if (isset($config->tmp_path))
		{
			if (substr($config->tmp_path, strlen(PATH_ROOT)) == DS . 'tmp')
			{
				$config->tmp_path = PATH_APP . substr($config->tmp_path, strlen(PATH_ROOT));
			}
		}

		if (isset($config->log_path))
		{
			if (substr($config->log_path, strlen(PATH_ROOT)) == DS . 'logs')
			{
				$config->log_path = PATH_APP . substr($config->log_path, strlen(PATH_ROOT));
			}
		}

		return $config;
	}

	/**
	 * Split the config file into new format
	 *
	 * @param   string  $format
	 * @param   string  $path
	 * @return  void
	 */
	public function split($format = null, $path = null)
	{
		$format = $format ?: 'php';
		$path   = $path   ?: PATH_APP . DS . 'config';

		$writer = new \Hubzero\Config\FileWriter(
			$format,
			$path
		);

		foreach ($this->map as $group => $values)
		{
			$contents = array();
			foreach ($values as $key)
			{
				$contents[$key] = $this->get($group . '.' . $key);
			}

			$writer->write($contents, $group);
		}
	}

	/**
	 * Write the contents of the legacy config
	 *
	 * @param   string  $file
	 * @return  bool
	 */
	public function update($file = null)
	{
		$file = ($file ?: $this->file);

		$contents = $this->toString('PHP', array('class' => 'JConfig', 'closingtag' => false));
		$original = '0640';

		if (is_file($file))
		{
			// Track original permissions
			$original = '0' . decoct(fileperms($file) & 0777);

			// First try and make sure the file is writable
			@chmod($file, octdec('0640'));
		}

		$result = file_put_contents($file, $contents);

		if (is_file($file))
		{
			@chmod($file, octdec($original));
		}

		return $result;
	}
}
