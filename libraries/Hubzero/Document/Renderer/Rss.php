<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

 /**
 * Hubzero_Document_Renderer_RSS is a feed that implements RSS 2.0 Specification that includes support for iTunes tags
 * 
 * @author      Johan Janssens <johan.janssens@joomla.org>
 * @author      Shawn Rice <zooley@purdue.edu>
 *
 * @see http://www.rssboard.org/rss-specification
 */

class Hubzero_Document_Renderer_Rss extends JDocumentRenderer
{
	/**
	 * Renderer mime type
	 *
	 * @var		string
	 * @access	private
	 */
	var $_mime = "application/rss+xml";

	/**
	 * Render the feed
	 *
	 * @access public
	 * @return	string
	 */
	public function render()
	{
		$now	=& JFactory::getDate();
		$data	=& $this->_doc;

		$uri =& JFactory::getURI();
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		$feed = "<rss xmlns:itunes=\"http://www.itunes.com/DTDs/Podcast-1.0.dtd\" version=\"2.0\">\n";
		$feed.= "	<channel>\n";
		$feed.= "		<title>".$data->title."</title>\n";
		$feed.= "		<description>".$data->description."</description>\n";
		$feed.= "		<link>".$url.$data->link."</link>\n";
		$feed.= "		<lastBuildDate>".htmlspecialchars($now->toRFC822(), ENT_COMPAT, 'UTF-8')."</lastBuildDate>\n";
		$feed.= "		<generator>".$data->getGenerator()."</generator>\n";

		// iTunes specific tags
		if ($data->itunes_summary!="") {
			$feed.= "		<itunes:summary>".htmlspecialchars($data->itunes_summary, ENT_COMPAT, 'UTF-8')."</itunes:summary>\n";
		}
		if ($data->itunes_category!="") {
			$feed.= "		<itunes:category text=\"".htmlspecialchars($data->itunes_category, ENT_COMPAT, 'UTF-8')."\">\n";
			if ($data->itunes_subcategories!=null) {
				$cats = $data->itunes_subcategories;
				foreach ($cats as $cat) 
				{
					$feed.= "			<itunes:category text=\"".htmlspecialchars($cat, ENT_COMPAT, 'UTF-8')."\" />\n";
				}
			}
			$feed.= "		</itunes:category>\n";
		}
		if ($data->itunes_owner!=null)
		{
			$feed.= "		<itunes:owner>\n";
			$feed.= "			<itunes:name>".htmlspecialchars($data->itunes_owner->name, ENT_COMPAT, 'UTF-8')."</itunes:name>\n";
			$feed.= "			<itunes:email>".$data->itunes_owner->email."</itunes:email>\n";
			$feed.= "		</itunes:owner>\n";
		}
		if ($data->itunes_explicit!="") {
			$feed.= "		<itunes:explicit>".$data->itunes_explicit."</itunes:explicit>\n";
		}
		if ($data->itunes_keywords!="") {
			$feed.= "		<itunes:keywords>".htmlspecialchars($data->itunes_keywords, ENT_COMPAT, 'UTF-8')."</itunes:keywords>\n";
		}
		if ($data->itunes_author!="") {
			$feed.= "		<itunes:author>".htmlspecialchars($data->itunes_author, ENT_COMPAT, 'UTF-8')."</itunes:author>\n";
		}
		/*if ($data->itunes_image!="") {
			$feed.= "		<itunes:image>".htmlspecialchars($data->itunes_image, ENT_COMPAT, 'UTF-8')."</itunes:image>\n";
		}*/
		if ($data->itunes_image!=null)
		{
			$feed.= "		<itunes:image>\n";
			$feed.= "			<url>".$data->itunes_image->url."</url>\n";
			$feed.= "			<title>".htmlspecialchars($data->itunes_image->title, ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".$data->itunes_image->link."</link>\n";
			if ($data->itunes_image->width != "") {
				$feed.= "			<width>".$data->itunes_image->width."</width>\n";
			}
			if ($data->itunes_image->height!="") {
				$feed.= "			<height>".$data->itunes_image->height."</height>\n";
			}
			if ($data->itunes_image->description!="") {
				$feed.= "			<description><![CDATA[".$data->itunes_image->description."]]></description>\n";
			}
			$feed.= "		</itunes:image>\n";
		}
		// end iTunes specific tags

		if ($data->image!=null)
		{
			$feed.= "		<image>\n";
			$feed.= "			<url>".$data->image->url."</url>\n";
			$feed.= "			<title>".htmlspecialchars($data->image->title, ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".$data->image->link."</link>\n";
			if ($data->image->width != "") {
				$feed.= "			<width>".$data->image->width."</width>\n";
			}
			if ($data->image->height!="") {
				$feed.= "			<height>".$data->image->height."</height>\n";
			}
			if ($data->image->description!="") {
				$feed.= "			<description><![CDATA[".$data->image->description."]]></description>\n";
			}
			$feed.= "		</image>\n";
		}
		if ($data->language!="") {
			$feed.= "		<language>".$data->language."</language>\n";
		}
		if ($data->copyright!="") {
			$feed.= "		<copyright>".htmlspecialchars($data->copyright,ENT_COMPAT, 'UTF-8')."</copyright>\n";
		}
		if ($data->editor!="") {
			$feed.= "		<managingEditor>".htmlspecialchars($data->editor, ENT_COMPAT, 'UTF-8')."</managingEditor>\n";
		}
		if ($data->webmaster!="") {
			$feed.= "		<webMaster>".htmlspecialchars($data->webmaster, ENT_COMPAT, 'UTF-8')."</webMaster>\n";
		}
		if ($data->pubDate!="") {
			$pubDate =& JFactory::getDate($data->pubDate);
			$feed.= "		<pubDate>".htmlspecialchars($pubDate->toRFC822(),ENT_COMPAT, 'UTF-8')."</pubDate>\n";
		}
		if ($data->category!="") {
			$feed.= "		<category>".htmlspecialchars($data->category, ENT_COMPAT, 'UTF-8')."</category>\n";
		}
		if ($data->docs!="") {
			$feed.= "		<docs>".htmlspecialchars($data->docs, ENT_COMPAT, 'UTF-8')."</docs>\n";
		}
		if ($data->ttl!="") {
			$feed.= "		<ttl>".htmlspecialchars($data->ttl, ENT_COMPAT, 'UTF-8')."</ttl>\n";
		}
		if ($data->rating!="") {
			$feed.= "		<rating>".htmlspecialchars($data->rating, ENT_COMPAT, 'UTF-8')."</rating>\n";
		}
		if ($data->skipHours!="") {
			$feed.= "		<skipHours>".htmlspecialchars($data->skipHours, ENT_COMPAT, 'UTF-8')."</skipHours>\n";
		}
		if ($data->skipDays!="") {
			$feed.= "		<skipDays>".htmlspecialchars($data->skipDays, ENT_COMPAT, 'UTF-8')."</skipDays>\n";
		}

		for ($i=0; $i<count($data->items); $i++)
		{
			$feed.= "		<item>\n";
			$feed.= "			<title>".htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".$url.$data->items[$i]->link."</link>\n";
			$feed.= "			<description>".$this->_relToAbs($data->items[$i]->description)."</description>\n";
			
			// iTunes specific tags
			if ($data->items[$i]->itunes_summary!="") {
				$feed.= "			<itunes:summary>".htmlspecialchars($data->items[$i]->itunes_summary, ENT_COMPAT, 'UTF-8')."</itunes:summary>\n";
			}
			if ($data->items[$i]->itunes_duration!="") {
				$feed.= "			<itunes:duration>".htmlspecialchars($data->items[$i]->itunes_duration, ENT_COMPAT, 'UTF-8')."</itunes:duration>\n";
			}
			if ($data->items[$i]->itunes_explicit!="") {
				$feed.= "			<itunes:explicit>".$data->items[$i]->itunes_explicit."</itunes:explicit>\n";
			}
			if ($data->items[$i]->itunes_keywords!="") {
				$feed.= "			<itunes:keywords>".htmlspecialchars($data->items[$i]->itunes_keywords, ENT_COMPAT, 'UTF-8')."</itunes:keywords>\n";
			}
			if ($data->items[$i]->itunes_author!="") {
				$feed.= "			<itunes:author>".htmlspecialchars($data->items[$i]->itunes_author, ENT_COMPAT, 'UTF-8')."</itunes:author>\n";
			}
			if ($data->items[$i]->itunes_category!="") {
				$feed.= "			<itunes:category text=\"".htmlspecialchars($data->items[$i]->itunes_category, ENT_COMPAT, 'UTF-8')."\">\n";
				if ($data->items[$i]->itunes_subcategories!="") {
					$icats = $data->items[$i]->itunes_subcategories;
					foreach ($icats as $icat) 
					{
						$feed.= "				<itunes:category text=\"".htmlspecialchars($icat, ENT_COMPAT, 'UTF-8')."\">\n";
					}
				}
				$feed.= "			</itunes:category>\n";
			}
			/*if ($data->items[$i]->itunes_image!="") {
				$feed.= "			<itunes:image>".htmlspecialchars($data->items[$i]->itunes_image, ENT_COMPAT, 'UTF-8')."</itunes:image>\n";
			}*/
			if ($data->items[$i]->itunes_image!=null)
			{
				$feed.= "			<itunes:image>\n";
				$feed.= "				<url>".$data->items[$i]->itunes_image->url."</url>\n";
				$feed.= "				<title>".htmlspecialchars($data->items[$i]->itunes_image->title, ENT_COMPAT, 'UTF-8')."</title>\n";
				$feed.= "				<link>".$data->items[$i]->itunes_image->link."</link>\n";
				if ($data->items[$i]->itunes_image->width != "") {
					$feed.= "			<width>".$data->items[$i]->itunes_image->width."</width>\n";
				}
				if ($data->items[$i]->itunes_image->height!="") {
					$feed.= "			<height>".$data->items[$i]->itunes_image->height."</height>\n";
				}
				if ($data->items[$i]->itunes_image->description!="") {
					$feed.= "			<description><![CDATA[".$data->items[$i]->itunes_image->description."]]></description>\n";
				}
				$feed.= "			</itunes:image>\n";
			}
			// end iTunes specific tags
			
			if ($data->items[$i]->author!="") {
				$feed.= "			<author>".htmlspecialchars($data->items[$i]->author, ENT_COMPAT, 'UTF-8')."</author>\n";
			}
			/*
			// on hold
			if ($data->items[$i]->source!="") {
					$data.= "			<source>".htmlspecialchars($data->items[$i]->source, ENT_COMPAT, 'UTF-8')."</source>\n";
			}
			*/
			if ($data->items[$i]->category!="") {
				$feed.= "			<category>".htmlspecialchars($data->items[$i]->category, ENT_COMPAT, 'UTF-8')."</category>\n";
			}
			if ($data->items[$i]->comments!="") {
				$feed.= "			<comments>".htmlspecialchars($data->items[$i]->comments, ENT_COMPAT, 'UTF-8')."</comments>\n";
			}
			if ($data->items[$i]->date!="") {
				$itemDate =& JFactory::getDate($data->items[$i]->date);
				$feed.= "			<pubDate>".htmlspecialchars($itemDate->toRFC822(), ENT_COMPAT, 'UTF-8')."</pubDate>\n";
			}
			if ($data->items[$i]->guid!="") {
				$feed.= "			<guid>".htmlspecialchars($data->items[$i]->guid, ENT_COMPAT, 'UTF-8')."</guid>\n";
			}
			if ($data->items[$i]->enclosure != NULL)
			{
					$feed.= "			<enclosure url=\"";
					$feed.= $data->items[$i]->enclosure->url;
					$feed.= "\" length=\"";
					$feed.= $data->items[$i]->enclosure->length;
					$feed.= "\" type=\"";
					$feed.= $data->items[$i]->enclosure->type;
					$feed.= "\"/>\n";
			}

			$feed.= "		</item>\n";
		}
		$feed.= "	</channel>\n";
		$feed.= "</rss>\n";
		return $feed;
	}

	/**
	 * Convert links in a text from relative to absolute
	 *
	 * @access public
	 * @return	string
	 */
	private function _relToAbs($text)
	{
		$base = JURI::base();
  		$text = preg_replace("/(href|src)=\"(?!http|ftp|https)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}

