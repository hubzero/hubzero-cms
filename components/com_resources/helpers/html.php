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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');

/**
 * Resources helper class for misc. HTML and display
 */
class ResourcesHtml
{
	/**
	 * Encode some basic characters
	 * 
	 * @param      string  $str    Text to convert
	 * @param      integer $quotes Include quotes?
	 * @return     string
	 */
	public static function encode_html($str, $quotes=1)
	{
		$str = stripslashes($str);
		$a = array(
			'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}

	/**
	 * Clean text of potential XSS and other unwanted items such as
	 * HTML comments and javascript. Also shortens text.
	 * 
	 * @param      string  $text    Text to clean
	 * @param      integer $desclen Length to shorten to
	 * @return     string
	 */
	public static function cleanText($text, $desclen=300)
	{
		$elipse = false;

		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = str_replace('{mosimage}', '', $text);
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("\r", ' ', $text);
		$text = preg_replace('/<a\s+.*href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i', '\\2', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = strip_tags($text);
		if (strlen($text) > $desclen) 
		{
			$elipse = true;
		}
		$text = substr($text, 0, $desclen);
		if ($elipse) 
		{
			$text .= '&#8230;';
		}
		$text = trim($text);

		return $text;
	}

	/**
	 * Display an element with warning class
	 * 
	 * @param      string $msg Message to display
	 * @param      string $tag HTML element
	 * @return     string HTML
	 */
	public static function warning($msg, $tag='p')
	{
		return '<' . $tag . ' class="warning">' . $msg . '</' . $tag . '>' . "\n";
	}

	/**
	 * Display an element with archive class
	 * 
	 * @param      string $msg Message to display
	 * @param      string $tag HTML element
	 * @return     string HTML
	 */
	public static function archive($msg, $tag='p')
	{
		return '<' . $tag . ' class="archive">' . $msg . '</' . $tag . '>' . "\n";
	}

	/**
	 * Display a javascript alert
	 * 
	 * @param      string $msg Message to display
	 * @return     string HTML
	 */
	public static function alert($msg)
	{
		return "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1); </script>\n";
	}

	/**
	 * Generate an HTML header
	 * 
	 * @param      string $level Header level 1-6
	 * @param      string $txt   Header text
	 * @return     string HTML
	 */
	public static function hed($level, $txt)
	{
		return '<h' . $level . '>' . $txt . '</h' . $level . '>';
	}

	/**
	 * Generate a select form
	 * 
	 * @param      string $name  Field name
	 * @param      array  $array Data to populate select with
	 * @param      mixed  $value Value to select
	 * @param      string $class Class to add
	 * @return     string HTML
	 */
	public static function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="' . $name . '" id="' . $name . '"';
		$out .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}

	/**
	 * Draw a table row
	 * 
	 * @param      string $h Header cell
	 * @param      string $c Cell content
	 * @param      string $s Secondary cell content
	 * @return     string HTML
	 */
	public static function tableRow($h, $c='', $s='')
	{
		$html  = '  <tr>' . "\n";
		$html .= '   <th>' . $h . '</th>' . "\n";
		$html .= '   <td>';
		$html .= ($c) ? $c : '&nbsp;';
		$html .= '</td>' . "\n";
		if ($s) 
		{
			$html .= '   <td class="secondcol">';
			$html .= $s;
			$html .= '</td>' . "\n";
		}
		$html .= '  </tr>' . "\n";

		return $html;
	}

	/**
	 * Display an edit link
	 * 
	 * @param      integer $id         Resource ID
	 * @param      integer $published  Resource published date
	 * @param      integer $show_edit  Show edit controls?
	 * @param      integer $created_by Resource creator ID
	 * @param      integer $type       Link type
	 * @param      string  $r_type     Resource type ID
	 * @return     string HTML
	 */
	public static function adminIcon($id, $published, $show_edit, $created_by=0, $type, $r_type)
	{
		$juser = JFactory::getUser();

		if ($published < 0) 
		{
			return;
		}

		if (!$show_edit) 
		{
			return;
		}

		switch ($type)
		{
			case 'edit':
				if ($r_type == '7') 
				{
					$resource = new ResourcesResource(JFactory::getDBO());
					$resource->load($id);
					$link = 'index.php?option=com_tools&task=resource&step=1&app=' . $resource->alias;
				} 
				else 
				{
					$link = JRoute::_('index.php?option=com_resources&task=draft&step=1&id=' . $id);
				}
				$txt = JText::_('COM_RESOURCES_EDIT');
			break;

			default:
				$txt  = '';
				$link = '';
			break;
		}

		return ' <a class="edit button" href="' . $link . '" title="' . $txt . '">' . $txt . '</a>';
	}

	/**
	 * Extract content wrapped in <nb: tags
	 * 
	 * @param      string $text Text t extract from
	 * @param      string $tag  Tag to extract <nb:tag></nb:tag>
	 * @return     string 
	 */
	public static function parseTag($text, $tag)
	{
		preg_match("#<nb:" . $tag . ">(.*?)</nb:" . $tag . ">#s", $text, $matches);
		if (count($matches) > 0) 
		{
			$match = $matches[0];
			$match = str_replace('<nb:' . $tag . '>', '', $match);
			$match = str_replace('</nb:' . $tag . '>', '', $match);
		} 
		else 
		{
			$match = '';
		}
		return $match;
	}

	/**
	 * Format an ID by prefixing 0s.
	 * This is used for directory naming
	 * 
	 * @param      integer $someid ID to format
	 * @return     string
	 */
	public static function niceidformat($someid)
	{
		return \Hubzero\Utility\String::pad($someid);
	}

	/**
	 * Build the path to resources files from the creating date
	 * 
	 * @param      string  $date Timestamp (0000-00-00 00:00:00)
	 * @param      integer $id   Resource ID
	 * @param      string  $base Base path to prepend
	 * @return     string
	 */
	public static function build_path($date='', $id, $base)
	{
		$dir_id = self::niceidformat($id);
		
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs)) 
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		if ($date) 
		{
			$dir_year  = JFactory::getDate($date)->format('Y');
			$dir_month = JFactory::getDate($date)->format('m');

			if (!is_dir($base . DS . $dir_year . DS . $dir_month . DS . $dir_id) && intval($dir_year) <= 2013 && intval($dir_month) <= 11)
			{
				$dir_year  = JHTML::_('date', $date, 'Y');
				$dir_month = JHTML::_('date', $date, 'm');
			}
		} 
		else 
		{
			$dir_year  = JFactory::getDate()->format('Y');
			$dir_month = JFactory::getDate()->format('m');
		}

		return $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;
	}
	
