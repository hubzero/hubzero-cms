<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
