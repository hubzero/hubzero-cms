<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Antispam plugin for a basic Bayesian filter
 */
class plgAntispamBayesian extends \Hubzero\Plugin\Plugin
{
	/**
	 * Instantiate and return a spam detector.
	 *
	 * @return  object  Hubzero\Spam\Detector\DetectorInterface
	 * @since   1.3.2
	 */
	public function onAntispamDetector()
	{
		include_once __DIR__ . DS . 'Detector.php';

		$bayesian = new \Plugins\Antispam\Bayesian\Detector();
		$bayesian->setThreshold($this->params->get('threshold', 0.95));

		return $bayesian;
	}

	/**
	 * Event for training spam
	 *
	 * @param  string   $content  The content to train on
	 * @param  boolean  $isSpam   If the content is spam or not
	 * @since  1.3.2
	 */
	public function onAntispamTrain($content, $isSpam)
	{
		if (!$content)
		{
			return;
		}

		if (!$this->params->get('learn', 1))
		{
			return;
		}

		$bayesian = new \Plugins\Antispam\Bayesian\Detector();
		$bayesian->setThreshold($this->params->get('threshold', 0.95));

		$bayesian->learn($content, $isSpam);
	}
}
