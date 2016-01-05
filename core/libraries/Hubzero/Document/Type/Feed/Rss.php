<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
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
	public function render($name = NULL, $params = NULL, $content = NULL)
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
		$feed .= '		<title>' . $data->title . '</title>' . "\n";
		$feed .= '		<description><![CDATA[' . $data->description . ']]></description>' . "\n";
		$feed .= '		<link>' . str_replace(' ', '%20', $url . ltrim($data->link, '/')) . '</link>' . "\n";
		$feed .= '		<lastBuildDate>' . $this->escape($now->toRFC822()) . '</lastBuildDate>' . "\n";
		$feed .= '		<generator>' . $data->getGenerator() . '</generator>' . "\n";

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
			$feed .= '			<itunes:email>' . $data->itunes_owner->email . '</itunes:email>' . "\n";;
			$feed .= '		</itunes:owner>' . "\n";
		}
		if ($data->itunes_explicit != '')
		{
			$feed .= '		<itunes:explicit>' . $data->itunes_explicit . '</itunes:explicit>' . "\n";
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
			$feed .= '		<itunes:image href="' . $data->itunes_image->url . '" />' . "\n";
		}
		// end iTunes specific tags

		if ($data->image != null)
		{
			$feed .= '		<image>' . "\n";
			$feed .= '			<url>' . $data->image->url . '</url>' . "\n";
			$feed .= '			<title>' . $this->escape($data->image->title) . '</title>' . "\n";
			$feed .= '			<link>' . str_replace(' ', '%20', $data->image->link) . '</link>' . "\n";
			if ($data->image->width != "")
			{
				$feed .= '			<width>' . $data->image->width . '</width>' . "\n";
			}
			if ($data->image->height != '')
			{
				$feed .= '			<height>' . $data->image->height . '</height>' . "\n";
			}
			if ($data->image->description != '')
			{
				$feed .= '			<description><![CDATA[' . $data->image->description . ']]></description>' . "\n";
			}
			$feed .= '		</image>' . "\n";
		}
		if ($data->language != '')
		{
			$feed .= "		<language>" . $data->language . "</language>\n";
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
			if ((strpos($data->items[$i]->link, 'http://') === false) and (strpos($data->items[$i]->link, 'https://') === false))
			{
				$data->items[$i]->link = str_replace(' ', '%20', $url . ltrim($data->items[$i]->link, '/'));
			}

			$feed .= "		<item>\n";
			$feed .= "			<title>" . $this->escape(strip_tags($data->items[$i]->title)) . "</title>\n";
			$feed .= "			<link>" . str_replace(' ', '%20', $data->items[$i]->link) . "</link>\n";
			$feed .= "			<description>" . $this->_relToAbs($data->items[$i]->description) . "</description>\n";

			if (empty($data->items[$i]->guid) === true)
			{
				$feed .= "			<guid isPermaLink=\"true\">" . str_replace(' ', '%20', $data->items[$i]->link) . "</guid>\n";
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
				$feed .= "			<itunes:explicit>" . $data->items[$i]->itunes_explicit . "</itunes:explicit>\n";
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
				$feed .= "				<url>" . $data->items[$i]->itunes_image->url . "</url>\n";
				$feed .= "				<title>" . $this->escape($data->items[$i]->itunes_image->title) . "</title>\n";
				$feed .= "				<link>" . $data->items[$i]->itunes_image->link . "</link>\n";
				if ($data->items[$i]->itunes_image->width != '')
				{
					$feed .= "			<width>" . $data->items[$i]->itunes_image->width . "</width>\n";
				}
				if ($data->items[$i]->itunes_image->height != '')
				{
					$feed .= "			<height>" . $data->items[$i]->itunes_image->height . "</height>\n";
				}
				if ($data->items[$i]->itunes_image->description != '')
				{
					$feed .= "			<description><![CDATA[" . $data->items[$i]->itunes_image->description . "]]></description>\n";
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
			if ($data->items[$i]->guid != '')
			{
				$feed .= "			<guid>" . $this->escape($data->items[$i]->guid) . "</guid>\n";
			}
			if ($data->items[$i]->enclosure != NULL)
			{
				$feed .= '			<enclosure url="' . $data->items[$i]->enclosure->url . '" length="' . $data->items[$i]->enclosure->length . '" type="' . $data->items[$i]->enclosure->type . '"/>' . "\n";
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
		return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
	}
}

