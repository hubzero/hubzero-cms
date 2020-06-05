<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Babajispam Anti-spam Plugin
 */
class plgAntispamBabajispam extends \Hubzero\Plugin\Plugin
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

		return new \Plugins\Antispam\Babajispam\Detector();
	}
}