	/**
	 * Display certain supporting docs and/or link to more
	 * 
	 * @param      object  $publication   	Publication object
	 * @param      string  $option 			Component name
	 * @param      object  $children 		Publication attachments	
	 * @return     string HTML
	 */
	public static function sortSupportingDocs( $publication, $option, $children ) 
	{
		// Set counts		
		$docs 	= 0;
		
		$html 	= '';
		$supln  = '<ul class="supdocln">'."\n";
		$supli  = array();
		$shown 	= array();
				
		if ($children)
		{
			foreach ($children as $child) 
			{			
				$docs++;									
				$child->title = $child->title ? stripslashes($child->title) : '';				
				$child->title = str_replace( '"', '&quot;', $child->title );
				$child->title = str_replace( '&amp;', '&', $child->title );
				$child->title = str_replace( '&', '&amp;', $child->title );
				$child->title = str_replace( '&amp;quot;', '&quot;', $child->title );
				
				$title = ($child->logicaltitle)
						? stripslashes($child->logicaltitle)
						: stripslashes($child->title);
						
				$params = new JParameter( $child->params );
				
				$ftype 	  = ResourcesHtml::getFileExtension($child->path);
				//$class    = $params->get('class', $ftype);
				$doctitle = $params->get('title', $title);
				
				// Things we want to highlight
				$toShow = array('User Guide', 'Syllabus', 'iTunes', 'iTunes U', 'Audio', 'Video', 'Slides', 'YouTube', 'Vimeo');

				$url = ResourcesHtml::processPath($option, $child, $publication->id);
				$extra = '';
				
				foreach ($toShow as $item)
				{
					if (strtolower($doctitle) !=  preg_replace('/' . strtolower($item) . '/', '', strtolower($doctitle))
						&& !in_array($item, $shown)) 
					{
						$class = str_replace(' ', '', strtolower($item));
						$childParams  = new JParameter($child->params);
						$childAttribs = new JParameter($child->attribs);
						$linkAction = $childParams->get('link_action', 0);
						$width      = $childAttribs->get('width', 640);
						$height     = $childAttribs->get('height', 360);
						
						if ($linkAction == 1)
						{
							$supli[] = ' <li><a class="'.$class.'" rel="external" href="'.$url.'" title="'.$child->title.'"' 
							. $extra . '>'.$item.'</a></li>'."\n";
						}
						elseif ($linkAction == 2)
						{
							$class .= ' play';
							$class .= ' ' . $width . 'x' . $height;
							$supli[] = ' <li><a class="'.$class.'" href="'.$url.'" title="'.$child->title.'"' 
							. $extra . '>'.$item.'</a></li>'."\n";
						}
						else
						{
							$supli[] = ' <li><a class="'.$class.'" href="'.$url.'" title="'.$child->title.'"' 
							. $extra . '>'.$item.'</a></li>'."\n";
						}

						$shown[] = $item;
					}
				}
			}
		}	
		
		$sdocs = count( $supli ) > 2 ? 2 : count( $supli );
		$otherdocs = $docs - $sdocs;

		for ($i=0; $i < count( $supli ); $i++) 
		{
			$supln .=  $i < 2 ? $supli[$i] : '';
			$supln .=  $i == 2 && !$otherdocs ? $supli[$i] : '';
		}
		
		// View more link?			
		if ($docs > 0 && $otherdocs > 0) 
		{
			$supln .= ' <li class="otherdocs"><a href="' . JRoute::_('index.php?option=' . $option 
				. '&id=' . $publication->id . '&active=supportingdocs')
				.'" title="' . JText::_('View All') . ' ' . $docs.' ' . JText::_('Supporting Documents').' ">' 
				. $otherdocs . ' ' . JText::_('more') . ' &rsaquo;</a></li>' . "\n";
		}
		 
		if (!$sdocs && $docs > 0) 
		{
			$html .= "\t\t" . '<p class="viewalldocs"><a href="' . JRoute::_('index.php?option=' 
				. $option . '&id=' . $publication->id . '&active=supportingdocs') . '">' 
				. JText::_('Additional materials available') . ' (' . $docs .')</a></p>'."\n";
		}
		
		$supln .= '</ul>'."\n";
		$html .= $sdocs ? $supln : '';
		return $html;			
	}

