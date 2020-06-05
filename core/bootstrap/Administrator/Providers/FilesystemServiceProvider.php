<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Filesystem\Filesystem;
use Hubzero\Filesystem\Adapter\Local;
use Hubzero\Filesystem\Adapter\Ftp;
use Hubzero\Filesystem\Macro\EmptyDirectory;
use Hubzero\Filesystem\Macro\Directories;
use Hubzero\Filesystem\Macro\Files;
use Hubzero\Filesystem\Macro\DirectoryTree;
use Hubzero\Base\ServiceProvider;

/**
 * Filesystem service provider
 */
class FilesystemServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['filesystem'] = function($app)
		{
			if ($app['config']->get('ftp_enable'))
			{
				$adapter = new Ftp(array(
					'host'     => $app['config']->get('ftp_host'),
					'port'     => $app['config']->get('ftp_port'),
					'username' => $app['config']->get('ftp_user'),
					'password' => $app['config']->get('ftp_pass'),
					'root'     => $app['config']->get('ftp_root'),
				));
			}
			else
			{
				$adapter = new Local($app['config']->get('virus_scanner', "clamscan -i --no-summary --block-encrypted"));
			}

			$filesystem = new Filesystem($adapter);
			$filesystem->addMacro(new EmptyDirectory)
			           ->addMacro(new Directories)
			           ->addMacro(new Files)
			           ->addMacro(new DirectoryTree);

			return $filesystem;
		};
	}
}
