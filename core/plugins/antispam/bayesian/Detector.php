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

use Plugins\Antispam\Bayesian\Models\MessageHash;
use Plugins\Antispam\Bayesian\Models\TokenCount;
use Plugins\Antispam\Bayesian\Models\TokenProb;
use Hubzero\Spam\Detector\DetectorInterface;
use Exception;
use stdClass;

include_once(__DIR__ . DS . 'models' . DS . 'MessageHash.php');
include_once(__DIR__ . DS . 'models' . DS . 'TokenCount.php');
include_once(__DIR__ . DS . 'models' . DS . 'TokenProb.php');

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
		if (isset($options['threshold']))
		{
			$this->setThreshold($options['threshold']);
		}

		$this->message = '';
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
				if ($token->get('token') == $word)
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

		return 0;
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

		return TokenProb::all()
			->whereIn('token', $words)
			->rows();
	}

	/**
	 * Get token count
	 *
	 * @return  object
	 */
	protected function getTokensCount()
	{
		$obj = TokenCount::all()
			->ordered()
			->row();

		if (!$obj->get('id'))
		{
			$obj->set('good_count', 0);
			$obj->set('bad_count', 0);
		}

		return $obj;
	}

	/**
	 * Check if a record exists for specified text
	 *
	 * @param   string   $text
	 * @return  boolean
	 */
	protected function isTextCalculated($text)
	{
		$hash = sha1($text);

		$tbl = MessageHash::oneByHash($hash);

		if (!$tbl->get('id'))
		{
			$tbl->set('hash', $hash);
			$tbl->save();

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

		$tbl = MessageHash::oneByHash($hash);

		if (!$tbl->get('id'))
		{
			return false;
		}

		$tbl->destroy();

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

		$token_prob = TokenProb::oneByToken($token);

		if ($token_prob->get('id'))
		{
			$g += (int)$token_prob->get('in_ham');
			$b += (int)$token_prob->get('in_spam');
			$found = true;
		}

		$g *= self::GOOD_TOKEN_WEIGHT;

		if ($g + $b >= self::MIN_COUNT_FOR_INCLUSION)
		{
			$goodfactor = min(1, ((float)$g)/((float)$tokens_count->get('good_count')));
			$badfactor  = min(1, ((float)$b)/((float)$tokens_count->get('bad_count')));
			$prob = max(self::MIN_SCORE, min(self::MAX_SCORE, $badfactor / ($goodfactor + $badfactor)));
			if ($g == 0)
			{
				$prob = ($b > self::CERTAIN_SPAM_COUNT) ? self::CERTAIN_SPAM_SCORE : self::LIKELY_SPAM_SCORE;
			}

			if (!$found)
			{
				$this->increaseTokenCount($good_count, $bad_count);
			}

			if ($token_prob->get('id'))
			{
				$token_prob->set('prev_prob', $token_prob->get('prob'));
			}
			$token_prob->set('prob', (float)$prob);
			$token_prob->set('token', $token);
			$token_prob->set('in_ham', (int)$token_prob->get('in_ham') + (int)$good_count);
			$token_prob->set('in_spam', (int)$token_prob->get('in_spam') + (int)$bad_count);
			$token_prob->save();
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
		$tbl = TokenCount::all()
			->ordered()
			->row();

		$tbl->set('good_count', (int)$tbl->get('good_count') + (int)$good_count);
		$tbl->set('bad_count', (int)$tbl->get('bad_count') + (int)$bad_count);
		$tbl->save();
	}

	/**
	 * {@inheritDocs}
	 */
	public function message()
	{
		return $this->message;
	}
}