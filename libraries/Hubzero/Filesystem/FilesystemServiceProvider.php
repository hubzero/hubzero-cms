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

namespace Hubzero\Filesystem;

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