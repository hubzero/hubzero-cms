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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$publication = $this->publication;
$children    = $publication->_attachments[2];
$archive     = $publication->bundlePath();

// Set counts
$docs = 0;

$html = '';
$supln  = '<ul class="supdocln">' . "\n";
$supli  = array();

// Archival package?
if (file_exists($archive) && $publication->base == 'databases')
{
	$supli[] = ' <li class="archival-package"><a href="' . Route::url($publication->link('serve'). '&render=archive') . '" title="'. Lang::txt('COM_PUBLICATIONS_DOWNLOAD_ARCHIVE_PACKAGE') . '">' . Lang::txt('COM_PUBLICATIONS_ARCHIVE_PACKAGE') . '</a></li>' . "\n";
	$docs++;
}

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

		$params = new \Hubzero\Config\Registry($child->params);

		$serveas  = $params->get('serveas');
		$ftype 	  = $child->type == 'file' ? \Components\Projects\Helpers\Html::getFileExtension($child->path) : 'supporting';
		$class    = $params->get('class', $ftype);
		$doctitle = $params->get('title', $child->title);

		// Things we want to highlight
		$toShow = array('iTunes', 'iTunes U', 'Syllabus', 'Audio', 'Video', 'Slides');

		$url   = Route::url($publication->link('serve') . '&a=' . $child->id);
		$extra = '';

		switch ($serveas)
		{
			case 'download':
			default:
				break;

			case 'external':
				$extra = ' rel="external"';
				break;

			case 'inlineview':
				$class = 'play';
				$url  .= '&amp;render=inline';
				break;
		}

		if (in_array($doctitle, $toShow))
		{
			$supli[] = ' <li><a class="' . $class . '" href="' . $url . '" title="' . $child->title . '"' . $extra . '>' . $doctitle . '</a></li>' . "\n";
		}
	}
}

$sdocs = count($supli) > 2 ? 2 : count($supli);
$otherdocs = $docs - $sdocs;
$otherdocs = ($sdocs + $otherdocs) == 3  ? 0 : $otherdocs;

for ($i=0; $i < count( $supli ); $i++)
{
	$supln .=  $i < 2 ? $supli[$i] : '';
	$supln .=  $i == 2 && !$otherdocs ? $supli[$i] : '';
}

// View more link?
if ($docs > 0 && $otherdocs > 0)
{
	$supln .= ' <li class="otherdocs"><a href="' . Route::url($publication->link() . '&active=supportingdocs&v=' . $publication->get('version_number'))
		. '" title="' . Lang::txt('View All') . ' ' . $docs . ' ' . Lang::txt('Supporting Documents') . ' ">'
		. $otherdocs . ' ' . Lang::txt('more') . ' &rsaquo;</a></li>' . "\n";
}

if (!$sdocs && $docs > 0)
{
	$html .= "\t\t" . '<p class="viewalldocs"><a href="' . Route::url($publication->link() . '&active=supportingdocs&v=' . $publication->get('version_number')) . '">'
		. Lang::txt('COM_PUBLICATIONS_IN_DEVELOPMENT_DOCS_AVAIL') . '</a></p>' . "\n";
}

$supln .= '</ul>' . "\n";
$html .= $sdocs ? $supln : '';

echo $html;