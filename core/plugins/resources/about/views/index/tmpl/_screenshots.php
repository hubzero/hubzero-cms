<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = substr(PATH_APP, strlen(PATH_ROOT));

$upath = $base . (isset($this->upath) ? $this->upath : '');
$wpath = $base . (isset($this->wpath) ? $this->wpath : '');
$sinfo = (isset($this->sinfo) ? $this->sinfo : array());
$versionid = (isset($this->versionid) ? $this->versionid : 0);

$path = \Components\Resources\Helpers\html::build_path($this->created, $this->id, '');

// Get contribtool parameters
$tconfig = Component::params('com_tools');
$allowversions = $tconfig->get('screenshot_edit');

if ($versionid && $allowversions)
{
	// Add version directory
	$path .= DS . $versionid;
}

$d = @dir(PATH_ROOT . $upath . $path);

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
		if (is_file(PATH_ROOT . $upath . $path . DS . $img_file)
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
	$tn = \Components\Resources\Helpers\Html::thumbnail($images[$i]['img']);
	$els .=  ($this->slidebar && $i==0) ? '<div class="showcase-pane">' . "\n" : '';

	if (is_file(PATH_ROOT . $upath . $path . DS . $tn))
	{
		if (strtolower(end($images[$i]['type'])) == 'swf' || strtolower(end($images[$i]['type'])) == 'mov')
		{
			$g++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='') ? $images[$i]['title'] : Lang::txt('DEMO') . ' #' . $g;
			$els .= $this->slidebar ? '' : '<li>';
			$els .= ' <a class="popup" href="' . $wpath . $path . DS . $images[$i]['img'] . '" title="' . $title . '">';
			$els .= '<img src="' . $wpath . $path . DS . $tn . '" alt="' . $title . '" class="thumbima" /></a>';
			$els .= $this->slidebar ? '' : '</li>' . "\n";
		}
		else
		{
			$k++;
			$title = (isset($images[$i]['title']) && $images[$i]['title']!='')  ? $images[$i]['title']: Lang::txt('SCREENSHOT') . ' #' . $k;
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
