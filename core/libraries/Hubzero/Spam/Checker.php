<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam;

use Hubzero\Spam\Detector\DetectorInterface;
use Hubzero\Spam\StringProcessor\StringProcessorInterface;
use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Base\Traits\ErrorBag;
use Exception;

/**
 * Spam checker
 */
class Checker
{
	use ErrorBag;

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
	protected $stringProcessor = null;

	/**
	 * Constructor
	 *
	 * @param   object  $stringProcessor
	 * @return  void
	 */
	public function __construct($stringProcessor = null)
	{
		if ($stringProcessor)
		{
			$this->setStringProcessor($stringProcessor);
		}
	}

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
	 * Get string processor
	 *
	 * @return  object
	 */
	public function getStringProcessor()
	{
		return $this->stringProcessor;
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
			$msg  = null;

			try
			{
				if ($detector->detect($data))
				{
					$spam = true;

					if ($detector->message())
					{
						$messages[] = $detector->message();
					}

					$failure++;
				}

				$msg = $detector->message();
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());
			}

			$this->mark($id, $spam, $msg);
		}

		$result = new Result($failure > 0, $messages);

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
			'ip'         => null,
			'user_agent' => null
		), $data);

		$data['original_text'] = $data['text'];

		if ($this->stringProcessor)
		{
			$data['text'] = $this->stringProcessor->prepare($data['text']);
		}

		return $data;
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
}
