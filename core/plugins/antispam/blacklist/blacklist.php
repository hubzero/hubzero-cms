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

// No direct access
defined('_JEXEC') or die;

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
		include_once(__DIR__ . DS . 'Detector.php');

		$words = $this->params->get('badwords', 'viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, '
				. 'ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, '
				. 'debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, '
				. 'orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, '
				. 'porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, '
				. 'texas holdem, fisting');

		$words = explode(',', $words);
		$words = array_map('trim', $words);

		return new \Plugins\Antispam\BlackList\Detector(array('blackLists' => $words));
	}
}
