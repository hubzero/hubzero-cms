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
			'sef',
			'sef_rewrite',
			'sef_suffix',
			'sef_groups',
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
			'sitename_pagetitles',
			'robots',
			'unicodeslugs',
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
			parent::__construct($this->read($this->file));
		}
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
				$config->tmp_path = PATH_APP . DS . 'app' . substr($config->tmp_path, strlen(PATH_ROOT));
			}
		}

		if (isset($config->log_path))
		{
			if (substr($config->log_path, strlen(PATH_ROOT)) == DS . 'logs')
			{
				$config->log_path = PATH_APP . DS . 'app' . substr($config->log_path, strlen(PATH_ROOT));
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
	public function split($format, $path)
	{
		$format = $format ?: 'php';
		$path   = $path   ?: PATH_APP . DS . 'app' . DS . 'config';

		$writer = new \Hubzero\Config\FileWriter(
			$format,
			$path
		);

		foreach ($this->map as $group => $values)
		{
			$contents = array();
			foreach ($values as $key)
			{
				$contents[$key] = $this->get($key);
			}

			$writer->write($contents, $group);
		}
	}
}
