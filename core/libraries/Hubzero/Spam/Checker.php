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
use Hubzero\Error\Exception\RuntimeException;

/**
 * Spam checker
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
	 * Holds list of detectors and what they reported
	 *
	 * @var  array
	 */
	protected $report = array();

	/**
	 * StringProcessorInterface
	 *
	 * @var  object
	 */
	protected $stringProcessor;

	/**
	 * Log data?
	 *
	 * @var  boolean
	 */
	protected $logging = true;

	/**
	 * Set string processor
	 *
	 * @param   object  $stringProcessor
	 * @return  void
	 */
	public function setStringProcessor(StringProcessorInterface $stringProcessor)
	{
		$this->stringProcessor = $stringProcessor;
	}

	/**
	 * Set logging
	 *
	 * @param   bool  $log
	 * @return  void
	 */
	public function setLogging($log)
	{
		$this->logging = (bool) $log;
	}

	/**
	 * Checks if a string is spam or not
	 *
	 * @param   string|array  $data
	 * @return  object
	 */
	public function check($data)
	{
		$failure  = 0;
		$messages = array();

		if (is_string($data))
		{
			$data = array('text' => $data);
		}

		$data = $this->prepareData($data);

		foreach ($this->detectors as $id => $detector)
		{
			$spam = false;

			if ($detector->detect($data))
			{
				$spam = true;

				if ($detector->message())
				{
					$messages[] = $detector->message();
				}

				$failure++;
			}

			$this->mark($id, $spam, $detector->message());
		}

		$result = new Result($failure > 0, $messages);

		if ($this->logging)
		{
			$this->log($result->isSpam(), $data);
		}

		return $result;
	}

	/**
	 * Registers a Spam Detector
	 *
	 * @param   object  $spamDetector  SpamDetectorInterface
	 * @return  object  SpamDetector
	 * @throws  RuntimeException
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
	 * Get IP address
	 *
	 * @return  string
	 */
	public function getReport()
	{
		return $this->report;
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
			'username'   => null,
			'id'         => null,
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
		return \App::get('request')->ip();
	}

	/**
	 * Get User Agent
	 *
	 * @return  string
	 */
	protected function getUserAgent()
	{
		return \App::get('request')->getVar('HTTP_USER_AGENT', null, 'server');
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

		return $class;
	}

	/**
	 * Report the results of a spam detector 
	 *
	 * @param   string   $name     Name of the detector
	 * @param   boolean  $value    If spam or not
	 * @param   string   $message  Message set by detector
	 * @return  string
	 */
	protected function mark($name, $value, $message = null)
	{
		$this->report[] = array(
			'service' => $name,
			'is_spam' => $value,
			'message' => $message
		);
	}

	/**
	 * Log results of the check
	 *
	 * @param   string  $isSpam  Spam detection result
	 * @param   array   $data    Data being checked
	 * @return  void
	 */
	protected function log($isSpam, $data)
	{
		if (!\App::has('log'))
		{
			return;
		}

		$request = \App::get('request');

		$fallback  = 'option=' . $request->getCmd('option');
		$fallback .= '&controller=' . $request->getCmd('controller');
		$fallback .= '&task=' . $request->getCmd('task');

		$from = $request->getVar('REQUEST_URI', $fallback, 'server');
		$from = $from ?: $fallback;

		$info = array(
			($isSpam ? 'spam' : 'ham'),
			$data['ip'],
			$data['id'],
			$data['username'],
			md5($data['text']),
			$from
		);

		\App::get('log')
			->logger('spam')
			->info(implode(' ', $info));
	}
}
