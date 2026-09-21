<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Feed;

use Hubzero\Document\Renderer;
use Hubzero\Utility\Date;

/**
 * RSS is a feed that implements RSS 2.0 Specification that includes support for iTunes tags
 *
 * @see  http://www.rssboard.org/rss-specification
 *
 * Inspired by Joomla's JDocumentRendererRss class
 */
class Rss extends Renderer
{
	/**
	 * Renderer mime type
	 *
	 * @var  string
	 */
	protected $mime = 'application/rss+xml';

	/**
	 * Render the feed.
	 *
	 * @param   string  $name     The name of the element to render
	 * @param   array   $params   Array of values
	 * @param   string  $content  Override the output of the renderer
	 * @return  string  The output of the script
	 */
	public function render($name = null, $params = null, $content = null)
	{
		$now  = new Date('now');
		$data = $this->doc;

		$url = rtrim(\App::get('request')->root(), '/') . '/';

		if (\App::get('config')->get('sitename_pagetitles', 0) == 1)
		{
			$data->title = \App::get('language')->txt('JPAGETITLE', \App::get('config')->get('sitename'), $data->title);
		}
		elseif (\App::get('config')->get('sitename_pagetitles', 0) == 2)
		{
			$data->title = \App::get('language')->txt('JPAGETITLE', $data->title, \App::get('config')->get('sitename'));
		}

		$feed  = '<rss version="2.0" xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd">' . "\n";
		//$feed  = "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
		$feed .= '	<channel>' . "\n";
		$feed .= '		<title>' . $this->escape($data->title) . '</title>' . "\n";
		$feed .= '		<description><![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $this->_unwrapCdata((string) $data->description)) . ']]></description>' . "\n";
		$feed .= '		<link>' . $this->_escapeUrl(str_replace(' ', '%20', $url . ltrim($data->link, '/'))) . '</link>' . "\n";
		$feed .= '		<lastBuildDate>' . $this->escape($now->toRFC822()) . '</lastBuildDate>' . "\n";
		$feed .= '		<generator>' . $this->escape($data->getGenerator()) . '</generator>' . "\n";

		// iTunes specific tags
		if ($data->itunes_summary != '')
		{
			$feed .= '		<itunes:summary>' . $this->escape($data->itunes_summary) . '</itunes:summary>' . "\n";
		}
		if ($data->itunes_category != '')
		{
			$feed .= '		<itunes:category text="' . $this->escape($data->itunes_category) . '">' . "\n";
			if ($data->itunes_subcategories != null)
			{
				$cats = $data->itunes_subcategories;
				foreach ($cats as $cat)
				{
					$feed .= '			<itunes:category text="' . $this->escape($cat) . '" />' . "\n";
				}
			}
			$feed .= '		</itunes:category>' . "\n";
		}
		if ($data->itunes_owner != null)
		{
			$feed .= '		<itunes:owner>' . "\n";
			$feed .= '			<itunes:name>' . $this->escape($data->itunes_owner->name) . '</itunes:name>' . "\n";
			$feed .= '			<itunes:email>' . $this->escape($data->itunes_owner->email) . '</itunes:email>' . "\n";
			$feed .= '		</itunes:owner>' . "\n";
		}
		if ($data->itunes_explicit != '')
		{
			$feed .= '		<itunes:explicit>' . $this->escape($data->itunes_explicit) . '</itunes:explicit>' . "\n";
		}
		if ($data->itunes_keywords != '')
		{
			$feed .= '		<itunes:keywords>' . $this->escape($data->itunes_keywords) . '</itunes:keywords>' . "\n";
		}
		if ($data->itunes_author != '')
		{
			$feed .= '		<itunes:author>' . $this->escape($data->itunes_author) . '</itunes:author>' . "\n";
		}
		if ($data->itunes_image != null)
		{
			$feed .= '		<itunes:image href="' . $this->_escapeUrl($data->itunes_image->url) . '" />' . "\n";
		}
		// end iTunes specific tags

		if ($data->image != null)
		{
			$feed .= '		<image>' . "\n";
			$feed .= '			<url>' . $this->_escapeUrl($data->image->url) . '</url>' . "\n";
			$feed .= '			<title>' . $this->escape($data->image->title) . '</title>' . "\n";
			$feed .= '			<link>' . $this->_escapeUrl(str_replace(' ', '%20', $data->image->link)) . '</link>' . "\n";
			if ($data->image->width != "")
			{
				$feed .= '			<width>' . $this->escape($data->image->width) . '</width>' . "\n";
			}
			if ($data->image->height != '')
			{
				$feed .= '			<height>' . $this->escape($data->image->height) . '</height>' . "\n";
			}
			if ($data->image->description != '')
			{
				$feed .= '			<description><![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $this->_unwrapCdata((string) $data->image->description)) . ']]></description>' . "\n";
			}
			$feed .= '		</image>' . "\n";
		}
		if ($data->language != '')
		{
			$feed .= "		<language>" . htmlspecialchars((string) $data->language, ENT_QUOTES, 'UTF-8') . "</language>\n";
		}
		if ($data->copyright != '')
		{
			$feed .= "		<copyright>" . $this->escape($data->copyright) . "</copyright>\n";
		}
		if ($data->editor != '')
		{
			$feed .= "		<managingEditor>" . $this->escape($data->editor) . "</managingEditor>\n";
		}
		if ($data->webmaster != '')
		{
			$feed .= "		<webMaster>" . $this->escape($data->webmaster) . "</webMaster>\n";
		}
		if ($data->pubDate != '')
		{
			$pubDate = new Date($data->pubDate);
			$feed .= "		<pubDate>" . $this->escape($pubDate->toRFC822()) . "</pubDate>\n";
		}
		if ($data->category)
		{
			if (!is_array($data->category))
			{
				$data->category = array($data->category);
			}

			foreach ($data->category as $category)
			{
				$feed .= "		<category>" . $this->escape($category) . "</category>\n";
			}
		}
		if ($data->docs != '')
		{
			$feed .= "		<docs>" . $this->escape($data->docs) . "</docs>\n";
		}
		if ($data->ttl != '')
		{
			$feed .= "		<ttl>" . $this->escape($data->ttl) . "</ttl>\n";
		}
		if ($data->rating != '')
		{
			$feed .= "		<rating>" . $this->escape($data->rating) . "</rating>\n";
		}
		if ($data->skipHours != '')
		{
			$feed .= "		<skipHours>" . $this->escape($data->skipHours) . "</skipHours>\n";
		}
		if ($data->skipDays != '')
		{
			$feed .= "		<skipDays>" . $this->escape($data->skipDays) . "</skipDays>\n";
		}

		for ($i=0; $i<count($data->items); $i++)
		{
			// Test the PREFIX, not "contains": a relative link may legitimately
			// carry "http://" inside its query string (a redirect target, say),
			// and such a link was left unprefixed and so unresolvable in a reader.
			if (!preg_match('#^https?://#i', (string) $data->items[$i]->link))
			{
				$data->items[$i]->link = str_replace(' ', '%20', $url . ltrim((string) $data->items[$i]->link, '/'));
			}

			$feed .= "		<item>\n";
			$feed .= "			<title>" . $this->escape(strip_tags($data->items[$i]->title)) . "</title>\n";
			$feed .= "			<link>" . $this->_escapeUrl(str_replace(' ', '%20', $data->items[$i]->link)) . "</link>\n";
			$feed .= "			<description><![CDATA[" . str_replace(']]>', ']]]]><![CDATA[>', $this->_unwrapCdata((string) $this->_relToAbs($data->items[$i]->description))) . "]]></description>\n";

			if (empty($data->items[$i]->guid) === true)
			{
				$feed .= "			<guid isPermaLink=\"true\">" . $this->_escapeUrl(str_replace(' ', '%20', $data->items[$i]->link)) . "</guid>\n";
			}
			else
			{
				$feed .= "			<guid isPermaLink=\"false\">" . $this->escape($data->items[$i]->guid) . "</guid>\n";
			}

			// iTunes specific tags
			if ($data->items[$i]->itunes_summary != '')
			{
				$feed .= "			<itunes:summary>" . $this->escape($data->items[$i]->itunes_summary) . "</itunes:summary>\n";
			}
			if ($data->items[$i]->itunes_duration != '')
			{
				$feed .= "			<itunes:duration>" . $this->escape($data->items[$i]->itunes_duration) . "</itunes:duration>\n";
			}
			if ($data->items[$i]->itunes_explicit != '')
			{
				$feed .= "			<itunes:explicit>" . $this->escape($data->items[$i]->itunes_explicit) . "</itunes:explicit>\n";
			}
			if ($data->items[$i]->itunes_keywords != '')
			{
				$feed .= "			<itunes:keywords>" . $this->escape($data->items[$i]->itunes_keywords) . "</itunes:keywords>\n";
			}
			if ($data->items[$i]->itunes_author != '')
			{
				$feed .= "			<itunes:author>" . $this->escape($data->items[$i]->itunes_author) . "</itunes:author>\n";
			}
			if ($data->items[$i]->itunes_category != '')
			{
				$feed .= "			<itunes:category text=\"" . $this->escape($data->items[$i]->itunes_category) . "\">\n";
				if ($data->items[$i]->itunes_subcategories != '')
				{
					$icats = $data->items[$i]->itunes_subcategories;
					foreach ($icats as $icat)
					{
						$feed .= "				<itunes:category text=\"" . $this->escape($icat) . "\">\n";
					}
				}
				$feed .= "			</itunes:category>\n";
			}
			if ($data->items[$i]->itunes_image != null)
			{
				$feed .= "			<itunes:image>\n";
				$feed .= "				<url>" . $this->_escapeUrl($data->items[$i]->itunes_image->url) . "</url>\n";
				$feed .= "				<title>" . $this->escape($data->items[$i]->itunes_image->title) . "</title>\n";
				$feed .= "				<link>" . $this->_escapeUrl($data->items[$i]->itunes_image->link) . "</link>\n";
				if ($data->items[$i]->itunes_image->width != '')
				{
					$feed .= "			<width>" . $this->escape($data->items[$i]->itunes_image->width) . "</width>\n";
				}
				if ($data->items[$i]->itunes_image->height != '')
				{
					$feed .= "			<height>" . $this->escape($data->items[$i]->itunes_image->height) . "</height>\n";
				}
				if ($data->items[$i]->itunes_image->description != '')
				{
					$feed .= "			<description><![CDATA[" . str_replace(']]>', ']]]]><![CDATA[>', $this->_unwrapCdata((string) $data->items[$i]->itunes_image->description)) . "]]></description>\n";
				}
				$feed .= "			</itunes:image>\n";
			}
			// end iTunes specific tags

			if ($data->items[$i]->author != '')
			{
				$feed .= "			<author>" . $this->escape($data->items[$i]->author) . "</author>\n";
			}
			if ($data->items[$i]->category)
			{
				if (!is_array($data->items[$i]->category))
				{
					$data->items[$i]->category = array($data->items[$i]->category);
				}

				foreach ($data->items[$i]->category as $category)
				{
					$feed .= "			<category>" . $this->escape($category) . "</category>\n";
				}
			}
			if ($data->items[$i]->comments != '')
			{
				$feed .= "			<comments>" . $this->escape($data->items[$i]->comments) . "</comments>\n";
			}
			if ($data->items[$i]->date != '')
			{
				$itemDate = new Date($data->items[$i]->date);
				$feed .= "			<pubDate>" . $this->escape($itemDate->toRFC822()) . "</pubDate>\n";
			}
			// A <guid> is already emitted above -- with isPermaLink set, as the
			// spec requires -- so this second, attribute-less one made every item
			// carry two. Readers key de-duplication on guid; a repeated element
			// is at best ignored and at worst treated as a different item.
			if ($data->items[$i]->enclosure != null)
			{
				$feed .= '			<enclosure url="' . $this->_escapeUrl($data->items[$i]->enclosure->url) . '" length="' . $this->escape($data->items[$i]->enclosure->length) . '" type="' . $this->escape($data->items[$i]->enclosure->type) . '"/>' . "\n";
			}

			$feed .= "		</item>\n";
		}
		$feed .= "	</channel>\n";
		$feed .= "</rss>\n";

		return $feed;
	}

	/**
	 * Convert links in a text from relative to absolute
	 *
	 * @return  string
	 */
	private function _relToAbs($text)
	{
		$base = \App::get('request')->base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

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

	/**
	 * Strip a CDATA wrapper that a feed producer added itself, so it is not
	 * nested inside the wrapper this renderer adds.
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
	 * Escape a URL for an XML element or attribute.
	 *
	 * Feed producers set link and url fields from Route::url(), whose $xhtml
	 * argument defaults to true -- so the value arrives already entity-encoded
	 * and escaping it again turns "&amp;" into "&amp;amp;", which corrupts the
	 * query string of every link in the feed whenever SEF is off. Other
	 * producers hand over a raw URL, which must be escaped or a bare "&" makes
	 * the document not well-formed. $double_encode = false covers both: it
	 * escapes a bare "&" and leaves an existing entity alone. Atom.php carries
	 * the same helper, for the same reason.
	 *
	 * @param   string  $url
	 * @return  string
	 */
	protected function _escapeUrl($url)
	{
		return htmlspecialchars((string) $url, ENT_COMPAT, 'UTF-8', false);
	}
}
