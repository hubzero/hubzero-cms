<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Document\Renderer;

/**
 * RSS is a feed that implements RSS 2.0 Specification that includes support for iTunes tags
 *
 * @see    http://www.rssboard.org/rss-specification
 */
class Rss extends \JDocumentRenderer
{
	/**
	 * Renderer mime type
	 *
	 * @var  string
	 */
	protected $_mime = 'application/rss+xml';

	/**
	 * Render the feed
	 *
	 * @access public
	 * @return	string
	 */
	public function render($name = NULL, $params = NULL, $content = NULL)
	{
		$now  = \JFactory::getDate();
		$data = $this->_doc;

		$uri = \JFactory::getURI();
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		$feed  = '<rss xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version="2.0">' . "\n";
		$feed .= '	<channel>' . "\n";
		$feed .= '		<title>' . $data->title . '</title>' . "\n";
		$feed .= '		<description>' . $data->description . '</description>' . "\n";
		$feed .= '		<link>' . $url . $data->link . '</link>' . "\n";
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

		if ($data->image!=null)
		{
			$feed .= '		<image>' . "\n";
			$feed .= '			<url>' . $data->image->url . '</url>' . "\n";
			$feed .= '			<title>' . $this->escape($data->image->title) . '</title>' . "\n";
			$feed .= '			<link>' . $data->image->link . '</link>' . "\n";
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
			$pubDate = \JFactory::getDate($data->pubDate);
			$feed .= "		<pubDate>" . $this->escape($pubDate->toRFC822()) . "</pubDate>\n";
		}
		if ($data->category != '')
		{
			$feed .= "		<category>" . $this->escape($data->category) . "</category>\n";
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
			$feed .= "		<item>\n";
			$feed .= "			<title>" . $this->escape(strip_tags($data->items[$i]->title)) . "</title>\n";
			$feed .= "			<link>" . $url . $data->items[$i]->link . "</link>\n";
			$feed .= "			<description>" . $this->_relToAbs($data->items[$i]->description) . "</description>\n";

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
			if ($data->items[$i]->itunes_image!=null)
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
			if ($data->items[$i]->category != '')
			{
				$feed .= "			<category>" . $this->escape($data->items[$i]->category) . "</category>\n";
			}
			if ($data->items[$i]->comments != '')
			{
				$feed .= "			<comments>" . $this->escape($data->items[$i]->comments) . "</comments>\n";
			}
			if ($data->items[$i]->date != '')
			{
				$itemDate = \JFactory::getDate($data->items[$i]->date);
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
		$base = \JURI::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}

	/**
	 * Escape text
	 *
	 * @param   string $text
	 * @return  string
	 */
	public function escape($text)
	{
		return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
	}
}

