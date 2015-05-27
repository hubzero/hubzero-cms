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
	 * Regex for matching links
	 */
	const URL_REGEX = "!((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)!";

	/**
	 * Regex for word detection
	 *
	 * @var  string
	 */
	protected $regex;

	/**
	 * Constructor
	 *
	 * @param   mixed  $properties
	 * @return  void
	 */
	public function __construct($properties = null)
	{
		$this->set('linkFrequency', 5);
		$this->set('linkRatio', 40);
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

		$spam = false;

		if (!$this->getValue())
		{
			return $spam;
		}

		// Check the user's IP against the blacklist
		if ($ips = $this->get('blacklist'))
		{
			$spam = $this->blacklistedIp($ips);
		}

		// Bad words
		if (!$spam && $this->get('badwords'))
		{
			$spam = $this->pottyMouth($this->getValue());
		}

		// Check the number of links in the text
		if (!$spam && $this->get('linkFrequency'))
		{
			$spam = $this->linkRife($this->getValue());
		}

		return $spam;
	}

	/**
	 * Run text through IP checker
	 *
	 * @param   string   $ips
	 * @return  boolean
	 */
	public function blacklistedIp($ips)
	{
		// Spammer IPs (banned)
		if ($ips)
		{
			$bl = explode(',', $ips);
			array_map('trim', $bl);

			// Check the user's IP against the blacklist
			$ip = \JRequest::ip();

			if (in_array($ip, $bl))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Run text through bad word checker
	 *
	 * @param   string   $text
	 * @return  boolean
	 */
	public function pottyMouth($text)
	{
		if (!$this->regex)
		{
			$blackLists = explode(',', $this->get('badwords'));
			array_map('trim', $blackLists);

			$blackLists[] = '#\[url=(.*?)\](.*?)\[\/url\]#';
			$blackLists[] = '#\[url=(.*?)\[\/url\]#';

			$this->regex = sprintf('~%s~', implode('|', array_map(function ($value)
			{
				if (isset($value[0]) && $value[0] == '#')
				{
					$value = substr($value, 1, -1);
				}
				else
				{
					$value = preg_quote($value);
				}

				return '(?:' . $value . ')';
			}, $blackLists)));
		}

		return (bool) preg_match($this->regex, $text);
	}

	/**
	 * Run text through link rife detector
	 *
	 * @param   string   $text
	 * @return  boolean
	 */
	public function linkRife($text)
	{
		// We only need the text
		$text = strip_tags($text);
		$text = str_replace(array('&amp;', '&nbsp;'), array('&', ' '), $text);
		$text = html_entity_decode($text);

		preg_match_all(self::URL_REGEX, $text, $matches);
		$linkCount = count($matches[0]);

		$wordCount = str_word_count($text, 0, 'http: //');

		if ($linkCount >= $this->get('linkFrequency'))
		{
			// If the link count is more than the maximum allowed
			// the string is automatically considered spam..
			return true;
		}

		if ($this->get('linkValidation'))
		{
			foreach ($matches[0] as $match)
			{
				if ($this->isBlacklistedLink($match))
				{
					return true;
				}
			}
		}

		// Get the ratio of words to link
		$ratio = floor(($linkCount / $wordCount) * 100);

		return $ratio >= $this->get('linkRatio');
	}

	/**
	 * Check if a URL is in SpamHaus' registry
	 *
	 * @param   string   $input  URL to check
	 * @return  boolean
	 */
	protected function isBlacklistedLink($input)
	{
		if (!function_exists('dns_get_record'))
		{
			return false;
		}

		$parsed = parse_url($input);

		if (!isset($parsed['host']))
		{
			return false;
		}

		// Remove www. from domain (but not from www.com)
		$parsed['host'] = preg_replace('/^www\.(.+\.)/i', '$1', $parsed['host']);

		// The 3 major blacklists
		$blacklists = array(
			'zen.spamhaus.org',
			'multi.surbl.org',
			'black.uribl.com',
		);

		// Check against each black list, exit if blacklisted
		foreach ($blacklists as $i => $blacklist)
		{
			// SpamHaus requires the IP be reversed
			if ($i == 0)
			{
				$parsed['host'] = implode('.', array_reverse(explode('.', $parsed['host']), false));
			}
			$domain = $parsed['host'] . '.' . $blacklist . '.';
			$record = dns_get_record($domain);

			if (count($record) > 0)
			{
				return true;
			}
		}

		return false;
	}
}