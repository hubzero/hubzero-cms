<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Feed;

use Hubzero\Document\Renderer;
use Hubzero\Utility\Date;
use App;

/**
 * A feed that implements the atom specification
 *
 * Please note that just by using this class you won't automatically
 * produce valid atom files. For example, you have to specify either an editor
 * for the feed or an author for every single feed item.
 *
 * @see  http://www.atomenabled.org/developers/syndication/atom-format-spec.php
 *
 * Inspired by Joomla's JDocumentRendererAtom class
 */
class Atom extends Renderer
{
	/**
	 * Document mime type
	 *
	 * @var  string
	 */
	protected $mime = "application/atom+xml";

	/**
	 * Render the feed.
	 *
	 * @param   string  $name     The name of the element to render
	 * @param   array   $params   Array of values
	 * @param   string  $content  Override the output of the renderer
	 * @return  string  The output of the script
	 */
	public function render($name = '', $params = null, $content = null)
	{
		$now  = new Date('now');
		$data = $this->doc;

		$url = App::get('request')->root();

		// Site root with a guaranteed trailing slash, built the way Rss.php builds
		// the same value. Request::root() already ends in one, so the rtrim and
		// re-append are belt and braces; what matters is that every join below
		// ltrims its right-hand side, so the two cannot double up.
		$root = rtrim($url, '/') . '/';

		$tz = new \DateTimeZone(App::get('config')->get('offset'));

		$syndicationURL = App::get('router')->url('&format=feed&type=atom');

		if (App::get('config')->get('sitename_pagetitles', 0) == 1)
		{
			$data->title = App::get('language')->txt('JPAGETITLE', App::get('config')->get('sitename'), $data->title);
		}
		elseif (App::get('config')->get('sitename_pagetitles', 0) == 2)
		{
			$data->title = App::get('language')->txt('JPAGETITLE', $data->title, App::get('config')->get('sitename'));
		}

		$feed_title = htmlspecialchars($data->title, ENT_COMPAT, 'UTF-8');

		$feed  = "<feed xmlns=\"http://www.w3.org/2005/Atom\" ";
		if ($data->language != "")
		{
			$feed .= " xml:lang=\"" . htmlspecialchars((string) $data->language, ENT_COMPAT, 'UTF-8') . "\"";
		}
		$feed .= ">\n";
		$feed .= "	<title type=\"text\">" . $feed_title . "</title>\n";
		$feed .= "	<subtitle type=\"text\">" . htmlspecialchars($data->description, ENT_COMPAT, 'UTF-8') . "</subtitle>\n";
		if (empty($data->category) === false)
		{
			if (is_array($data->category))
			{
				foreach ($data->category as $cat)
				{
					$feed .= "	<category term=\"" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "\" />\n";
				}
			}
			else
			{
				$feed .= "	<category term=\"" . htmlspecialchars($data->category, ENT_COMPAT, 'UTF-8') . "\" />\n";
			}
		}
		$feed .= "	<link rel=\"alternate\" type=\"text/html\" href=\"" . $this->_escapeUrl($url) . "\"/>\n";
		$feed .= "	<id>" . $this->_escapeUrl(str_replace(' ', '%20', $data->getBase())) . "</id>\n";
		$feed .= "	<updated>" . htmlspecialchars($now->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</updated>\n";
		if ($data->editor != "")
		{
			$feed .= "	<author>\n";
			$feed .= "		<name>" . htmlspecialchars((string) $data->editor, ENT_COMPAT, 'UTF-8') . "</name>\n";
			if ($data->editorEmail != "")
			{
				$feed .= "		<email>" . htmlspecialchars($data->editorEmail, ENT_COMPAT, 'UTF-8') . "</email>\n";
			}
			$feed .= "	</author>\n";
		}
		$feed .= "	<generator uri=\"http://hubzero.org\" version=\"2.5\">" . $this->escape($data->getGenerator()) . "</generator>\n";
		$feed .= '	<link rel="self" type="application/atom+xml" href="' . $this->_escapeUrl(str_replace(' ', '%20', $root . ltrim((string) $syndicationURL, '/'))) . "\"/>\n";

		for ($i = 0, $count = count($data->items); $i < $count; $i++)
		{
			// An item link may be absolute: Route::url() returns one when asked,
			// and Rss.php has always tested for that before prefixing, so the
			// renderers are written to accept it. Atom prefixed unconditionally,
			// which turns an absolute link into "https://hub/https://hub/entry".
			// Test the PREFIX rather than "contains", since a relative link may
			// legitimately carry "http://" inside its query string, and trim
			// both sides so the join cannot produce a doubled slash.
			$itemLink = (string) $data->items[$i]->link;

			if (!preg_match('#^https?://#i', $itemLink))
			{
				$itemLink = $root . ltrim($itemLink, '/');
			}

			$itemLink = str_replace(' ', '%20', $itemLink);

			$feed .= "	<entry>\n";
			$feed .= "		<title>" . htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8') . "</title>\n";
			$feed .= '		<link rel="alternate" type="text/html" href="' . $this->_escapeUrl($itemLink) . "\"/>\n";

			if ($data->items[$i]->date == "")
			{
				$data->items[$i]->date = $now->toUnix();
			}
			$itemDate = new Date($data->items[$i]->date);
			$itemDate->setTimeZone($tz);

			$feed .= "		<published>" . htmlspecialchars($itemDate->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</published>\n";
			$feed .= "		<updated>" . htmlspecialchars($itemDate->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</updated>\n";
			if (empty($data->items[$i]->guid) === true)
			{
				$feed .= "		<id>" . $this->_escapeUrl($itemLink) . "</id>\n";
			}
			else
			{
				$feed .= "		<id>" . htmlspecialchars($data->items[$i]->guid, ENT_COMPAT, 'UTF-8') . "</id>\n";
			}

			if ($data->items[$i]->author != "")
			{
				$feed .= "		<author>\n";
				$feed .= "			<name>" . htmlspecialchars($data->items[$i]->author, ENT_COMPAT, 'UTF-8') . "</name>\n";
				if ($data->items[$i]->authorEmail != "")
				{
					$feed .= "			<email>" . htmlspecialchars($data->items[$i]->authorEmail, ENT_COMPAT, 'UTF-8') . "</email>\n";
				}
				$feed .= "		</author>\n";
			}
			if ($data->items[$i]->description != "")
			{
				$description = $this->_unwrapCdata((string) $data->items[$i]->description);
				$feed .= "		<summary type=\"html\">" . htmlspecialchars($description, ENT_COMPAT, 'UTF-8') . "</summary>\n";
				$feed .= "		<content type=\"html\">" . htmlspecialchars($description, ENT_COMPAT, 'UTF-8') . "</content>\n";
			}
			if (empty($data->items[$i]->category) === false)
			{
				if (is_array($data->items[$i]->category))
				{
					foreach ($data->items[$i]->category as $cat)
					{
						$feed .= "		<category term=\"" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "\" />\n";
					}
				}
				else
				{
					$feed .= "		<category term=\"" . htmlspecialchars($data->items[$i]->category, ENT_COMPAT, 'UTF-8') . "\" />\n";
				}
			}
			if ($data->items[$i]->enclosure != null)
			{
				$feed .= "		<link rel=\"enclosure\" href=\"" . $this->_escapeUrl($data->items[$i]->enclosure->url) . "\" type=\"" . $this->escape($data->items[$i]->enclosure->type) . "\"  length=\"" . $this->escape($data->items[$i]->enclosure->length) . "\" />\n";
			}
			$feed .= "	</entry>\n";
		}
		$feed .= "</feed>\n";
		return $feed;
	}

	/**
	 * Escape a URL for an XML element or attribute.
	 *
	 * Feed producers set link and url fields from Route::url(), whose $xhtml
	 * argument defaults to true -- so the value arrives already entity-encoded
	 * and escaping it again turns "&amp;" into "&amp;amp;", which corrupts the
	 * query string of every link in the feed whenever SEF is off. Other
	 * producers hand over a raw URL, which must be escaped or a bare "&" makes
	 * the document not well-formed. $double_encode = false covers both: it
	 * escapes a bare "&" and leaves an existing entity alone.
	 *
	 * @param   string  $url
	 * @return  string
	 */
	protected function _escapeUrl($url)
	{
		return htmlspecialchars((string) $url, ENT_COMPAT, 'UTF-8', false);
	}

	/**
	 * Strip a producer-supplied CDATA wrapper
	 *
	 * Some producers (blog, tags, member/group blogs) hand over the description
	 * already wrapped in CDATA for the RSS renderer; here the text is
	 * entity-escaped, so the wrapper would appear literally.
	 *
	 * @param   string  $text
	 * @return  string
	 */
	protected function _unwrapCdata($text)
	{
		if (preg_match('/^\s*<!\[CDATA\[(.*)\]\]>\s*$/s', $text, $m))
		{
			return $m[1];
		}
		return $text;
	}

	/**
	 * Escape text
	 *
	 * @param   string  $text
	 * @return  string
	 */
	public function escape($text)
	{
		return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
	}
}
