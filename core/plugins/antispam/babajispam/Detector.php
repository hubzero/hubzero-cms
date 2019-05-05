<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Babajispam;

use Hubzero\Spam\Detector\DetectorInterface;
use Exception;

/**
 * Spam detector for Babajispam
 */
class Detector implements DetectorInterface
{
	/**
	 * Message
	 *
	 * @var  string
	 */
	protected $message;

	/**
	 * Constructor
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		$this->message = '';

		if (isset($options['message']))
		{
			$this->message = $options['message'];
		}
	}

	/**
	 * Checks the text if it contains any word that is blacklisted.
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data)
	{
		$context  = $data['text'];
		$email    = $data['email'];
		$username = $data['username'];

		$spam = 0;
		$reason = 0;

		// International phone number match (let match be a little fuzzy)
		// This is the payload of babaji spam so gets you right on the edge of
		// of being marked spam. Pretty much any other rule hit should
		// trigger marking this as spam.
		if (preg_match("/(^|[^\d])(([\s\-\+]*\d[\s\-\+]*) {11,12})([^\d\-\+]|$)/", $context))
		{
			$spam += 50;
			$reason |= 1;
		}

		// Spammer like to include variants of the name Babaji in the spam
		$baba = array("/(^|\s)baba(\s|$)/","/(^|\s)ji(\s|$)/","/(^|\s)b.{0,3}a.{0,3}b.{0,3}a.{0,4}j.{0,3}i(\s|$)/");

		foreach ($baba as $b)
		{
			if ((($b{0} == '/') && preg_match($b, $context))
			 || (($b[0] != '/') && strpos($context, $b) !== false))
			{
				$spam += 10;
				$reason |= 2;
			}

			if ((($b[0] == '/') && preg_match($b, $email))
			 || (($b[0] != '/') && strpos($email, $b) !== false))
			{
				$spam += 10;
				$reason |= 4;
			}

			if ((($b[0] == '/') && preg_match($b, $username))
			 || (($b[0] != '/') && strpos($username, $b) !== false))
			{
				$spam += 10;
				$reason |= 8;
			}
		}

		// Spammer likes to include various obfuscated texts
		$keywords = array(
			"ßåßå", "Vå§hïkåråñ", "Lðvê", "§þê¢ïålï§†", "þrðßlêm", "Mµ†hkårñï", "jï", "Pℝℴℬℒℰℳ)","mðhïñï", "vå§hïkåråñ",
			"vå§hïKÄRÄñ", "mårrïågê", "§ðlµ", "†ïðñ§", "Äll", "vððÐðð", "ßLåÇk", "MåGïÇ",
			"/Black\-{0,1}Magic/i","Haryana","Ambala"
		);

		foreach ($keywords as $k)
		{
			if ((($k[0] == '/') && preg_match($k, $context))
			 || (($k[0] != '/') && strpos($context, $k) !== false))
			{
				$spam += 10;
				$reason |= 16;
			}
			if ((($k[0] == '/') && preg_match($k, $email))
			 || (($k[0] != '/') && strpos($email, $k) !== false))
			{
				$spam += 10;
				$reason |= 32;
			}
			if ((($k[0] == '/') && preg_match($k, $username))
			 || (($k[0] != '/') && strpos($username, $k) !== false))
			{
				$spam += 10;
				$reason |= 64;
			}
		}

		// This is to catch phone number plus little content (unique word count < 5)
		if (count(array_unique(str_word_count($context, 1))) < 5)
		{
			$spam += 10;
			$reason |= 128;
		}

		$reasons = '';

		for ($i = 7; $i >= 0; $i--)
		{
			$mask = 1 << $i;

			$reasons .= ($reason & $mask) ? 'X' : 'O';
		}

		$reasons .= '-' . $spam;

		$this->message = $reasons;

		return ($spam > 60);
	}

	/**
	 * Return set message
	 *
	 * @return  string
	 */
	public function message()
	{
		return $this->message;
	}
}
