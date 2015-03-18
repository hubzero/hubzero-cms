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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$path = $this->path;
$pid = $this->publication->id;
$vid = $this->publication->version_id;

if (!$path || !$pid || !$vid)
{
	return;
}

$d = @dir(PATH_APP . $path);
if (!$d)
{
	return;
}

$database  = \JFactory::getDbo();
$pScreenshot = new \Components\Publications\Tables\Screenshot($database);
$shots = $pScreenshot->getScreenshots( $this->publication->version_id );


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
		if (is_file(PATH_APP . $path . DS . $img_file)
		 && substr($entry, 0, 1) != '.'
		 && strtolower($entry) !== 'index.html')
		{
			if (preg_match("#bmp|gif|jpeg|jpg|png|swf|mov#i", $img_file))
			{
				$images[] = $img_file;
			}
			if (preg_match("/_tn/i", $img_file))
			{
				$tns[] = $img_file;
			}
			$images = array_diff($images, $tns);
		}
	}

	$d->close();
}
if (empty($images))
{
	return false;
}

$b = 0;
foreach ($images as $ima)
{
	$new = array();
	$new['img'] = $ima;
	$new['type'] = explode('.', $new['img']);

	// get title and ordering info from the database, if available
	if (count($shots) > 0)
	{
		foreach ($shots as $si)
		{
			if ($si->srcfile == $ima)
			{
				$new['title'] = stripslashes($si->title);
				$new['title'] = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $new['title']);
				$new['ordering'] = $si->ordering;
			}
		}
	}
	if (!isset($new['title']))
	{
		// skip
		continue;
	}

	$ordering[] = isset($new['ordering']) ? $new['ordering'] : $b;
	$b++;
	$all[] = $new;
}

if (count($shots) > 0)
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

for ($i = 0, $n = count($images); $i < $n; $i++)
{
	$tn = \Components\Publications\Helpers\Html::createThumbName($images[$i]['img'], '_tn', $extension = 'png');
	$els .=  ($i==0 ) ? '<div class="showcase-pane">'."\n" : '';

	if (is_file(PATH_APP . $path . DS . $tn))
	{
		if (strtolower(end($images[$i]['type'])) == 'swf' || strtolower(end($images[$i]['type'])) == 'mov')
		{
			$g++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='') ? $images[$i]['title'] : JText::_('DEMO') . ' #' . $g;
			$els .= ' <a class="popup" href="/publications' . DS . $pid . DS . $vid . '/Image:' . $images[$i]['img'] . '" title="' . $title . '">';
			$els .= '<img src="/publications' . DS . $pid . DS . $vid . '/Image:' . $tn . '" alt="' . $title . '" class="thumbima" /></a>';
		}
		else
		{
			$k++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='')  ? $images[$i]['title']: JText::_('SCREENSHOT') . ' #' . $k;
			$els .= ' <a rel="lightbox" href="/publications' . DS . $pid . DS . $vid . '/Image:' . $images[$i]['img'] . '" title="' . $title . '">';
			$els .= '<img src="/publications' . DS . $pid . DS . $vid . '/Image:' . $tn . '" alt="' . $title . '" class="thumbima" /></a>';
		}
	}
	$els .=  ($i == ($n - 1)) ? '</div>' . "\n" : '';
}

if ($els)
{
	$html .= '<div id="showcase">'."\n" ;
	$html .= '<div id="showcase-prev" ></div>'."\n";
	$html .= '  <div id="showcase-window">'."\n";
	$html .= $els;
	$html .= '  </div>'."\n";
	$html .= '  <div id="showcase-next" ></div>'."\n";
	$html .= '</div>'."\n";
}

if ($html)
{
	$out  = ' <div class="sscontainer">'."\n";
	$out .= $html;
	$out .= ' </div><!-- / .sscontainer -->'."\n";
	echo $out;
}
?>