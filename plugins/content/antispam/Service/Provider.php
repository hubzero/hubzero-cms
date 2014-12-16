<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Antispam\Service;

use Hubzero\Antispam\Adapter\AbstractAdapter;
use Exception;

/**
 * Really simple anti-spam adapter
 */
class Provider extends AbstractAdapter
{
	/**
	 * Constructor
	 *
	 * @param   mixed  $properties
	 * @return  void
	 */
	public function __construct($properties = null)
	{
		$this->set('linkFrequency', 5);
		$this->set('badwords', 'viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, '
				. 'ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, '
				. 'debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, '
				. 'orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, '
				. 'porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, '
				. 'texas holdem, fisting');

		if ($properties !== null)
		{
			$this->setProperties($properties);
		}
	}

	/**
	 *	Tests for spam.
	 * 
	 * @param   string  $value  Content to test
	 * @return  bool    True if the comment is spam, false if not
	 */
	public function isSpam($value = null)
	{
		if ($value)
		{
			$this->setValue($value);
		}

		if (!$this->getValue())
		{
			return false;
		}

		// Spammer IPs (banned)
		$bl = array();
		if ($ips = $this->get('blacklist'))
		{
			$bl = explode(',', $ips);
			array_map('trim', $bl);
		}

		// Bad words
		$words = $this->get('badwords');
		if ($words)
		{
			$badwords = explode(',', $words);
			array_map('trim', $badwords);
		}
		else
		{
			$badwords = array();
		}

		// Build an array of patterns to check againts
		$patterns = array('/\[url=(.*?)\](.*?)\[\/url\]/s', '/\[url=(.*?)\[\/url\]/s');
		foreach ($badwords as $badword)
		{
			if (!empty($badword))
			{
				$patterns[] = '/(.*?)' . trim($badword) . '(.*?)/is';
			}
		}

		// Set the splam flag
		$spam = false;

		// Check the text against bad words
		foreach ($patterns as $pattern)
		{
			preg_match_all($pattern, $this->getValue(), $matches);
			if (count($matches[0]) >= 1)
			{
				$spam = true;
			}
		}

		// Check the number of links in the text
		// Very unusual to have 5 or more - usually only spammers
		if (!$spam)
		{
			$num = substr_count($this->getValue(), 'http://');
			if ($num >= intval($this->get('linkFrequency'))) // too many links
			{
				$spam = true;
			}
		}

		// Check the user's IP against the blacklist
		$ip = \JRequest::ip();
		if (in_array($ip, $bl))
		{
			$spam = true;
		}

		return $spam;
	}
}