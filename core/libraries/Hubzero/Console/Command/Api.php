<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Application;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Group command class
 **/
class Api extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Revokes all oauth related tokens/codes
	 * 
	 * @return void
	 */
	public function revokeAll()
	{
		$this->revokeAccessTokens();
		$this->revokeRefreshTokens();
		$this->revokeAuthorizationCodes();
	}

	/**
	 * Delete all access tokens
	 *
	 * @return void
	 **/
	public function revokeAccessTokens()
	{
		// Get db object
		$db = App::get('db');

		// Attempt to delete tokens
		try
		{
			$db->setQuery("DELETE FROM `#__developer_access_tokens`");
			$db->query();
		}
		catch (QueryFailedException $e)
		{
			$this->output->error('Error:' . $e->getMessage());
		}

		// Successfully deleted tokens
		$this->output->addLine('All access tokens successfully revoked.', 'success');
	}

	/**
	 * Delete all refresh tokens
	 *
	 * @return void
	 **/
	public function revokeRefreshTokens()
	{
		// Get db object
		$db = App::get('db');

		// Attempt to delete tokens
		try
		{
			$db->setQuery("DELETE FROM `#__developer_refresh_tokens`");
			$db->query();
		}
		catch (QueryFailedException $e)
		{
			$this->output->error('Error:' . $e->getMessage());
		}

		// Successfully deleted tokens
		$this->output->addLine('All refresh tokens successfully revoked.', 'success');
	}

	/**
	 * Delete all authorization codes
	 *
	 * @return void
	 **/
	public function revokeAuthorizationCodes()
	{
		// Get db object
		$db = App::get('db');

		// Attempt to delete tokens
		try
		{
			$db->setQuery("DELETE FROM `#__developer_authorization_codes`");
			$db->query();
		}
		catch (QueryFailedException $e)
		{
			$this->output->error('Error:' . $e->getMessage());
		}

		// Successfully deleted tokens
		$this->output->addLine('All authorization codes successfully revoked.', 'success');
	}

	/**
	 * Generate documentation for API
	 * 
	 * @return void
	 */
	public function generateDocumentation()
	{
		// Generate documentation
		$generator     = new \Hubzero\Api\Doc\Generator();
		$documentation = $generator->output('array', true);

		// Output error messages
		foreach ($documentation['errors'] as $error)
		{
			$this->output->addLine($error , 'error');
		}

		// Successfully processed the following files
		foreach ($documentation['files'] as $file)
		{
			$this->output->addLine('Successfully processed the file: ' . $file , 'success');
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Api Related Commands.'
			);
	}
}