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

namespace Plugins\Antispam\Bayesian;

use Plugins\Antispam\Bayesian\Table\MessageHash;
use Plugins\Antispam\Bayesian\Table\TokenCount;
use Plugins\Antispam\Bayesian\Table\TokenProb;
use Hubzero\Spam\Detector\DetectorInterface;
use Exception;
use stdClass;

include_once(__DIR__ . DS . 'Table' . DS . 'MessageHash.php');
include_once(__DIR__ . DS . 'Table' . DS . 'TokenCount.php');
include_once(__DIR__ . DS . 'Table' . DS . 'TokenProb.php');

/**
 * Bayesian filter
 */
class Detector implements DetectorInterface
{
	/**
	 * Constants
	 */
	const GOOD_TOKEN_WEIGHT       = 2;
	const MIN_TOKEN_COUNT         = 0;
	const MIN_COUNT_FOR_INCLUSION = 5;
	const MIN_SCORE               = 0.011;
	const MAX_SCORE               = 0.99;
	const LIKELY_SPAM_SCORE       = 0.9998;
	const CERTAIN_SPAM_SCORE      = 0.9999;
	const CERTAIN_SPAM_COUNT      = 10;
	const INTERSTING_WORD_COUNT   = 15;

	/**
	 * Ratio (In Percentage) of the number of links
	 * to the number of words in the string. If the
	 * percentage ratio is greater than the specified
	 * ratio, it is considered a "Link Overflow"
	 *
	 * @var  int
	 */
	protected $threshold = 0.95;

	/**
	 * Holds the file that stores blacklisted words
	 *
	 * @var  null
	 */
	protected $db = null;

	/**
	 * Message
	 *
	 * @var  string
	 */
	protected $message = '';

	/**
	 * Constructor
	 *
	 * @param   mixed  $properties
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		if (isset($options['threshold']))
		{
			$this->setThreshold($options['threshold']);
		}

		if (!isset($options['db']))
		{
			$options['db'] = \App::get('db');
		}

		$this->setDbo($options['db']);

		$this->message = '';
	}

	/**
	 * Set database connection
	 *
	 * @param   string  $file
	 * @return  object
	 * @throws  Exception
	 */
	public function setDbo($db)
	{
		$this->db = $db;

		return $this;
	}

	/**
	 * Get database connection
	 *
	 * @return  object
	 */
	public function getDbo()
	{
		return $this->db;
	}

	/**
	 * Set the threshold
	 *
	 * @param   float   $threshold
	 * @return  object
	 */
	public function setThreshold($threshold)
	{
		$this->threshold = $threshold;

		return $this;
	}

	/**
	 * Get the threshold
	 *
	 * @return float
	 */
	public function getThreshold()
	{
		return $this->threshold;
	}

	/**
	 *	Tests for spam.
	 * 
	 * @param   string  $value  Content to test
	 * @return  bool    True if the comment is spam, false if not
	 */
	public function detect($data)
	{
		// We only need the text
		$text = strip_tags($data['text']);
		$text = str_replace(array('&amp;', '&nbsp;'), array('&', ' '), $text);
		$text = html_entity_decode($text);

		$score = 0;

		$words_count = preg_match_all('/([a-zA-Z]\w+)\W*/', $text, $words);

		$tokens = $this->getTokensProb($words[1]);
		$tokens_prob = array();
		foreach ($words[1] as $word)
		{
			foreach ($tokens as $token)
			{
				if ($token->token == $word)
				{
					$tokens_prob[] = $token;
					break;
				}
			}
		}

		usort($tokens_prob, array($this, 'compareToken'));
		$index = 0;
		$mult = 1.0;
		$comb = 1.0;

		foreach ($tokens_prob as $token)
		{
			$prob = $token->prob;
			$mult = $mult * $prob;
			$comb = $comb * (1 - $prob);
			$index++;
			if ($index >= self::INTERSTING_WORD_COUNT)
			{
				break;
			}
		}

		if ($mult + $comb > 0.0000001)
		{
			$score = $mult / ($mult + $comb);
		}

		if ($score >= (float) $this->getThreshold())
		{
			return true;
		}

		return false;
	}

