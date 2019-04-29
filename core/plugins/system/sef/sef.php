<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * SEF Plugin
 */
class plgSystemSef extends \Hubzero\Plugin\Plugin
{
	/**
	 * Converting the site URL to fit to the HTTP request
	 *
	 * @return  bool
	 */
	public function onAfterRender()
	{
		if (!App::isSite() || !Config::get('sef'))
		{
			return true;
		}

		// Replace src links
		$base   = Request::base(true) . '/';
		$buffer = App::get('response')->getContent();

		$regex  = '#href="index.php\?([^"]*)#m';
		$buffer = preg_replace_callback($regex, array('plgSystemSef', 'route'), $buffer);
		$this->checkBuffer($buffer);

		$protocols = '[a-zA-Z0-9]+:'; //To check for all unknown protocals (a protocol must contain at least one alpahnumeric fillowed by :
		$regex  = '#(src|href|poster)="(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
		$buffer = preg_replace($regex, "$1=\"$base\$2\"", $buffer);
		$this->checkBuffer($buffer);

		// Onclick
		$regex  = '#(onclick="window.open\(\')(?!/|' . $protocols . '|\#)([^/]+[^\']*?\')#m';
		$buffer = preg_replace($regex, '$1' . $base . '$2', $buffer);
		$this->checkBuffer($buffer);

		// ONMOUSEOVER / ONMOUSEOUT
		$regex  = '#(onmouseover|onmouseout)="this.src=([\']+)(?!/|' . $protocols . '|\#|\')([^"]+)"#m';
		$buffer = preg_replace($regex, '$1="this.src=$2' . $base . '$3$4"', $buffer);
		$this->checkBuffer($buffer);

		// Background image
		$regex  = '#style\s*=\s*[\'\"](.*):\s*url\s*\([\'\"]?(?!/|'.$protocols.'|\#)([^\)\'\"]+)[\'\"]?\)#m';
		$buffer = preg_replace($regex, 'style="$1: url(\'' . $base . '$2$3\')', $buffer);
		$this->checkBuffer($buffer);

		// OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag
		$regex  = '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		$buffer = preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $buffer);
		$this->checkBuffer($buffer);

		// OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag
		$regex  = '#(<param\s+[^>]*)value\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
		$buffer = preg_replace($regex, '<param value="' . $base . '$2" name="$3"', $buffer);
		$this->checkBuffer($buffer);

		// OBJECT data="xx" attribute -- fix it only in the object tag
		$regex  = '#(<object\s+[^>]*)data\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
		$buffer = preg_replace($regex, '$1data="' . $base . '$2"$3', $buffer);
		$this->checkBuffer($buffer);

		App::get('response')->setContent($buffer);

		return true;
	}

	/**
	 * Check buffer
	 *
	 * @param   mixed  $buffer
	 * @return  void
	 */
	private function checkBuffer($buffer)
	{
		if ($buffer === null)
		{
			switch (preg_last_error())
			{
				case PREG_BACKTRACK_LIMIT_ERROR:
					$message = 'PHP regular expression limit reached (pcre.backtrack_limit)';
					break;
				case PREG_RECURSION_LIMIT_ERROR:
					$message = 'PHP regular expression limit reached (pcre.recursion_limit)';
					break;
				case PREG_BAD_UTF8_ERROR:
					$message = 'Bad UTF8 passed to PCRE function';
					break;
				default:
					$message = 'Unknown PCRE error calling PCRE function';
			}

			App::abort(500, $message);
		}
	}

	/**
	 * Replaces the matched tags
	 *
	 * @param   array   $matches  An array of matches (see preg_match_all)
	 * @return  string
	 */
	protected static function route(&$matches)
	{
		$url = $matches[1];
		$url = str_replace('&amp;', '&', $url);

		return 'href="' . Route::url('index.php?' . $url);
	}
}