	/**
	 * Display a list of screenshots for a tool
	 * 
	 * @param      integer $id        Tool ID
	 * @param      string  $created   Created date
	 * @param      string  $upath     Upload path
	 * @param      string  $wpath     Web path for display
	 * @param      integer $versionid Version ID
	 * @param      array   $sinfo     Screenshots information
	 * @param      integer $slidebar  Display slidebar?
	 * @param      string  $path      Path
	 * @return     string HTML
	 */
	public static function screenshots($id, $created, $upath, $wpath, $versionid=0, $sinfo=array(), $slidebar=0, $path='')
	{
		$path = self::build_path($created, $id, '');

		// Get contribtool parameters
		$tconfig = JComponentHelper::getParams('com_tools');
		$allowversions = $tconfig->get('screenshot_edit');

		if ($versionid && $allowversions) 
		{
			// Add version directory
			$path .= DS . $versionid;
		}

		$d = @dir(JPATH_ROOT . $upath . $path);

		$images = array();
		$tns = array();
		$all = array();
		$ordering = array();
		$html = '';

		if ($d) 
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(JPATH_ROOT . $upath . $path . DS . $img_file) 
				 && substr($entry, 0, 1) != '.' 
				 && strtolower($entry) !== 'index.html') 
				{
					if (preg_match("#bmp|gif|jpg|png|swf|mov#i", $img_file)) 
					{
						$images[] = $img_file;
					}
					if (preg_match("/-tn/i", $img_file)) 
					{
						$tns[] = $img_file;
					}
					$images = array_diff($images, $tns);
				}
			}

			$d->close();
		}

		$b = 0;
		if ($images) 
		{
			foreach ($images as $ima)
			{
				$new = array();
				$new['img'] = $ima;
				$new['type'] = explode('.', $new['img']);

				// get title and ordering info from the database, if available
				if (count($sinfo) > 0) 
				{
					foreach ($sinfo as $si)
					{
						if ($si->filename == $ima) 
						{
							$new['title'] = stripslashes($si->title);
							$new['title'] = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $new['title']);
							$new['ordering'] = $si->ordering;
						}
					}
				}

				$ordering[] = isset($new['ordering']) ? $new['ordering'] : $b;
				$b++;
				$all[] = $new;
			}
		}

		if (count($sinfo) > 0)
		{
			// Sort by ordering
			array_multisort($ordering, $all);
		} 
		else 
		{
			// Sort by name
			sort($all);
		}
		$images = $all;

		$els = '';
		$k = 0;
		$g = 0;
		for ($i=0, $n=count($images); $i < $n; $i++)
		{
			$tn = self::thumbnail($images[$i]['img']);
			$els .=  ($slidebar && $i==0) ? '<div class="showcase-pane">' . "\n" : '';

			if (is_file(JPATH_ROOT . $upath . $path . DS . $tn)) 
			{
				if (strtolower(end($images[$i]['type'])) == 'swf' || strtolower(end($images[$i]['type'])) == 'mov') 
				{
					$g++;
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='') ? $images[$i]['title'] : JText::_('DEMO') . ' #' . $g;
					$els .= $slidebar ? '' : '<li>';
					$els .= ' <a class="popup" href="' . $wpath . $path . DS . $images[$i]['img'] . '" title="' . $title . '">';
					$els .= '<img src="' . $wpath . $path . DS . $tn . '" alt="' . $title . '" class="thumbima" /></a>';
					$els .= $slidebar ? '' : '</li>' . "\n";
				} 
				else 
				{
					$k++;
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='')  ? $images[$i]['title']: JText::_('SCREENSHOT') . ' #' . $k;
					$els .= $slidebar ? '' : '<li>';
					$els .= ' <a rel="lightbox" href="' . $wpath . $path . DS . $images[$i]['img'] . '" title="' . $title . '">';
					$els .= '<img src="' . $wpath . $path . DS . $tn . '" alt="' . $title . '" class="thumbima" /></a>';
					$els .= $slidebar ? '' : '</li>' . "\n";
				}
			}
			$els .=  ($slidebar && $i == ($n - 1)) ? '</div>' . "\n" : '';
		}

		if ($els) 
		{
			$html .= $slidebar ? '<div id="showcase">' . "\n" : '';
			$html .= $slidebar ? '  <div id="showcase-prev" ></div>' . "\n" : '';
			$html .= $slidebar ? '  <div id="showcase-window">' . "\n" : '';
			$html .= $slidebar ? '' : '<ul class="screenshots">' . "\n";
			$html .= $els;
			$html .= $slidebar ? '' : '</ul>' . "\n";
			$html .= $slidebar ? '  </div>' . "\n" : '';
			$html .= $slidebar ? '  <div id="showcase-next" ></div>' . "\n" : '';
			$html .= $slidebar ? '</div>' . "\n" : '';
		}

		return $html;
	}

	/**
	 * Generate a thumbnail name from a file name
	 * 
	 * @param      string $pic File name
	 * @return     string
	 */
	public static function thumbnail($pic)
	{
		jimport('joomla.filesystem.file');
		return JFile::stripExt($pic) . '-tn.gif';
	}

	/**
	 * Display a list of skill levels
	 * 
	 * @param      array   $levels List of levels
	 * @param      integer $sel    Selected level
	 * @return     string HTML
	 */
	public static function skillLevelCircle($levels = array(), $sel = 0)
	{
		$html = '<ul class="audiencelevel">' . "\n";
		foreach ($levels as $key => $value)
		{
			$class = ($key != $sel) ? ' isoff' : '';
			$class = ($key != $sel && $key == 'level0') ? '_isoff' : $class;
			$html .= "\t".' <li class="' . $key . $class . '"><span>&nbsp;</span></li>' . "\n";
		}
		$html .= '</ul>' . "\n";
		return $html;
	}

	/**
	 * Display a table of skill levels
	 * 
	 * @param      array  $labels       Skill levels
	 * @param      string $audiencelink Link to learn more about skill levels
	 * @return     string HTML
	 */
	public static function skillLevelTable($labels = array(), $audiencelink)
	{
		$html  = '<table class="skillset" summary="' . JText::_('Resource Audience Skill Rating Table') . '">' . "\n";
		$html .= "\t".'<thead>' . "\n";
		$html .= "\t\t".'<tr>' . "\n";
		$html .= "\t\t".'<td colspan = "2" class="combtd">' . JText::_('Difficulty Level') . '</td>' . "\n";
		$html .= "\t\t".'<td>' . JText::_('Target Audience') . '</td>' . "\n";
		$html .= "\t\t".'</tr>' . "\n";
		$html .= "\t".'</thead>' . "\n";
		$html .= "\t". '<tbody>' . "\n";
		foreach ($labels as $key => $label)
		{
			$ul = self::skillLevelCircle($labels, $key);
			$html .= self::tableRow($ul, $label['title'], $label['desc']);
		}
		$html .= "\t" . '</tbody>' . "\n";
		$html .= '</table>' . "\n";
		$html .= '<p class="learnmore"><a href="' . $audiencelink . '">'.JText::_('Learn more') . ' &rsaquo;</a></p>' . "\n";
		return $html;
	}

	/**
	 * Show skill levels
	 * 
	 * @param      array   $audience     Audiences
	 * @param      integer $showtips     Show tips?
	 * @param      integer $numlevels    Number of levels to dipslay
	 * @param      string  $audiencelink Link to learn more about skill levels
	 * @return     string HTML
	 */
	public static function showSkillLevel($audience, $showtips = 1, $numlevels = 4, $audiencelink = '')
	{
		$html     = '';
		$levels   = array();
		$labels   = array();
		$selected = array();
		$txtlabel = '';

		if ($audience && count($audience) > 0) 
		{
			$audience = $audience[0];
			$html .= "\t\t" . '<div class="showscale">' . "\n";

			for ($i = 0, $n = $numlevels; $i <= $n; $i++)
			{
				$lb = 'label' . $i;
				$lv = 'level' . $i;
				$ds = 'desc' . $i;
				$levels[$lv] = $audience->$lv;
				$labels[$lv]['title'] = $audience->$lb;
				$labels[$lv]['desc']  = $audience->$ds;
				if ($audience->$lv) 
				{
					$selected[] = $lv;
				}
			}

			$html.= '<ul class="audiencelevel">' . "\n";

			// colored circles
			foreach ($levels as $key => $value)
			{
				$class = (!$value) ? ' isoff' : '';
				$class = (!$value && $key == 'level0') ? '_isoff' : $class;
				$html .= ' <li class="' . $key . $class . '"><span>&nbsp;</span></li>' . "\n";
			}

			// figure out text label
			if (count($selected) == 1) 
			{
				$txtlabel = $labels[$selected[0]]['title'];
			} 
			else if (count($selected) > 1) 
			{
				$first 	    = array_shift($selected);
				$first		= $labels[$first]['title'];
				$firstbits  = explode("-", $first);
				$first 	    = array_shift($firstbits);

				$last     = end($selected);
				$last     = $labels[$last]['title'];
				$lastbits = explode("-", $last);
				$last     = end($lastbits);

				$txtlabel = $first . '-' . $last;
			} 
			else 
			{
				$txtlabel = JText::_('Tool Audience Unrated');
			}

			$html .= ' <li class="txtlabel">' . $txtlabel . '</li>' . "\n";
			$html .= '</ul>' . "\n";
			$html .= "\t\t" . '</div>' . "\n";

			// pop-up with explanation
			if ($showtips) 
			{
				$html .= "\t\t" . '<div class="explainscale">' . "\n";
				$html .= self::skillLevelTable($labels, $audiencelink);
				$html .= "\t\t" . '</div>' . "\n";
			}

			return '<div class="usagescale">' . $html . '</div>';
		}

		return $html;
	}

	/**
	 * Write metadata information for a resource
	 * 
	 * @param      object  $params    Resource params
	 * @param      integer $ranking   Resource ranking
	 * @param      string  $statshtml Usage data to append
	 * @param      integer $id        Resource ID
	 * @param      array   $sections  Active plugins' names
	 * @param      string  $xtra      Extra content to append
	 * @return     string HTML
	 */
	public static function metadata($params, $ranking, $statshtml, $id, $sections, $xtra='')
	{
		/*$html = '';
		if ($params->get('show_ranking')) 
		{
			$rank = round($ranking, 1);

			$r = (10*$rank);
			if (intval($r) < 10) 
			{
				$r = '0' . $r;
			}

			$html .= '<dl class="rankinfo">' . "\n";
			$html .= "\t".'<dt class="ranking"><span class="rank-' . $r . '">This resource has a</span> ' . number_format($rank, 1) . ' Ranking</dt>' . "\n";
			$html .= "\t".'<dd>' . "\n";
			$html .= "\t\t".'<p>' . "\n";
			$html .= "\t\t\t".'Ranking is calculated from a formula comprised of <a href="' . JRoute::_('index.php?option=com_resources&id=' . $id . '&active=reviews') . '">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>' . "\n";
			$html .= "\t\t".'</p>' . "\n";
			$html .= "\t\t".'<div>' . "\n";
			$html .= $statshtml;
			$html .= "\t\t".'</div>' . "\n";
			$html .= "\t".'</dd>' . "\n";
			$html .= '</dl>' . "\n";
		}
		$html .= ($xtra) ? $xtra : '';
		foreach ($sections as $section)
		{
			$html .= (isset($section['metadata'])) ? $section['metadata'] : '';
		}
		$html .= '<div class="clear"></div>';

		return '<div class="metadata">' . $html . '</div>';*/
		$view = new JView(array(
			'name'   => 'view',
			'layout' => '_metadata',
		));
		$view->option = 'com_resources';
		$view->sections = $sections;
		$view->model = ResourcesModelResource::getInstance($id);
		return $view->loadTemplate();
	}

	/**
	 * ===MARKED FOR DEPRECATION===
	 *
	 * Write a header and container for supporting documents
	 * 
	 * @param      string $content Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function supportingDocuments($content)
	{
		$html  = self::hed(3,JText::_('COM_RESOURCES_SUPPORTING_DOCUMENTS')) . "\n";
		$html .= $content;

		return '<div class="supportingdocs">' . $html . '</div>';
	}

	/**
	 * Display a link to the license associated with this resource
	 * 
	 * @param      array $license License name
	 * @return     string HTML
	 */
	public static function license($license)
	{
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'license.php');

		$license = str_replace(' ', '-', strtolower($license));
		$license = preg_replace("/[^a-zA-Z0-9\-_]/", '', $license);

		$database = JFactory::getDBO();
		$rl = new ResourcesLicense($database);
		$rl->load($license);

		$html = '';
		if ($rl->id)
		{
			if (substr($rl->name, 0, 6) != 'custom')
			{
				$html = '<p class="' . $rl->name . ' license">Licensed';
				if ($rl->url)
				{
					$html .= ' according to <a rel="license" href="' . $rl->url . '" title="' . $rl->title . '">this deed</a>';
				}
				else 
				{
					$html .= ' under ' . $rl->title;
				}
				$html .= '.</p>';
			}
			else 
			{
				$html = '<p class="' . $rl->name . ' license">Licensed according to <a rel="license" class="popup" href="' . JRoute::_('index.php?option=com_resources&task=license&resource=' . substr($rl->name, 6) . '&no_html=1') . '">this deed</a>.</p>';
			}
		}
		return $html;
	}

	/**
	 * Display resource sub view content
	 * 
	 * @param      array  $sections Active plugins' content
	 * @param      array  $cats     Active plugins' names
	 * @param      string $active   Current plugin name
	 * @param      string $h        Hide class
	 * @param      string $c        Extra classes
	 * @return     string HTML
	 */
	public static function sections($sections, $cats, $active='about', $h, $c)
	{
		$html = '';

		if (!$sections) 
		{
			return $html;
		}

		$k = 0;

		foreach ($sections as $section)
		{
			if ($section['html'] != '') 
			{
				/*
				if (!isset($cats[$k]) || !$cats[$k])
				{
					continue;
				}
				*/
				$cls  = ($c) ? $c . ' ' : '';
				//if (key($cats[$k]) != $active) 
				if ($section['area'] != $active)
				{
					$cls .= ($h) ? $h . ' ' : '';
				}
				$html .= '<div class="' . $cls . 'section" id="' . $section['area'] . '-section">' . $section['html'] . '</div>';
			}
			$k++;
		}

		return $html;
	}

	/**
	 * Output tab controls for resource plugins (sub views)
	 * 
	 * @param      string $option Component name
	 * @param      string $id     Resource ID
	 * @param      array  $cats   Active plugins' names
	 * @param      string $active Current plugin name
	 * @param      string $alias  Resource alias
	 * @return     string HTML
	 */
	public static function tabs($option, $id, $cats, $active='about', $alias='')
	{
		$html  = "\t" . '<ul id="sub-menu" class="sub-menu">' . "\n";
		$i = 1;
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '') 
			{
				if ($alias) 
				{
					$url = JRoute::_('index.php?option=' . $option . '&alias=' . $alias . '&active=' . $name);
				} 
				else 
				{
					$url = JRoute::_('index.php?option=' . $option . '&id=' . $id . '&active=' . $name);
				}
				if (strtolower($name) == $active) 
				{
					$app = JFactory::getApplication();
					$pathway = $app->getPathway();
					$pathway->addItem($cat[$name],$url);

					if ($active != 'about') 
					{
						$document = JFactory::getDocument();
						$title = $document->getTitle();
						$document->setTitle($title . ': ' . $cat[$name]);
					}
				}
				$html .= "\t\t" . '<li id="sm-' . $i . '"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="' . $name . '" href="' . $url . '"><span>' . $cat[$name] . '</span></a></li>' . "\n";
				$i++;
			}
		}
		$html .= "\t".'</ul>' . "\n";

		return $html;
	}

	/**
	 * Generate resource title
	 * 
	 * @param      string  $option      Component name
	 * @param      object  $resource    ResourcesResource
	 * @param      object  $params      Resource config
	 * @param      integer $show_edit   Show edit controls?
	 * @param      object  $config      Component config
	 * @param      integer $show_posted Show published date
	 * @return     string HTML
	 */
	public static function title($option, $resource, $params, $show_edit, $config=null, $show_posted=1)
	{
		$mode = JRequest::getWord('mode', '');

		$txt = '';

		if ($mode != 'preview')
		{
			switch ($resource->published)
			{
				case 1: $txt .= ''; break;
				case 2: $txt .= '<span>[' . JText::_('COM_RESOURCES_DRAFT_EXTERNAL') . ']</span> '; break;
				case 3: $txt .= '<span>[' . JText::_('COM_RESOURCES_PENDING') . ']</span> ';        break;
				case 4: $txt .= '<span>[' . JText::_('COM_RESOURCES_DELETED') . ']</span> ';        break;
				case 5: $txt .= '<span>[' . JText::_('COM_RESOURCES_DRAFT_INTERNAL') . ']</span> '; break;
				case 0; $txt .= '<span>[' . JText::_('COM_RESOURCES_UNPUBLISHED') . ']</span> ';    break;
			}
		}

		$txt .= stripslashes($resource->title);
		if ($mode != 'preview')
		{
			$txt .= self::adminIcon($resource->id, $resource->published, $show_edit, 0, 'edit', $resource->type);
		}
		
		$html  = self::hed(2, $txt) . "\n";

		if ($show_posted) 
		{
			switch ($params->get('show_date'))
			{
				case 0: $thedate = ''; break;
				case 1: $thedate = $resource->created; break;
				case 2: $thedate = $resource->modified; break;
				case 3: $thedate = $resource->publish_up; break;
			}

			$typenorm = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($resource->getTypeTitle()));

			$html .= '<p>' . JText::_('COM_RESOURCES_POSTED') . ' ';
			$html .= ($thedate) ? JHTML::_('date', $thedate, JText::_('DATE_FORMAT_HZ1')) . ' ' : '';
			$html .= JText::_('COM_RESOURCES_IN') . ' <a href="' . JRoute::_('index.php?option=' . $option . '&type=' . $typenorm) . '">' . $resource->getTypeTitle() . '</a></p>' . "\n";
		}

		$html .= '<input type="hidden" name="rid" id="rid" value="' . $resource->id . '" />' . "\n";

		return '<div id="content-header" class="full">' . $html . '</div>';
	}

	/**
	 * Generate COins microformat
	 * 
	 * @param      object $cite     Resource citation data
	 * @param      object $resource ResourcesResource
	 * @param      object $config   Component config
	 * @param      object $helper   ResourcesHelper
	 * @return     string HTML
	 */
	//public static function citationCOins($cite, $resource, $config, $helper)
	public static function citationCOins($cite, $model)
	{
		if (!$cite) 
		{
			return '';
		}

		$html  = '<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal';

		// Get contribtool params
		$tconfig = JComponentHelper::getParams('com_tools');
		$doi = '';

		/*
		if (isset($resource->doi) && $resource->doi  && $tconfig->get('doi_shoulder'))
		{
			$doi = 'doi:' . $tconfig->get('doi_shoulder') . DS . strtoupper($resource->doi);
		}
		else if (isset($resource->doi_label) && $resource->doi_label)
		{
			$doi = 'doi:10254/' . $tconfig->get('doi_prefix') . $resource->id . '.' . $resource->doi_label;
		}*/

		$html .= isset($model->resource->doi)
				? '&amp;rft_id=info%3Adoi%2F'.urlencode($model->resource->doi)
				: '&amp;rfr_id=info%3Asid%2Fnanohub.org%3AnanoHUB';
		$html .= '&amp;rft.genre=article';
		$html .= '&amp;rft.atitle=' . urlencode($cite->title);
		$html .= '&amp;rft.date=' . urlencode($cite->year);

		if (isset($model->resource->revision) && $model->resource->revision!='dev')
		{
			//$helper->getToolAuthors($model->resource->alias, $model->resource->revision);
			$author_array = $model->contributors('tool');
		} 
		else 
		{
			//$helper->getCons();
			$author_array = $model->contributors();
		}
		//$author_array = $helper->_contributors;

		if ($author_array) 
		{
			for ($i = 0; $i < count($author_array); $i++)
			{
				if ($author_array[$i]->surname || $author_array[$i]->givenName) 
				{
					$name = stripslashes($author_array[$i]->givenName) . ' ';
					if ($author_array[$i]->middleName != NULL) 
					{
						$name .= stripslashes($author_array[$i]->middleName) . ' ';
					}
					$name .= stripslashes($author_array[$i]->surname);
				} 
				else 
				{
					$name = $author_array[$i]->name;
				}

				if ($i==0) 
				{
					$lastname  = $author_array[$i]->surname  ? $author_array[$i]->surname  : $author_array[$i]->name;
					$firstname = $author_array[$i]->givenName ? $author_array[$i]->givenName : $author_array[$i]->name;
					$html .= '&amp;rft.aulast=' . urlencode($lastname) . '&amp;rft.aufirst=' . urlencode($firstname);
				}
			}
		}

		$html .= '"></span>' . "\n";

		return $html;
	}

	/**
	 * Generate a citation for a resource
	 * 
	 * @param      string  $option    Component name
	 * @param      object  $cite      Citation data
	 * @param      string  $id        Resource ID
	 * @param      string  $citations Citations to prepend
	 * @param      integer $type      Resource type
	 * @param      string  $rev       Tool revision
	 * @return     string HTML
	 */
	public static function citation($option, $cite, $id, $citations, $type, $rev='')
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

		$html  = '<p>' . JText::_('COM_RESOURCES_CITATION_INSTRUCTIONS') . '</p>' . "\n";
		if (trim($citations))
		{
			$html .= '<ul class="citations results">' . "\n";
			$html .= "\t" . '<li>' . "\n";
			$html .= $citations;
			$html .= "\t" . '</li>' . "\n";
			$html .= '</ul>' . "\n";
		}
		if ($cite) 
		{
			$html .= '<ul class="citations results">' . "\n";
			$html .= "\t" . '<li>' . "\n";
			$html .= CitationFormat::formatReference($cite);
			if (is_numeric($rev) || (is_string($rev) && $rev != 'dev')) 
			{
				$html .= "\t\t" . '<p class="details">' . "\n";
				$html .= "\t\t\t" . '<a href="index.php?option=' . $option . '&task=citation&id=' . $id . '&format=bibtex&no_html=1&rev=' . $rev . '" title="' . JText::_('COM_RESOURCES_DOWNLOAD_BIBTEX_FORMAT') . '">BibTex</a> <span>|</span> ' . "\n";
				$html .= "\t\t\t" . '<a href="index.php?option=' . $option . '&task=citation&id=' . $id . '&format=endnote&no_html=1&rev=' . $rev . '" title="' . JText::_('COM_RESOURCES_DOWNLOAD_ENDNOTE_FORMAT') . '">EndNote</a>' . "\n";
				$html .= "\t\t" . '</p>' . "\n";
			}
			$html .= "\t" . '</li>' . "\n";
			$html .= '</ul>' . "\n";
			
		}
		
		/*if ($type == 7) 
		{
			$html .= '<p>'.JText::_('In addition, we would appreciate it if you would add the following acknowledgment to your publication:').'</p>' . "\n";
			$html .= '<ul class="citations results">' . "\n";
			$html .= "\t".'<li>' . "\n";
			$html .= "\t\t".'<p>'.JText::_('Simulation services for results presented here were provided by the Network for Computational Nanotechnology (NCN) at nanoHUB.org').'</p>' . "\n";
			$html .= "\t".'</li>' . "\n";
			$html .= '</ul>' . "\n";
		}*/
		return $html;
	}

	/**
	 * Get the classname for a rating value
	 * 
	 * @param      integer $rating Rating (out of 5 total)
	 * @return     string 
	 */
	public static function getRatingClass($rating=0)
	{
		switch ($rating)
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}
		return $class;
	}

	/**
	 * Short description for 'writeChildren'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $config Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      unknown $resource Parameter description (if any) ...
	 * @param      array $children Parameter description (if any) ...
	 * @param      unknown $live_site Parameter description (if any) ...
	 * @param      integer $id Parameter description (if any) ...
	 * @param      integer $active Parameter description (if any) ...
	 * @param      integer $pid Parameter description (if any) ...
	 * @param      integer $fsize Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function writeChildren($config, $option, $database, $resource, $children, $live_site, $id=0, $active=0, $pid=0, $fsize=0)
	{
	    $juser = JFactory::getUser();
		$out   = '';
		$blorp = '';
		if ($children != NULL) 
		{
			$paramsClass = 'JParameter';
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$paramsClass = 'JRegistry';
			}

			$linkAction = 0;
			$out .= '<ul>' . "\n";
			$base = $config->get('uploadpath');
			foreach ($children as $child)
			{
				if ($child->access == 0 || ($child->access == 1 && !$juser->get('guest'))) 
				{
					jimport('joomla.filesystem.file');
					$ftype = JFile::getExt($child->path);

					//$url = self::processPath($option, $child, $pid);

					$class = '';
					$action = '';
					if ($child->standalone == 1) 
					{
						$liclass = ' class="html"';
						$title = stripslashes($child->title);
					} 
					else 
					{
						//$rt = new ResourcesType($database);
						//$rt->load($child->type);
						$rt = ResourcesType::getRecordInstance($child->type);
						$tparams = new $paramsClass($rt->params);

						//$lt = new ResourcesType($database);
						//$lt->load($child->logicaltype);
						$lt = ResourcesType::getRecordInstance($child->logicaltype);
						$ltparams = new $paramsClass($lt->params);

						// Check the link action by child's type
						if ($child->logicaltype) 
						{
							$rtLinkAction = $ltparams->get('linkAction', 'extension');
						} 
						else 
						{
							$rtLinkAction = $tparams->get('linkAction', 'extension');
						}

						switch ($rtLinkAction)
						{
							case 'download':
								$class = 'download';
								$linkAction = 3;
							break;

							case 'lightbox':
								$class = 'play';
								$linkAction = 2;
							break;

							case 'newwindow':
								$action = 'rel="external"';
								$linkAction = 1;
							break;

							case 'extension':
							default:
								$linkAction = 0;

								//$mediatypes = array('11','20','34','19','37','32','15','40','41','15','76');
								$mediatypes = array('elink','quicktime','presentation','presentation_audio','breeze','quiz','player','video_stream','video','hubpresenter');
								//$downtypes = array('60','59','57','55');
								$downtypes = array('thesis','handout','manual','software_download');

								if (in_array($lt->alias, $downtypes)) {
									$class = 'download';
								} elseif (in_array($rt->alias, $mediatypes)) {
									$mediatypes = array('flash_paper','breeze','32','26');
									if (in_array($child->type, $mediatypes)) {
										$class = 'play';
									}
								} else {
									$class = 'download';
								}
							break;
						}

						// Check for any link action overrides on the child itself
						$childParams = new $paramsClass($child->params);
						$linkAction = intval($childParams->get('link_action', $linkAction));
						switch ($linkAction)
						{
							case 3:
								$class = 'download';
							break;

							case 2:
								$class = 'play';
							break;

							case 1:
								$action = 'rel="external"';
							break;

							case 0:
							default:
								// Do nothing
							break;
						}

						switch ($rt->alias)
						{
							case 'user_guide':
								$liclass = ' class="guide"';
								break;
							case 'ilink':
								$liclass = ' class="html"';
								break;
							case 'breeze':
								$liclass = ' class="swf"';
								//$class = ' class="play"';
								break;

							case 'hubpresenter':
							 	$liclass = ' class="presentation"';
								$class = 'hubpresenter';
								break;
							default:
								$liclass = ' class="'.strtolower($ftype).'"';
								break;
						}

						$title = ($child->logicaltitle) ? $child->logicaltitle : stripslashes($child->title);
					}

					$url = self::processPath($option, $child, $pid, $linkAction);

					$child->title = str_replace('"', '&quot;', $child->title);
					$child->title = str_replace('&amp;', '&', $child->title);
					$child->title = str_replace('&', '&amp;', $child->title);
					$child->title = str_replace('&amp;quot;', '&quot;', $child->title);

					// width & height
					$attribs = new $paramsClass($child->attribs);
					$width  = intval($attribs->get('width', 640));
					$height = intval($attribs->get('height', 360));
					if ($width > 0 && $height > 0) 
					{
						$class .= ' ' . $width . 'x' . $height;
					}

					// user guide 
					//$guide = 0;
					if (strtolower($title) !=  preg_replace('/user guide/', '', strtolower($title))) 
					{
						$liclass = ' class="guide"';
						//$guide = 1;
					}

					$out .= "\t" . '<li' . $liclass . '>' . "\n";
					$out .= "\t\t" . self::getFileAttribs($child->path, $base, $fsize) . "\n";
					$out .= "\t\t" . '<a';
					$out .= ($class) ? ' class="' . $class . '"' : '';
					$out .= ' href="' . $url . '"';
					$out .= ($action)  ? ' ' . $action : '';
					$out .= ' title="' . stripslashes($child->title) . '">' . $title . '</a>' . "\n";
					$out .= "\t" . '</li>' . "\n";
				}
			}
			$out .= '</ul>' . "\n";
		} 
		else 
		{
			$out .= '<p>[ none ]</p>';
		}
		return $out;
	}

	/**
	 * Get the extension of a file
	 * 
	 * @param      string $url File path/name
	 * @return     string
	 */
	public static function getFileExtension($url)
	{
		jimport('joomla.filesystem.file');
		return JFile::getExt($url);
	}

	/**
	 * Determine the final URL for the primary resource child
	 * 
	 * @param      string  $option Component name
	 * @param      object  $item   Child resource
	 * @param      integer $pid    Parent resource ID
	 * @param      integer $action Action
	 * @return     string
	 */
	public static function processPath($option, $item, $pid=0, $action=0)
	{
		$database = JFactory::getDBO();
		$juser = JFactory::getUser();

		//$rt = new ResourcesType($database);
		//$rt->load($item->type);
		$rt = ResourcesType::getRecordInstance($item->type);
		$type = $rt->alias;

		if ($item->standalone == 1) 
		{
			$url = JRoute::_('index.php?option=' . $option . '&id=' .  $item->id);
		} 
		else 
		{
			switch ($type)
			{
				case 'ilink':
					if ($item->path) 
					{
						// internal link, not a resource
						$url = $item->path;
					} 
					else 
					{
						// internal link but a resource
						$url = JRoute::_('index.php?option=' . $option . '&id=' .  $item->id);
					}
				break;

				case 'video':
					$url = JRoute::_('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=video');
				break;

				case 'hubpresenter':
					$url = JRoute::_('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=watch');
				break;

				case 'breeze':
					$url = JRoute::_('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=play');
				break;

				default:
					if ($action == 2) 
					{
						$url = JRoute::_('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=play');
					} 
					else 
					{
						if (strstr($item->path, 'http') || substr($item->path, 0, 3) == 'mms') 
						{
							$url = $item->path;
						} 
						else 
						{
							$url = JRoute::_('index.php?option=' . $option . '&id=' . $item->id . '&task=download&file=' . basename($item->path));
						}
					}
				break;
			}
		}
		return $url;
	}

	/**
	 * Display the primary child
	 * For most resources, this will be the first child of a standalone resource
	 * Tools are the only exception in which case the button launches a tool session
	 * 
	 * @param      string $option     Component name
	 * @param      object $resource   ResourcesResource
	 * @param      object $firstChild First resource child
	 * @param      string $xact       Extra parameters to add
	 * @return     string 
	 */
	public static function primary_child($option, $resource, $firstChild, $xact='')
	{
	    $juser = JFactory::getUser();

		$database = JFactory::getDBO();

		$html = '';

		switch ($resource->type)
		{
			case 7:
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					$jacl = JFactory::getACL();
					$jacl->addACL('com_tools', 'manage', 'users', 'super administrator');
					$jacl->addACL('com_tools', 'manage', 'users', 'administrator');
					$jacl->addACL('com_tools', 'manage', 'users', 'manager');

					$authorized = $juser->authorize('com_tools', 'manage');
				}
				else
				{
					$authorized = $juser->authorise('core.manage', 'com_tools.' . $resource->id);
				}

				$juser = JFactory::getUser();

				$mconfig = JComponentHelper::getParams('com_tools');

				// Ensure we have a connection to the middleware
				if (!$mconfig->get('mw_on')
				 || ($mconfig->get('mw_on') > 1 && !$authorized)) 
				{
					$pop   = self::warning(JText::_('Session invocation is currently disabled.'));
					$html .= self::primaryButton('link_disabled', '', 'Launch Tool', '', '', '', 1, $pop);
					return $html;
				}
				
				//are we on the iPad
				$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
				
				//get tool params
				$params = JComponentHelper::getParams('com_tools');
				$launchOnIpad = $params->get('launch_ipad', 0);

				// Generate the URL that launches a tool session
				$lurl ='';
				$database = JFactory::getDBO();
				$tables = $database->getTableList();
				$table = $database->getPrefix() . 'tool_version';

				if (in_array($table,$tables)) 
				{
					if (isset($resource->revision) && $resource->toolpublished) 
					{

						$sess = $resource->tool ? $resource->tool : $resource->alias . '_r' . $resource->revision;
						$v = (!isset($resource->revision) or $resource->revision=='dev') ? 'test' : $resource->revision;
						if($isiPad && $launchOnIpad)
						{
							$lurl = 'nanohub://tools/invoke/' . $resource->alias . '/' . $v;
						}
						else
						{
							$lurl = 'index.php?option=com_tools&app=' . $resource->alias . '&task=invoke&version=' . $v;
						}
						
					} 
					elseif (!isset($resource->revision) or $resource->revision=='dev') 
					{ // serve dev version
						if($isiPad && $launchOnIpad)
						{
							$lurl = 'nanohub://tools/invoke/' . $resource->alias . '/dev';
						}
						else
						{
							$lurl = 'index.php?option=com_tools&app=' . $resource->alias . '&task=invoke&version=dev';
						}
					}
				} 
				else 
				{
					if($isiPad && $launchOnIpad)
					{
						$lurl = 'nanohub://tools/invoke/' . $resource->alias;
					}
					else
					{
						$lurl = 'index.php?option=com_tools&task=invoke&app=' . $resource->alias;
					}
				}

				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'tool.php');

				// Create some tool objects
				$hztv = ToolsModelVersion::getInstance($resource->tool);
				$ht = ToolsModelTool::getInstance($hztv->toolid);
				if ($ht) 
				{ // @FIXME: this only seems to fail on hubbub VMs where workspace resource is incomplete/incorrect (bad data in DB?)
					$toolgroups = $ht->getToolGroupsRestriction($hztv->toolid, $resource->tool);
				}

				// Get current users groups
				$xgroups = \Hubzero\User\Helper::getGroups($juser->get('id'), 'members');
				$ingroup = false;
				$groups = array();
				if ($xgroups) 
				{
					foreach ($xgroups as $xgroup)
					{
						$groups[] = $xgroup->cn;
					}
					// Check if they're in the admin tool group
					$admingroup = JComponentHelper::getParams('com_tools')->get('admingroup');
					if ($admingroup && in_array($admingroup, $groups))
					{
						$ingroup = true;
					}
					// Not in the admin group
					// Check if they're in the tool's group
					else
					{
						if ($toolgroups) 
						{
							foreach ($toolgroups as $toolgroup)
							{
								if (in_array($toolgroup->cn, $groups)) 
								{
									$ingroup = true;
								}
							}
						}
					}
				}

				if (!$juser->get('guest') && !$ingroup && $toolgroups) 
				{ // see if tool is restricted to a group and if current user is in that group
					$pop = self::warning(JText::_('
						WARNING: This tool is currently restricted to authorized members of the hub.
						If you need access, please submit a ticket to that effect and include the reason for your request.'));
					$html .= self::primaryButton('link_disabled', '', 'Launch Tool', '', '', '', 1, $pop);
				} 
				else if ((isset($resource->revision) && $resource->toolpublished) or !isset($resource->revision)) 
				{ // dev or published tool
					//if ($juser->get('guest')) { 
						// Not logged-in = show message
						//$html .= self::primaryButton('launchtool disabled', $lurl, 'Launch Tool');
						//$html .= self::warning('You must <a href="'.JRoute::_('index.php?option=com_login').'">log in</a> before you can run this tool.')."\n";
					//} else {
						$pop = ($juser->get('guest')) ? self::warning(JText::_('You must login before you can run this tool.')) : '';
						$pop = ($resource->revision =='dev') ? self::warning(JText::_('Warning: This tool version is under development and may not be run until it is installed.')) : $pop;
						$html .= self::primaryButton('launchtool', $lurl, JText::_('Launch Tool'), '', '', '', 0, $pop);
					//}
				} 
				else 
				{ // tool unpublished
					$pop   = self::warning(JText::_('This tool version is unpublished and cannot be run. If you would like to have this version staged, you can put a request through HUB Support.'));
					$html .= self::primaryButton('link_disabled', '', 'Launch Tool', '', '', '', 1, $pop);
					//$html .= self::warning($pop)."\n";
				}
			break;

			case 4:
				// write primary button and downloads for a Learning Module
				$html .= self::primaryButton('', JRoute::_('index.php?option=com_resources&id=' . $resource->id . '&task=play'), 'Start learning module');
			break;

			case 6:
			case 8:
			case 31:
			case 2:
				// do nothing
				$mesg  = JText::_('View').' ';
				$mesg .= $resource->type == 6 ? 'Course Lectures' : '';
				$mesg .= $resource->type == 2 ? 'Workshop ' : '';
				$mesg .= $resource->type == 6 ? '' : 'Series';
				$html .= self::primaryButton('download', JRoute::_('index.php?option=com_resources&id=' . $resource->id) . '#series', $mesg, '', $mesg, '');
			break;

			default:
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}
				
				$firstChild->title = str_replace('"', '&quot;', $firstChild->title);
				$firstChild->title = str_replace('&amp;', '&', $firstChild->title);
				$firstChild->title = str_replace('&', '&amp;', $firstChild->title);

				$mesg   = '';
				$class  = '';
				$action = '';
				$xtra   = '';

				//$lt = new ResourcesType($database);
				//$lt->load($firstChild->logicaltype);
				$lt = ResourcesType::getRecordInstance($firstChild->logicaltype);
				$ltparams = new $paramsClass($lt->params);

				//$rt = new ResourcesType($database);
				//$rt->load($firstChild->type);
				$rt = ResourcesType::getRecordInstance($firstChild->type);
				$tparams = new $paramsClass($rt->params);

				if ($firstChild->logicaltype) 
				{
					$rtLinkAction = $ltparams->get('linkAction', 'extension');
				} 
				else 
				{
					$rtLinkAction = $tparams->get('linkAction', 'extension');
				}

				switch ($rtLinkAction)
				{
					case 'download':
						$mesg  = 'Download';
						$class = 'download';
						//$action = 'rel="download"';
						$linkAction = 3;
					break;

					case 'lightbox':
						$mesg = 'View Resource';
						$class = 'play';
						//$action = 'rel="internal"';
						$linkAction = 2;
					break;

					case 'newwindow':
						$mesg = 'View Resource';
						//$class = 'popup';
						$action = 'rel="external"';
						$linkAction = 1;
					break;

					case 'extension':
					default:
						$linkAction = 0;

						//$mediatypes = array('11','20','34','19','37','32','15','40','41','15','76');
						$mediatypes = array('elink','quicktime','presentation','presentation_audio','breeze','quiz','player','video_stream','video','hubpresenter');
						//$downtypes = array('60','59','57','55');
						$downtypes = array('thesis','handout','manual','software_download');

						if (in_array($lt->alias, $downtypes)) 
						{
							$mesg  = 'Download';
							$class = 'download';
						} 
						elseif (in_array($rt->alias, $mediatypes)) 
						{
							$mesg  = 'View Presentation';
							$mediatypes = array('flash_paper','breeze','32','26');
							if (in_array($firstChild->type, $mediatypes)) 
							{
								$class = 'play';
							}
						} 
						else 
						{
							$mesg  = 'Download';
							$class = 'download';
						}

						if ($firstChild->standalone == 1) 
						{
							$mesg  = 'View Resource';
							$class = ''; //'play';
						}

						if (substr($firstChild->path, 0, 7) == 'http://'
						 || substr($firstChild->path, 0, 8) == 'https://'
						 || substr($firstChild->path, 0, 6) == 'ftp://'
						 || substr($firstChild->path, 0, 9) == 'mainto://'
						 || substr($firstChild->path, 0, 9) == 'gopher://'
						 || substr($firstChild->path, 0, 7) == 'file://'
						 || substr($firstChild->path, 0, 7) == 'news://'
						 || substr($firstChild->path, 0, 7) == 'feed://'
						 || substr($firstChild->path, 0, 6) == 'mms://') 
						{
							$mesg  = 'View Link';
						}
					break;
				}

				// IF (not a simulator) THEN show the first child as the primary button
				if ($firstChild->access==1 && $juser->get('guest')) 
				{
					// first child is for registered users only and the visitor is not logged in
					$pop  = '<p class="warning">This resource requires you to log in before you can proceed with the download.</p>' . "\n";
					$html .= self::primaryButton($class . ' disabled', JRoute::_('index.php?option=com_login'), $mesg, '', '', '', '', $pop);
				} 
				else 
				{
					$childParams = new $paramsClass($firstChild->params);
					$linkAction = intval($childParams->get('link_action', $linkAction));

					$url = self::processPath($option, $firstChild, $resource->id, $linkAction);

					switch ($linkAction)
					{
						case 3:
							$mesg  = 'Download';
							$class = 'download';
						break;

						case 2:
							$mesg  = 'View Resource';
							$class = 'play';
						break;

						case 1:
							$mesg = 'View Resource';
							//$class = 'popup';
							$action = 'rel="external"';
						break;

						case 0:
						default:
							// Do nothing
						break;
					}

					$attribs = new $paramsClass($firstChild->attribs);
					$width  = intval($attribs->get('width', 640));
					$height = intval($attribs->get('height', 360));
					if ($width > 0 && $height > 0) 
					{
						$class .= ' ' . $width . 'x' . $height;
					}

					//$xtra = '';
					//if ($firstChild->type == 13 || $firstChild->type == 15 || $firstChild->type == 33) {
						//$xtra = ' '. self::getFileAttribs($firstChild->path);
					//}

					//load a resouce type object on child resource type
					//$rt = new ResourcesType($database);
					//$rt->load($firstChild->type);

					//if we are a hubpresenter resource type, do not show file type in button
					if ($rt->alias == 'hubpresenter') 
					{
						//$xtra = "";
						//$class = "play 1000x600";
						$class = 'hubpresenter';
					} 
					else 
					{
						$mesg .= ' ' . self::getFileAttribs($firstChild->path);
					}

					if ($rt->alias == 'video') 
					{
						$class = 'video' . $class;
					}

					$pt = ResourcesType::getRecordInstance($resource->type);
					if ($pt->alias == 'databases')
					{
						$mesg = "Dataview";
					}

					if ($xact) 
					{
						$action = $xact;
					}

					$html .= self::primaryButton($class, $url, $mesg, $xtra, $firstChild->title, $action);
				}
			break;
		}

		return $html;
	}

	/**
	 * Generate the primary resources button
	 * 
	 * @param      string  $class    Class to add
	 * @param      string  $href     Link url
	 * @param      string  $msg      Link text
	 * @param      string  $xtra     Extra parameters to add (deprecated)
	 * @param      string  $title    Link title
	 * @param      string  $action   Link action
	 * @param      boolean $disabled Is the button disable?
	 * @param      string  $pop      Pop-up content
	 * @return     string
	 */
	public static function primaryButton($class, $href, $msg, $xtra='', $title='', $action='', $disabled=false, $pop = '')
	{
		$out = '';

		if ($disabled)
		{
			$out .= "\t" . '<p id="primary-document">' . "\n";
			$out .= "\t\t" . '<span class="' . $class . '">' . $msg . '</span>' . "\n";
			$out .= "\t" . '</p>' . "\n";
		}
		else
		{
			$title = htmlentities($title, ENT_QUOTES);

			$out .= "\t" . '<p id="primary-document">' . "\n";
			$out .= "\t\t" . '<a';
			$out .= ($class)  ? ' class="' . $class . '"' : '';
			$out .= ($href)   ? ' href="' . $href . '"'   : '';
			$out .= ($title)  ? ' title="' . $title . '"' : '';
			$out .= ($action) ? ' ' . $action             : '';
			$out .= '>' . $msg . '</a>' . "\n";
			$out .= "\t" . '</p>' . "\n";
		}

		if ($pop)
		{
			$out .= "\t" . '<div id="primary-document_pop">' . "\n";
			$out .= "\t\t" . '<div>' . $pop . '</div>' . "\n";
			$out .= "\t" . '</div>' . "\n";
		}

		return $out;
	}

	/**
	 * Display the file type and size for a file
	 * 
	 * @param      string  $path      File path
	 * @param      string  $base_path Path to prepend
	 * @param      integer $fsize     Format the filesize?
	 * @return     string
	 */
	public static function getFileAttribs($path, $base_path='', $fsize=0)
	{
		// Return nothing if no path provided
		if (!$path) 
		{
			return '';
		}

		if ($base_path) 
		{
			$base_path = DS . trim($base_path, DS);
		}

		if (preg_match("/(?:https?:|mailto:|ftp:|gopher:|news:|file:)/", $path))
		{
			$type = 'HTM';
			$fs = '';
		}
		else
		{
			$path = DS . trim($path, DS);
			// Ensure a starting slash
			if (substr($path, 0, strlen($base_path)) == $base_path) 
			{
				// Do nothing
			} 
			else 
			{
				$path = $base_path . $path;
			}

			$path = JPATH_ROOT . $path;

			jimport('joomla.filesystem.file');
			$type = strtoupper(JFile::getExt($path));

			//check to see if we have a json file (HUBpresenter)
			if ($type == 'JSON') 
			{
				$type = 'HTML5';
			}
			if (!$type)
			{
				$type = 'HTM';
			}

			// Get the file size if the file exist
			$fs = (file_exists($path)) ? filesize($path) : '';
		}

		$html  = '<span class="caption">(' . $type;
		if ($fs) 
		{
			switch ($type)
			{
				case 'HTM':
				case 'HTML':
				case 'PHP':
				case 'ASF':
				case 'SWF':
				case 'HTML5':
					$fs = '';
				break;

				default:
					$fs = ($fsize) ? $fs : \Hubzero\Utility\Number::formatBytes($fs, 2);
				break;
			}

			$html .= ($fs) ? ', ' . $fs : '';
		}
		$html .= ')</span>';

		return $html;
	}

	/**
	 * Format a filesize to more understandable Gb/Mb/Kb/b
	 * 
	 * @param      integer $fileSize File size to format
	 * @return     string
	 */
	public static function formatsize($file_size)
	{
		return \Hubzero\Utility\Number::formatBytes($file_size, 2);
	}

	/**
	 * Write a list of database results
	 * 
	 * @param      object &$database  JDatabase
	 * @param      array  &$lines     Database results
	 * @param      integer $show_edit Show edit controls?
	 * @param      integer $show_date Date to display
	 * @return     string HTML
	 */
	public static function writeResults(&$database, &$lines, $show_edit=0, $show_date=3)
	{
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$juser = JFactory::getUser();

		$config = JComponentHelper::getParams('com_resources');

		$html  = '<ol class="resources results">' . "\n";
		if (is_array($lines))
		{
			foreach ($lines as $line)
			{
				// Instantiate a helper object
				$helper = new ResourcesHelper($line->id, $database);
				$helper->getContributors();
				$helper->getContributorIDs();

				// Determine if they have access to edit
				if (!$juser->get('guest')) 
				{
					if ((!$show_edit && $line->created_by == $juser->get('id'))
					 || in_array($juser->get('id'), $helper->contributorIDs)) 
					{
						$show_edit = 2;
					}
				}

				// Get parameters
				$params = clone($config);
				$rparams = new $paramsClass($line->params);
				$params->merge($rparams);

				// Instantiate a new view
				$view = new JView(array(
					'name'   => 'browse',
					'layout' => 'item'
				));
				$view->option = 'com_resources';
				$view->config = $config;
				$view->params = $params;
				$view->juser  = $juser;
				$view->helper = $helper;
				$view->line   = $line;
				$view->show_edit = $show_edit;

				// Set the display date
				switch ($params->get('show_date'))
				{
					case 0: $view->thedate = ''; break;
					case 1: $view->thedate = JHTML::_('date', $line->created, JText::_('DATE_FORMAT_HZ1'));    break;
					case 2: $view->thedate = JHTML::_('date', $line->modified, JText::_('DATE_FORMAT_HZ1'));   break;
					case 3: $view->thedate = JHTML::_('date', $line->publish_up, JText::_('DATE_FORMAT_HZ1')); break;
				}

				$html .= $view->loadTemplate();
			}
		}
		$html .= '</ol>' . "\n";

		return $html;
	}
}