	/**
	 * Compare token
	 *
	 * @param   object   $token1
	 * @param   object   $token2
	 * @return  integer
	 */
	protected function compareToken($token1, $token2)
	{
		$interest1 = 0.5 - abs(0.5 - $token1->prob);
		$interest2 = 0.5 - abs(0.5 - $token2->prob);

		if ($interest1 < $interest2)
		{
			return -1;
		}
		else if ($interest1 > $interest2)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Get token probability
	 *
	 * @param   array  $words
	 * @return  array
	 */
	protected function getTokensProb($words)
	{
		if (count($words) == 0)
		{
			return null;
		}

		$tbl = new TokenProb($this->getDbo());
		return $tbl->find('list', array('token' => $words));
	}

	/**
	 * Get token count
	 *
	 * @return  object
	 */
	protected function getTokensCount()
	{
		$tbl = new TokenCount($this->getDbo());
		$obj = $tbl->find('first');

		if (!$obj)
		{
			$obj = new stdClass();
			$obj->good_count = 0;
			$obj->bad_count  = 0;
		}

		return $obj;
	}

	/**
	 * Description...
	 *
	 * @param   string   $text
	 * @return  boolean
	 */
	protected function isTextCalculated($text)
	{
		$hash = sha1($text);

		$tbl = new MessageHash($this->getDbo());

		if (!$tbl->find('count', array('hash' => $hash)))
		{
			$tbl->hash = $hash;
			$tbl->store();

			return false;
		}

		return true;
	}

	/**
	 * Learn what is spam or not
	 *
	 * @param   string   $text
	 * @param   string   $isSpam
	 * @return  boolean
	 */
	public function learn($text, $isSpam)
	{
		if ($this->isTextCalculated($text))
		{
			return false;
		}
		$words_count = preg_match_all('/([a-zA-Z]\w+)\W*/', $text, $matches);
		$words = array();

		$tokens_count = $this->getTokensCount();

		foreach ($matches[1] as $match)
		{
			if (array_key_exists($match, $words))
			{
				$words[$match]++;
			}
			else
			{
				$words[$match] = 1;
			}
		}
		foreach ($words as $token => $count)
		{
			if ($isSpam)
			{
				$this->calculateTokenProbality($token, 0, $count, $tokens_count);
			}
			else
			{
				$this->calculateTokenProbality($token, $count, 0, $tokens_count);
			}
		}
		return true;
	}

	/**
	 * Description...
	 *
	 * @param   string   $text
	 * @return  boolean
	 */
	protected function removeFromCalculatedText($text)
	{
		$hash = sha1($text);

		$tbl = new MessageHash($this->getDbo());

		if (!$tbl->find('count', array('hash' => $hash)))
		{
			return false;
		}

		$tbl->deleteByHash($hash);

		return true;
	}

	/**
	 * Forget a piece of text
	 *
	 * @param   string   $text
	 * @param   integer  $isSpam
	 * @return  void
	 */
	public function forget($text, $isSpam)
	{
		if (!$this->removeFromCalculatedText($text))
		{
			return;
		}

		$words_count = preg_match_all('/([a-zA-Z]\w+)\W*/', $text, $matches);
		$words = array();

		$tokens_count = $this->getTokensCount();

		foreach ($matches[1] as $match)
		{
			if (array_key_exists($match, $words))
			{
				$words[$match]++;
			}
			else
			{
				$words[$match] = 1;
			}
		}

		foreach ($words as $token => $count)
		{
			if ($is_spam)
			{
				$this->calculateTokenProbality($token, 0, -1 * $count, $tokens_count);
			}
			else
			{
				$this->calculateTokenProbality($token, -1 * $count, 0, $tokens_count);
			}
		}
	}

	/**
	 * Calculate token probability
	 *
	 * @param   object   $token
	 * @param   integer  $good_count
	 * @param   integer  $bad_count
	 * @param   integer  $tokens_count
	 * @return  void
	 */
	protected function calculateTokenProbality($token, $good_count, $bad_count, $tokens_count)
	{
		$g = $good_count;
		$b = $bad_count;
		$found = false;

		$token_prob = new TokenProb($this->getDbo());
		$token_prob->loadByToken($token);

		if ($token_prob->id)
		{
			$g += (int)$token_prob->in_ham;
			$b += (int)$token_prob->in_spam;
			$found = true;
		}

		$g *= self::GOOD_TOKEN_WEIGHT;

		if ($g + $b >= self::MIN_COUNT_FOR_INCLUSION)
		{
			$goodfactor = min(1, ((float)$g)/((float)$tokens_count->good_count));
			$badfactor  = min(1, ((float)$b)/((float)$tokens_count->bad_count));
			$prob = max(self::MIN_SCORE, min(self::MAX_SCORE, $badfactor / ($goodfactor + $badfactor)));
			if ($g == 0)
			{
				$prob = ($b > self::CERTAIN_SPAM_COUNT) ? self::CERTAIN_SPAM_SCORE : self::LIKELY_SPAM_SCORE;
			}

			if (!$found)
			{
				$this->increaseTokenCount($good_count, $bad_count);
			}

			if ($token_prob->id)
			{
				$token_prob->prev_prob = $token_prob->prob;
			}
			$token_prob->prob    = (float)$prob;
			$token_prob->token   = $token;
			$token_prob->in_ham  = (int)$token_prob->in_ham + (int)$good_count;
			$token_prob->in_spam = (int)$token_prob->in_spam + (int)$bad_count;
			$token_prob->store();
		}
	}

	/**
	 * Increase token count
	 *
	 * @param   integer  $good_count
	 * @param   integer  $bad_count
	 * @return  boolean
	 */
	protected function increaseTokenCount($good_count, $bad_count)
	{
		$tbl = new TokenCount($this->getDbo());

		if ($obj = $tbl->find('first'))
		{
			$tbl->bind($obj);
		}

		$tbl->good_count = (int)$tbl->good_count + (int)$good_count;
		$tbl->bad_count  = (int)$tbl->bad_count + (int)$bad_count;
		$tbl->store();
	}

	/**
	 * {@inheritDocs}
	 */
	public function message()
	{
		return $this->message;
	}
}