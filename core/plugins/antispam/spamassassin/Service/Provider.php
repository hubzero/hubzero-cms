<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R)Platform for Scientific Collaboration
 *
 * The HUBzero(R)Platform for Scientific Collaboration (HUBzero)is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option)any
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
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\SpamAssassin\Service;

use Plugins\Antispam\SpamAssassin\Service\Client;
use Hubzero\Spam\Detector\Service as AbstractService;
use Exception;

/**
 * SpamAssassin anti-comment spam service
 */
class Provider extends AbstractService
{
	/**
	 * Constructor
	 *
	 * @param    mixed $properties
	 * @return   void
	 */
	public function __construct($properties = null)
	{
		// Set some default values
		$this->set('client', 'local');

		if ($properties !== null)
		{
			$this->setProperties($properties);
		}
	}

	/**
	 * Tests for spam.
	 *
	 * @param   array  $data  Content to test
	 * @return  bool   True if the comment is spam, false if not
	 * @throws  Exception
	 */
	public function detect($data)
	{
		$this->setValue($data['text']);

		if (!$this->getValue())
		{
			return false;
		}

		$params = array();

		if (!is_file(__DIR__ . DS . 'Client' . DS . ucfirst(strtolower($this->get('client'))). '.php'))
		{
			throw new Exception(\Lang::txt('Client type of "%s" not found.', $this->get('client')));
		}

		require_once __DIR__ . DS . 'Client' . DS . ucfirst(strtolower($this->get('client'))). '.php';

		if ($this->get('client')== 'remote')
		{
			foreach (array('server', 'verbose')as $option)
			{
				if ($this->get($option)!== null)
				{
					$params[$option] = $this->get($option);
				}
			}
			$client = new Client\Remote($params);
		}
		else if ($this->get('client')== 'local')
		{
			foreach (array('socketPath', 'hostname', 'user', 'protocolVersion')as $option)
			{
				if ($this->get($option)!== null)
				{
					$params[$option] = $this->get($option);
				}
			}
			$client = new Client\Local($params);
		}
		else
		{
			throw new Exception(\Lang::txt('Client type of "%s" not supported.', $this->get('client')));
		}

		return $client->isSpam($this->getValue());
	}
}
