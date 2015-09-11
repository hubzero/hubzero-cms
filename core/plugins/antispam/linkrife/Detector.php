<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\LinkRife;

use Hubzero\Spam\Detector\DetectorInterface;

/**
 * LinkRife : Link Overflow Detector
 *
 * Spam Detector that detects if a string contains
 * too many links. Inspired by work from 
 * Laju Morrison <morrelinko@gmail.com>
 */
class Detector implements DetectorInterface
{
	/**
	 * Regex for matching links
	 */
	const URL_REGEX = "!((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)!";

	/**
	 * Maximum number of links allowed in a text
	 * before it is considered spam.
	 *
	 * @var  integer
	 */
	protected $maxLinkAllowed = 10;

	/**
	 * Ratio (In Percentage) of the number of links
	 * to the number of words in the string. If the
	 * percentage ratio is greater than the specified
	 * ratio, it is considered a "Link Overflow"
	 *
	 * @var  integer
	 */
	protected $maxRatio = 40;

	/**
	 * Validate found links?
	 *
	 * @var  boolean
	 */
	protected $linkValidation = 0;

	/**
	 * Message
	 *
	 * @var  string
	 */
	protected $message = '';

	/**
	 * Constructor
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		if (isset($options['maxLinkAllowed']))
		{
			$this->setMaxLinkAllowed($options['maxLinkAllowed']);
		}

		if (isset($options['maxRatio']))
		{
			$this->setMaxRatio($options['maxRatio']);
		}

		if (isset($options['linkValidation']))
		{
			$this->setLinkValidation($options['linkValidation']);
		}

		$this->message = '';
	}

	/**
	 * Sets the maximum number of links allowed in a text
	 * before it is considered spam.
	 *
	 * @param   integer  $count
	 * @return  object
	 */
	public function setMaxLinkAllowed($count)
	{
		$this->maxLinkAllowed = $count;

		return $this;
	}

	/**
	 * Get the max number of links allowed
	 *
	 * @return  integer
	 */
	public function getMaxLinkAllowed()
	{
		return $this->maxLinkAllowed;
	}

	/**
	 * Set the max ratio (of links to text)
	 *
	 * @param   integer  $ratio
	 * @return  object
	 */
	public function setMaxRatio($ratio)
	{
		$this->maxRatio = $ratio;

		return $this;
	}

	/**
	 * Get the max ratio (of links to text)
	 *
	 * @return  integer
	 */
	public function getMaxRatio()
	{
		return $this->maxRatio;
	}

	/**
	 * Set the link validation setting
	 *
	 * @param   integer  $validate
	 * @return  object
	 */
	public function setLinkValidation($validate)
	{
		$this->linkValidation = $validate;

		return $this;
	}

	/**
	 * Get the link validation setting
	 *
	 * @return  integer
	 */
	public function getLinkValidation()
	{
		return $this->linkValidation;
	}

	/**
	 * Check if a URL is in SpamHaus' registry
	 *
	 * @param   string   $input  URL to check
	 * @return  boolean
	 */
	public function isBlacklisted($input)
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

	/**
	 * {@inheritDocs}
	 */
	public function detect($data)
	{
		// We only need the text
		$text = strip_tags($data['text']);
		$text = str_replace(array('&amp;', '&nbsp;'), array('&', ' '), $text);
		$text = html_entity_decode($text);

		preg_match_all(self::URL_REGEX, $text, $matches);
		$linkCount = count($matches[0]);

		$wordCount = str_word_count($text, 0, 'http: //');
		$wordCount = ($wordCount <= 0 ? 1 : $wordCount);

		if ($linkCount >= $this->maxLinkAllowed)
		{
			// If the link count is more than the maximum allowed
			// the string is automatically considered spam..
			$this->message = 'Exceeded maximum links allowed.';
			return true;
		}

		if ($this->linkValidation)
		{
			foreach ($matches[0] as $match)
			{
				if ($this->isBlacklisted($match))
				{
					$this->message = 'Detected blacklisted link.';
					return true;
				}
			}
		}

		// Get the ratio of words to link
		$ratio = floor(($linkCount / $wordCount) * 100);

		if ($ratio >= $this->maxRatio)
		{
			$this->message = 'Exceeded link-to-text ratio.';
			return true;
		}

		return false;
	}

	/**
	 * {@inheritDocs}
	 */
	public function message()
	{
		return $this->message;
	}
}
