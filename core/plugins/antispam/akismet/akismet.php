<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Akismet antispam Plugin
 */
class plgAntispamAkismet extends \Hubzero\Plugin\Plugin
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

		if (!$this->params->get('apiKey'))
		{
			return;
		}

		$akismet = new \Plugins\Antispam\Akismet\Service\Provider();
		$akismet->set('apiKey', $this->params->get('apiKey'))
		        ->set('apiPort', $this->params->get('apiPort', 80))
		        ->set('akismetServer', $this->params->get('akismetServer', 'rest.akismet.com'))
		        ->set('akismetVersion', $this->params->get('akismetVersion', '1.1'));

		return $akismet;
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

		$akismet = new \Plugins\Antispam\Akismet\Service\Provider();
		$akismet->set('apiKey', $this->params->get('apiKey'))
		        ->set('apiPort', $this->params->get('apiPort', 80))
		        ->set('akismetServer', $this->params->get('akismetServer', 'rest.akismet.com'))
		        ->set('akismetVersion', $this->params->get('akismetVersion', '1.1'));

		$akismet->learn($content, $isSpam);
	}
}
