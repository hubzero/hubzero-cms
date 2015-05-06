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

		$params = new \JRegistry( $child->params );

		$serveas  = $params->get('serveas');
		$ftype 	  = $child->type == 'file' ? \Components\Projects\Helpers\Html::getFileExtension($child->path) : 'supporting';
		$class    = $params->get('class', $ftype);
		$doctitle = $params->get('title', $child->title);

		// Things we want to highlight
		$toShow = array('iTunes', 'iTunes U', 'Syllabus', 'Audio', 'Video', 'Slides');

		$url   = Route::url($publication->link('serve') . '&a=' . $child->id);
		$extra = '';

		switch ( $serveas )
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
			$supli[] = ' <li><a class="' . $class . '" href="' . $url . '" title="' . $child->title . '"'
				. $extra . '>' . $doctitle . '</a></li>' . "\n";
		}
	}
}

$sdocs = count( $supli ) > 2 ? 2 : count( $supli );
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
	$supln .= ' <li class="otherdocs"><a href="' . Route::url($publication->link() . '&active=supportingdocs')
		. '" title="' . Lang::txt('View All') . ' ' . $docs . ' ' . Lang::txt('Supporting Documents') . ' ">'
		. $otherdocs . ' ' . Lang::txt('more') . ' &rsaquo;</a></li>' . "\n";
}

if (!$sdocs && $docs > 0)
{
	$html .= "\t\t" . '<p class="viewalldocs"><a href="' . Route::url($publication->link() . '&active=supportingdocs') . '">'
		. Lang::txt('COM_PUBLICATIONS_IN_DEVELOPMENT_DOCS_AVAIL') . '</a></p>' . "\n";
}

$supln .= '</ul>' . "\n";
$html .= $sdocs ? $supln : '';
echo $html;
?>