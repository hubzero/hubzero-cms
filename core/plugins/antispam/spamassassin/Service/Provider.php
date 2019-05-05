<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
			foreach (array('server', 'verbose') as $option)
			{
				if ($this->get($option)!== null)
				{
					$params[$option] = $this->get($option);
				}
			}
			$client = new Client\Remote($params);
		}
		elseif ($this->get('client') == 'local')
		{
			foreach (array('socketPath', 'hostname', 'user', 'protocolVersion') as $option)
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
