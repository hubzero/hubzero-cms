<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Antispam plugin for a LinkRife spam detector
 */
class plgAntispamLinkRife extends \Hubzero\Plugin\Plugin
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

		$linkrife = new \Plugins\Antispam\LinkRife\Detector();
		$linkrife->setMaxLinkAllowed($this->params->get('linkFrequency', 10));
		$linkrife->setMaxRatio($this->params->get('linkRatio', 40));
		$linkrife->setLinkValidation($this->params->get('linkValidation', 0));

		return $linkrife;
	}
}
