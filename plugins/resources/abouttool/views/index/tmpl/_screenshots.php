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

$upath = (isset($this->upath) ? $this->upath : '');
$wpath = (isset($this->wpath) ? $this->wpath : '');
$sinfo = (isset($this->sinfo) ? $this->sinfo : array());
$versionid = (isset($this->versionid) ? $this->versionid : 0);

$path = Resourceshtml::build_path($this->created, $this->id, '');

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
	$tn = ResourcesHtml::thumbnail($images[$i]['img']);
	$els .=  ($this->slidebar && $i==0) ? '<div class="showcase-pane">' . "\n" : '';

	if (is_file(JPATH_ROOT . $upath . $path . DS . $tn))
	{
		if (strtolower(end($images[$i]['type'])) == 'swf' || strtolower(end($images[$i]['type'])) == 'mov')
		{
			$g++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='') ? $images[$i]['title'] : JText::_('DEMO') . ' #' . $g;
			$els .= $this->slidebar ? '' : '<li>';
			$els .= ' <a class="popup" href="' . $wpath . $path . DS . $images[$i]['img'] . '" title="' . $title . '">';
			$els .= '<img src="' . $wpath . $path . DS . $tn . '" alt="' . $title . '" class="thumbima" /></a>';
			$els .= $this->slidebar ? '' : '</li>' . "\n";
		}
		else
		{
			$k++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='')  ? $images[$i]['title']: JText::_('SCREENSHOT') . ' #' . $k;
			$els .= $this->slidebar ? '' : '<li>';
			$els .= ' <a rel="lightbox" href="' . $wpath . $path . DS . $images[$i]['img'] . '" title="' . $title . '">';
			$els .= '<img src="' . $wpath . $path . DS . $tn . '" alt="' . $title . '" class="thumbima" /></a>';
			$els .= $this->slidebar ? '' : '</li>' . "\n";
		}
	}
	$els .=  ($this->slidebar && $i == ($n - 1)) ? '</div>' . "\n" : '';
}

if ($els) { ?>
	<div class="sscontainer">
		<?php if ($this->slidebar) { ?>
		<div id="showcase">
			<div id="showcase-prev" ></div>
			<div id="showcase-window">
				<ul class="screenshots">
		<?php } ?>

		<?php echo $els; ?>

		<?php if ($this->slidebar) { ?>
				</ul>
			</div>
			<div id="showcase-next" ></div>
		</div>
		<?php } ?>
	</div><!-- / .sscontainer -->
<?php }
