<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Spam Assassin antispam Plugin
 */
class plgAntispamSpamassassin extends \Hubzero\Plugin\Plugin
{
	/**
	 * Instantiate and return a spam detector.
	 *
	 * @return  object  Hubzero\Spam\Detector\DetectorInterface
	 * @since   1.3.2
	 */
	public function onAntispamDetector()
	{
		include_once __DIR__ . DS . 'Service' . DS . 'Provider.php';

		$service = new \Plugins\Antispam\SpamAssassin\Service\Provider();

		$service->set('client', $this->params->get('client', 'local'))
		        ->set('hostname', $this->params->get('hostname', 'localhost'))
		        ->set('port', $this->params->get('port', 783))
		        ->set('protocolVersion', $this->params->get('protocolVersion', '1.5'))
		        ->set('socket', $this->params->get('socket'))
		        ->set('socketPath', $this->params->get('socketPath'))
		        ->set('enableZlib', $this->params->get('enableZlib', 0))
		        ->set('server', $this->params->get('server', 'http://spamcheck.postmarkapp.com/filter'))
		        ->set('verbose', $this->params->get('verbose', 0));

		return $service;
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

		if (!$this->params->get('learn', 0))
		{
			return;
		}

		include_once __DIR__ . DS . 'Service' . DS . 'Provider.php';

		$service = new \Plugins\Antispam\SpamAssassin\Service\Provider();

		$service->set('client', $this->params->get('client', 'local'))
		        ->set('hostname', $this->params->get('hostname', 'localhost'))
		        ->set('port', $this->params->get('port', 783))
		        ->set('protocolVersion', $this->params->get('protocolVersion', '1.5'))
		        ->set('socket', $this->params->get('socket'))
		        ->set('socketPath', $this->params->get('socketPath'))
		        ->set('enableZlib', $this->params->get('enableZlib', 0))
		        ->set('server', $this->params->get('server', 'http://spamcheck.postmarkapp.com/filter'))
		        ->set('verbose', $this->params->get('verbose', 0));

		$service->learn($content, $isSpam);
	}
}
