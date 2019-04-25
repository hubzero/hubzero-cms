<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Antispam plugin for a Black Listed word detector
 */
class plgAntispamBlackList extends \Hubzero\Plugin\Plugin
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

		$words = $this->params->get('badwords', 'viagra, xanax, phentermine, ringtones, tramadol, hydrocodone, levitra, '
				. 'ambien, vicodin, fioricet, diazepam, accarat, casino, '
				. 'fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, '
				. 'porno, videosex, hentai, kasino, kasinos, poker');

		$words = explode(',', $words);
		$words = array_map('trim', $words);

		return new \Plugins\Antispam\BlackList\Detector(array('blackLists' => $words));
	}
}
