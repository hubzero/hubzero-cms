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

namespace Hubzero\Spam;

use Hubzero\Spam\Detector\DetectorInterface;
use Hubzero\Spam\StringProcessor\StringProcessorInterface;
use RuntimeException;

/**
 * Spam detector
 */
class Checker
{
	/**
	 * Holds registered spam detectors
	 *
	 * @var  array
	 */
	protected $detectors = array();

	/**
	 * @var StringProcessorInterface
	 */
	protected $stringProcessor;

	/**
	 * @param StringProcessorInterface $stringProcessor
	 */
	public function setStringProcessor(StringProcessorInterface $stringProcessor)
	{
		$this->stringProcessor = $stringProcessor;
	}

	/**
	 * Checks if a string is spam or not
	 *
	 * @param   string|array  $data
	 * @return  object
	 */
	public function check($data)
	{
		$failure = 0;
		if (is_string($data))
		{
			$data = array('text' => $data);
		}

		$data = $this->prepareData($data);

		foreach ($this->detectors as $detector)
		{
			if ($detector->detect($data))
			{
				$failure++;
			}
		}

		return new Result($failure > 0);
	}

	/**
	 * Registers a Spam Detector
	 *
	 * @param   object  $spamDetector  SpamDetectorInterface
	 * @return  object  SpamDetector
	 * @throws  \RuntimeException
	 */
	public function registerDetector(DetectorInterface $spamDetector)
	{
		$detectorId = $this->classSimpleName($spamDetector);

		if (isset($this->detectors[$detectorId]))
		{
			throw new RuntimeException(
				sprintf('Spam Detector [%s] already registered', $detectorId)
			);
		}

		$this->detectors[$detectorId] = $spamDetector;

		return $this;
	}

	/**
	 * Gets a detector using its detector ID (Class Simple Name)
	 *
	 * @param   string  $detectorId
	 * @return  mixed   False or SpamDetectorInterface
	 */
	public function getDetector($detectorId)
	{
		if (!isset($this->detectors[$detectorId]))
		{
			return false;
		}

		return $this->detectors[$detectorId];
	}

	/**
	 * Gets a list of all spam detectors
	 *
	 * @return  array
	 */
	public function getDetectors()
	{
		return $this->detectors;
	}

	/**
	 * Used to normalize string before passing
	 * it to detectors
	 *
	 * @param   array   $data
	 * @return  string
	 */
	protected function prepareData(array $data)
	{
		$data = array_merge(array(
			'name'       => null,
			'email'      => null,
			'text'       => null,
			'ip'         => $this->getIp(),
			'user_agent' => $this->getUserAgent()
		), $data);

		$data['original_text'] = $data['text'];
		$data['text'] = $this->stringProcessor ? $this->stringProcessor->prepare($data['text']) : $data['text'];

		return $data;
	}

	/**
	 * Get IP address
	 *
	 * @return  string
	 */
	protected function getIp()
	{
		return \Request::ip();
	}

	/**
	 * Get User Agent
	 *
	 * @return  string
	 */
	protected function getUserAgent()
	{
		return \Request::getVar('HTTP_USER_AGENT', null, 'server');
	}

	/**
	 * Gets the name of a class (w. Namespaces removed)
	 *
	 * @param   mixed   $class  String (class name) or object
	 * @return  string
	 */
	protected function classSimpleName($class)
	{
		if (is_object($class))
		{
			$class = get_class($class);
		}

		return $class; //substr($class, strrpos($class, '\\') + 1);
	}
}
