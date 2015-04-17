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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Helpers;

if (!defined('a'))
{
	define('a','&amp;');
}

/**
 * Html helper class
 */
class Html
{
	/**
	 * Get publication path
	 *
	 * @param      string 	$pid
	 * @param      string 	$vid
	 * @param      string 	$base
	 * @param      string 	$filedir
	 * @param      boolean 	$root
	 * @return     string
	 */
	public static function buildPubPath( $pid = NULL, $vid = NULL,
		$base = '', $filedir = '', $root = 0
	)
	{
		if ($vid === NULL or $pid === NULL )
		{
			return false;
		}
		if (!$base)
		{
			$pubconfig = Component::params( 'com_publications' );
			$base = $pubconfig->get('webpath');
		}

		$base = DS . trim($base, DS);

		$pub_dir     =  \Hubzero\Utility\String::pad( $pid );
		$version_dir =  \Hubzero\Utility\String::pad( $vid );
		$path        = $base . DS . $pub_dir . DS . $version_dir;
		$path        = $filedir ? $path . DS . $filedir : $path;
		$path        = $root ? PATH_APP . $path : $path;

		return $path;
	}

	/**
	 * Get publication thumbnail
	 *
	 * @param      int 		$pid
	 * @param      int 		$versionid
	 * @param      array 	$config
	 * @param      boolean 	$force
	 * @param      string	$cat
	 * @return     string HTML
	 */
	public static function getThumb($pid = 0, $versionid = 0, $config = NULL,
		$force = false, $cat = ''
	)
	{
		if (empty($config))
		{
			$config = Component::params( 'com_publications' );
		}

		// Get publication directory path
		$webpath     = DS . trim($config->get('webpath', 'site/publications'), DS);
		$path        = self::buildPubPath($pid, $versionid, $webpath);

		// Get default picture
		$default = $cat == 'tools'
				? $config->get('toolpic', '/components/com_publications/site/assets/img/tool_thumb.gif')
				: $config->get('defaultpic', '/components/com_publications/site/assets/img/resource_thumb.gif');

		// Check for default image
		if (is_file(PATH_APP . $path . DS . 'thumb.gif') && $force == false)
		{
			return $path . DS . 'thumb.gif';
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Show contributors
	 *
	 * @param      array 	$contributors
	 * @param      boolean 	$showorgs
	 * @param      boolean 	$showaslist
	 * @param      boolean 	$incSubmitter
	 *
	 * @return     string
	 */
	public static function showContributors( $contributors = '', $showorgs = false,
		$showaslist = false, $incSubmitter = false, $format = false
	)
	{
		$view = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'site',
			'name'      => 'view',
			'layout'    => '_contributors',
		));
		$view->contributors  = $contributors;
		$view->showorgs      = $showorgs;
		$view->showaslist    = $showaslist;
		$view->incSubmitter  = $incSubmitter;
		$view->format        = $format;
		return $view->loadTemplate();
	}

	/**
	 * Display a list of skill levels
	 *
	 * @param      array   $levels List of levels
	 * @param      integer $sel    Selected level
	 * @return     string HTML
	 */
	public static function skillLevelCircle( $levels = array(), $sel = 0 )
	{
		$html = '';

		$html.= '<div class="audience_wrap">' . "\n";
		$html.= '<ul class="audiencelevel">' . "\n";
		foreach ($levels as $level)  {
			$class = $level->label != $sel ? ' isoff' : '';
			$class = $level->label != $sel && $level->label == 'level0' ? '_isoff' : $class;
			if ($level->label != $sel && $sel == 'level0') {
				$class .= " hidden";
			}
			$html .= ' <li class="'.$level->label.$class.'"><span>&nbsp;</span></li>' . "\n";
		}
		$html.= '</ul>' . "\n";
		$html.= '</div>' . "\n";
		return $html;
	}

	/**
	 * Show license information for a publication
	 *
	 * @param      object  $publication   	Publication object
	 * @param      string  $version     	Version name
	 * @param      string  $option 			Component name
	 * @param      object  $license 		Publication license object
	 * @param      string  $class  			CSS class for the license hyperlink
	 * @return     string HTML
	 */
	public static function showLicense( $publication, $version, $option, $license = '', $class = "showinbox" )
	{
		if (!$license)
		{
			return false;
		}

		$cls = strtolower($license->name);
		$custom = $publication->license_text ? $publication->license_text : '';
		$custom = !$custom && $license->text ? $license->text : $custom;
		$lnk = $license->url ? $license->url : '';
		$title = strtolower($license->title) != 'custom' ? $license->title : '';
		$url = Route::url('index.php?option=' . $option . '&id=' . $publication->id . '&task=license&v=' . $version);

		$html  = '<p class="' . $cls . ' license">'.Lang::txt('COM_PUBLICATIONS_LICENSED_UNDER').' ';
		if ($title)
		{
			if ($lnk && !$custom)
			{
				$html .= '<a href="' . $lnk . '" rel="external">' . $title . '</a>';
			}
			else
			{
				$html .= $title . ' ' . Lang::txt('COM_PUBLICATIONS_LICENSED_ACCORDING_TO') . ' ';
				$html .= '<a href="' . $url . '" class="' . $class . '">'.Lang::txt('COM_PUBLICATIONS_LICENSED_THESE_TERMS') . '</a>';
			}
		}
		else
		{
			$html .= '<a href="' . $url . '" class="' . $class . '">' . Lang::txt('COM_PUBLICATIONS_LICENSED_THESE_TERMS') . '</a>';
		}
		$html .= '</p>';

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
	public static function sections( $sections, $cats, $active='about', $h, $c )
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
				$cls  = ($c) ? $c.' ' : '';
				if (key($cats[$k]) != $active)
				{
					$cls .= ($h) ? $h.' ' : '';
				}
				$html .= '<div class="' . $cls . 'section" id="' . key($cats[$k]) . '-section">' . $section['html'] . '</div>';
			}
			$k++;
		}

		return $html;
	}

	/**
	 * Output tab controls for resource plugins (sub views)
	 *
	 * @param      string $option Component name
	 * @param      string $id     Publication ID
	 * @param      array  $cats   Active plugins' names
	 * @param      string $active Current plugin name
	 * @param      string $alias  Publication alias
	 * @param      string $version  Publication version
	 * @return     string HTML
	 */
	public static function tabs( $option, $id, $cats, $active = 'about', $alias = '', $version = '' )
	{
		$html  = '';
		$html .= "\t".'<ul class="sub-menu">' . "\n";
		$i = 1;
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name == 'usage')
			{
				continue;
			}
			if ($name != '')
			{
				if ($alias)
				{
					$url = Route::url('index.php?option=' . $option . '&alias=' . $alias . '&active=' . $name);
				}
				else
				{
					$url = Route::url('index.php?option=' . $option . '&id=' . $id . '&active=' . $name);
				}
				if ($version && $version != 'default')
				{
					$url .= '?v=' . $version;
				}
				if (strtolower($name) == $active)
				{
					Pathway::append($cat[$name],$url);

					if ($active != 'about')
					{
						$document = \JFactory::getDocument();
						$title = $document->getTitle();
						$document->setTitle( $title.': '.$cat[$name] );
					}
				}
				$html .= "\t\t".'<li id="sm-' . $i . '"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="' . $name . '" href="' . $url . '"><span>' . $cat[$name] . '</span></a></li>' . "\n";
				$i++;
			}
		}
		$html .= "\t".'</ul>'."\n";

		return $html;
	}

	/**
	 * Generate COins microformat
	 *
	 * @param      object $cite     	Pub citation data
	 * @param      object $publication 	Publication
	 * @param      object $config   	Component config
	 * @param      object $helper   	Publication Helper
	 * @return     string HTML
	 */
	public static function citationCOins($cite, $publication, $config, $helper)
	{
		if (!$cite)
		{
			return '';
		}

		$html  = '<span ';
		$html .= ' class="Z3988"';
		$html .= ' title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal';
		$html .= isset($publication->doi)
			? '&amp;rft_id=info%3Adoi%2F'.$publication->doi
			: '';

		$html .= '&amp;rft.genre=unknown';
		$html .= '&amp;rft.atitle='.urlencode($cite->title);
		$html .= '&amp;rft.date='.urlencode($cite->year);

		$author_array = $publication->_authors;

		if ($author_array)
		{
			for ($i = 0; $i < count($author_array); $i++)
			{
				if ($author_array[$i]->lastName || $author_array[$i]->firstName)
				{
					$name = stripslashes($author_array[$i]->firstName) .' ';
					$name .= stripslashes($author_array[$i]->lastName);
				}
				else
				{
					$name = $author_array[$i]->name;
				}

				$html.= '&amp;rft.au='.urlencode($name);
			}
		}

		$html.= '"></span>'."\n";

		return $html;
	}

	/**
	 * Generate a citation for a publication
	 *
	 * @param      string  $option    Component name
	 * @param      object  $cite      Citation data
	 * @param      object  $pub       Publication
	 * @param      string  $citations Citations to prepend
	 * @param      string  $version   Version name
	 * @return     string HTML
	 */
	public static function citation( $option, $cite, $pub, $citations, $version = 'default')
	{
		include_once( PATH_CORE . DS . 'components' . DS . 'com_citations' . DS
			. 'helpers' . DS . 'format.php' );
		include_once( PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php' );

		$cconfig  = Component::params( 'com_citations' );

		$template = "{AUTHORS} ({YEAR}). <b>{TITLE/CHAPTER}</b>. <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER}, {SERIES}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b> {PAGES}, {ORGANIZATION}, {INSTITUTION}, {SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}. {PUBLISHER}. doi:{DOI}";

		$formatter = new \Components\Citations\Helpers\Format();
		$formatter->setTemplate($template);

		$html  = '<p>'.Lang::txt('COM_PUBLICATIONS_CITATION_INSTRUCTIONS').'</p>'."\n";
		$html .= $citations;
		if ($cite)
		{
			$html .= '<ul class="citations results">'."\n";
			$html .= "\t".'<li>'."\n";

			$formatted = $formatter->formatCitation($cite, false, true, $cconfig);

			$formatted = str_replace('"', '', $formatted);
			if ($cite->doi && $cite->url)
			{
				$formatted = str_replace('doi:' . $cite->doi, '<a href="' . $cite->url . '" rel="external">'
					. 'doi:' . $cite->doi . '</a>', $formatted);
			}
			else
			{
				$formatted = str_replace('doi:', '', $formatted);
			}

			$html .= $formatted;
			if ($version != 'dev')
			{
				$html .= "\t\t".'<p class="details">'."\n";
				$html .= "\t\t\t".'<a href="index.php?option='.$option.'&task=citation&id='
					.$pub->id.'&format=bibtex&no_html=1&v='.$version.'" title="'
					.Lang::txt('COM_PUBLICATIONS_DOWNLOAD_BIBTEX_FORMAT').'">BibTex</a> <span>|</span> '."\n";
				$html .= "\t\t\t".'<a href="index.php?option='.$option.'&task=citation&id='
					.$pub->id.'&format=endnote&no_html=1&v='.$version.'" title="'
					.Lang::txt('COM_PUBLICATIONS_DOWNLOAD_ENDNOTE_FORMAT').'">EndNote</a>'."\n";
				$html .= "\t\t".'</p>'."\n";
			}
			$html .= "\t".'</li>'."\n";
			$html .= '</ul>'."\n";
		}

		return $html;
	}

	/**
	 * Process metadata for a publication
	 *
	 * @param      string  $metadata  	Pub metadata
	 * @param      object  $category  	Category
	 * @param      int     $table 		Show in html table?
	 * @return     array
	 */
	public static function processMetadata( $metadata, $category, $table = 1 )
	{
		$html 		= '';
		$citations 	= '';

		if (!$metadata)
		{
			return false;
		}

		// Parse data
		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $metadata, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = self::_txtUnpee($match[2]);
			}
		}

		$customFields = $category->customFields && $category->customFields != '{"fields":[]}'
						? $category->customFields
						: '{"fields":[{"default":"","name":"citations","label":"Citations","type":"textarea","required":"0"}]}';

		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications'
			. DS . 'models' . DS . 'elements.php');

		$elements 	= new \Components\Publications\Models\Elements($data, $customFields);
		$schema 	= $elements->getSchema();

		foreach ($schema->fields as $field)
		{
			if (isset($data[$field->name]))
			{
				if ($field->name == 'citations')
				{
					$citations = $data[$field->name];
				}
				elseif ($value = $elements->display($field->type, $data[$field->name]))
				{
					if ($table)
					{
						$html .= self::tableRow( $field->label, $value );
					}
					else
					{
						$html .= '<p class="pub-review-label">' . $field->label . '</p>';
						$html .= $value;
					}
				}
			}
		}

		return array('html' => $html, 'citations' => $citations);
	}

	/**
	 * Display certain supporting docs and/or link to more
	 *
	 * @param      object  $publication   	Publication object
	 * @param      string  $version     	Version name
	 * @param      string  $option 			Component name
	 * @param      object  $children 		Publication attachments
	 * @param      boolean $restricted
	 * @return     string HTML
	 */
	public static function sortSupportingDocs( $publication, $version, $option, $children, $restricted, $archive = '' )
	{
		if ($restricted)
		{
			return false;
		}

		// Set counts
		$docs = 0;

		$html = '';
		$supln  = '<ul class="supdocln">'."\n";
		$supli  = array();

		// Archival package?
		if (file_exists($archive) && $publication->base == 'databases')
		{
			$url = Route::url('index.php?option=com_publications&id='.$publication->id.'&task=serve&v=' . $version . '&render=archive');
			$supli[] = ' <li class="archival-package"><a href="'.$url.'" title="'. Lang::txt('COM_PUBLICATIONS_DOWNLOAD_ARCHIVE_PACKAGE') .'">' . Lang::txt('COM_PUBLICATIONS_ARCHIVE_PACKAGE') . '</a></li>'."\n";
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

				$params = new \JParameter( $child->params );

				// Get default serving option
				$defaultServeas = $publication->pubTypeHelper->dispatchByType($child->type, 'getProperty',
					$data = array('property' => '_serveas') );

				$serveas = $params->get('serveas', $defaultServeas);
				$ftype 	 = self::getFileExtension($child->path);
				$class   = $params->get('class', $ftype);
				$doctitle = $params->get('title', $child->title);

				// Things we want to highlight
				$toShow = array('iTunes', 'iTunes U', 'Syllabus', 'Audio', 'Video', 'Slides');

				$url   = Route::url('index.php?option=com_publications&id='.$publication->id
						. '&task=serve&v=' . $version . '&a=' . $child->id);
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
					$supli[] = ' <li><a class="'.$class.'" href="'.$url.'" title="'.$child->title.'"'
						. $extra . '>'.$doctitle.'</a></li>'."\n";
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
			$supln .= ' <li class="otherdocs"><a href="' . Route::url('index.php?option=' . $option
				. '&id=' . $publication->id . '&active=supportingdocs')
				.'" title="' . Lang::txt('View All') . ' ' . $docs.' ' . Lang::txt('Supporting Documents').' ">'
				. $otherdocs . ' ' . Lang::txt('more') . ' &rsaquo;</a></li>' . "\n";
		}

		if (!$sdocs && $docs > 0)
		{
			$html .= "\t\t" . '<p class="viewalldocs"><a href="' . Route::url('index.php?option='
				. $option . '&id=' . $publication->id . '&active=supportingdocs') . '">'
				. Lang::txt('COM_PUBLICATIONS_IN_DEVELOPMENT_DOCS_AVAIL') . '</a></p>'."\n";
		}

		$supln .= '</ul>'."\n";
		$html .= $sdocs ? $supln : '';
		return $html;
	}

	/**
	 * Show version info
	 *
	 * @param      object  $publication   	Publication object
	 * @param      string  $version     	Version name
	 * @param      string  $option 			Component name
	 * @param      array   $config
	 * @param      object $lastPubRelease
	 * @return     string HTML
	 */
	public static function showVersionInfo( $publication, $version, $option, $config, $lastPubRelease )
	{
		$dateFormat = 'M d, Y';
		$tz = false;

		$text = '';
		if ($version == 'dev')
		{
			// Dev version
			$class = 'devversion';
			$text .= Lang::txt('COM_PUBLICATIONS_VERSION').' <strong>'
			      .$publication->version_label.'</strong> ('.Lang::txt('COM_PUBLICATIONS_IN_DEVELOPMENT').')';
			$text .= '<span class="block">'.Lang::txt('COM_PUBLICATIONS_CREATED').' ';
			$text .= Lang::txt('COM_PUBLICATIONS_ON').' '.\JHTML::_('date', $publication->created, $dateFormat, $tz).'</span>';
		}
		else
		{
			$class = 'curversion';
			$text .= ($publication->main == 1 || $publication->state == 1) ? ''
				: '<strong>'.Lang::txt('COM_PUBLICATIONS_ARCHIVE').'</strong> ';
			$text .= Lang::txt('COM_PUBLICATIONS_VERSION').' <strong>'.$publication->version_label.'</strong>';
			$now = Date::toSql();

			switch ($publication->state)
			{
				case 1:
					$text .= ' - ';
					$text .= ($publication->published_up > $now)
						? Lang::txt('COM_PUBLICATIONS_TO_BE_RELEASED')
						: strtolower(Lang::txt('COM_PUBLICATIONS_PUBLISHED'));
					$text .= ' ' . Lang::txt('COM_PUBLICATIONS_ON').' '
						  .\JHTML::_('date', $publication->published_up, $dateFormat, $tz).' ';
					break;
				case 4:
					$text .= ' ('.strtolower(Lang::txt('COM_PUBLICATIONS_READY')).')';
					$text .= '<span class="block">'.Lang::txt('COM_PUBLICATIONS_FINALIZED').' ';
					$text .= Lang::txt('COM_PUBLICATIONS_ON') . ' '
					. \JHTML::_('date', $publication->published_up, $dateFormat, $tz).'</span>';
					$class = 'ready';
					break;
				case 5:
				case 7:
					$text .= $publication->state == 5
							? ' ('.strtolower(Lang::txt('COM_PUBLICATIONS_PENDING_APPROVAL')).')'
							: ' ('.strtolower(Lang::txt('COM_PUBLICATIONS_PENDING_WIP')).')';
					$text .= '<span class="block">'.Lang::txt('COM_PUBLICATIONS_SUBMITTED').' ';
					$text .= Lang::txt('COM_PUBLICATIONS_ON') . ' '
					.\JHTML::_('date', $publication->submitted, $dateFormat, $tz).'</span>';
					if ($publication->published_up > $now)
					{
						$text .= '<span class="block">';
						$text .= Lang::txt('COM_PUBLICATIONS_TO_BE_RELEASED') . ' ' . Lang::txt('COM_PUBLICATIONS_ON') . ' '
							. \JHTML::_('date', $publication->published_up, $dateFormat, $tz);
						$text .= '</span>';
					}
					$class = 'pending';
					break;
				case 0:
					$text .= ' ('.strtolower(Lang::txt('COM_PUBLICATIONS_UNPUBLISHED')).')';
					$text .= '<span class="block">'.Lang::txt('COM_PUBLICATIONS_RELEASED').' ';
					$text .= Lang::txt('COM_PUBLICATIONS_ON') . ' '
					. \JHTML::_('date', $publication->published_up, $dateFormat, $tz).'</span>';
					$class = $publication->main == 1 ? 'unpublished' : 'archive';
					break;
			}
		}

		// Show DOI if available
		if ($version != 'dev' && $publication->doi)
		{
			$text .= "\t\t".'<span class="doi">'.'doi:'.$publication->doi;
			$text .= ' - <span><a href="'. Route::url('index.php?option='.$option.'&id='.
			$publication->id.'&active=about'). '#citethis">'.Lang::txt('cite this').'</a></span></span>'."\n";
		}
		// Show archival status (mkAIP)
		if ($config->get('repository', 0))
		{
			if ($publication->doi && $publication->archived && $publication->archived != '0000-00-00 00:00:00')
			{
				$text .= "\t\t".'<span class="archival-notice archived">'. Lang::txt('COM_PUBLICATIONS_VERSION_ARCHIVED_ON_DATE') . ' ' . \JHTML::_('date', $publication->archived, $dateFormat, $tz) . "\n";
			}
			elseif ($config->get('graceperiod', 0))
			{
				$archiveDate  = $publication->accepted && $publication->accepted != '0000-00-00 00:00:00' ? \JFactory::getDate($publication->accepted . '+1 month')->toSql() : NULL;

				// Skip notice if archive date passed
				if (strtotime($archiveDate) > strtotime(\JFactory::getDate()))
				{
					$text .= $archiveDate ? "\t\t".'<span class="archival-notice unarchived">'. Lang::txt('COM_PUBLICATIONS_VERSION_TO_BE_ARCHIVED') . ' ' . \JHTML::_('date', $archiveDate, $dateFormat) . "\n" : '';
				}
			}
		}

		// Show current release information
		if ($lastPubRelease && $lastPubRelease->id != $publication->version_id)
		{
			$text .= "\t\t" . '<span class="block">' . Lang::txt('COM_PUBLICATIONS_LAST_PUB_RELEASE')
			. ' <a href="'. Route::url('index.php?option=' . $option . '&id=' .
			$publication->id . '&v=' . $lastPubRelease->version_number) . '">' . $lastPubRelease->version_label . '</a></span>';
		}

		// Output
		if ($text)
		{
			return '<p class="' . $class . '">' . $text . '</p>';
		}
		return false;
	}

	/**
	 * Show access message
	 *
	 * @param      object  $publication   	Publication object
	 * @param      string  $option 			Component name
	 * @param      boolean $authorized
	 * @param      boolean $restricted
	 * @param      string  $editlink
	 * @return     string HTML
	 */
	public static function showAccessMessage( $publication)
	{
		$msg = '';
		$now = Date::toSql();

		// Show message to restricted users
		if (!$publication->access('view-all'))
		{
			$class = 'warning';
			switch ($publication->access)
			{
				case 1:
					$msg = Lang::txt('COM_PUBLICATIONS_STATUS_MSG_REGISTERED');
					break;
				case 2:
					$msg = Lang::txt('COM_PUBLICATIONS_STATUS_MSG_RESTRICTED');
					break;
				case 3:
					$msg = Lang::txt('COM_PUBLICATIONS_STATUS_MSG_PRIVATE');
					break;
			}
		}
		// Show message to publication owners
		elseif ($publication->access('manage'))
		{
			if ($publication->_project->isProvisioned() == 0)
			{
				$project  = Lang::txt('COM_PUBLICATIONS_FROM_PROJECT');
				$project .= $publication->access('owner')
					? ' <a href="' . Route::url('index.php?option=com_projects&alias='
					. $publication->_project->get('alias')) .'">'
					: ' <strong>';
				$project .= \Hubzero\Utility\String::truncate($publication->_project->get('title'), 50);
				$project .= $publication->access('owner') ? '</a>' : '</strong>';
				$msg .= ' <span class="fromproject">' . $project . '</span>';
			}

			$class= 'info';
			switch ($publication->state)
			{
				case 1:
					$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_PUBLISHED').' ';
					switch ($publication->access)
					{
						case 0:
							$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_WITH_PUBLIC');
							break;
						case 1:
							$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_WITH_REGISTERED');
							break;
						case 2:
							$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_WITH_RESTRICTED');
							break;
						case 3:
							$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_WITH_PRIVATE');
							break;
					}

					if ($publication->published_up > $now)
					{
						$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_PUBLISHED_EMBARGO')
							. ' ' . \JHTML::_('date', $publication->published_up, 'm d, Y');
					}

					break;

				case 4:
					$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_POSTED');
					break;

				case 3:
					$msg .= $publication->versions
					     ? Lang::txt('COM_PUBLICATIONS_STATUS_MSG_DRAFT_VERSION')
					     : Lang::txt('COM_PUBLICATIONS_STATUS_MSG_DRAFT');
					break;

				case 0:
					$msg .= $publication->default_version_status == 0
						 ? Lang::txt('COM_PUBLICATIONS_STATUS_MSG_UNPUBLISHED')
						 : Lang::txt('COM_PUBLICATIONS_STATUS_MSG_UNPUBLISHED_VERSION');
					break;

				case 5:
					$msg .= $publication->versions
						 ? Lang::txt('COM_PUBLICATIONS_STATUS_MSG_PENDING')
						 : Lang::txt('COM_PUBLICATIONS_STATUS_MSG_PENDING_VERSION');
					break;

				case 7:
					$msg .= Lang::txt('COM_PUBLICATIONS_STATUS_MSG_WIP');
					break;
			}
			if ($publication->access('curator') && !$publication->access('owner') && !$publication->published())
			{
				$msg .= ' ' . Lang::txt('You are viewing this publication as a curator.');
			}

			if ($publication->access('owner'))
			{
				// Build pub url
				$route = $publication->_project->isProvisioned() == 1
						? 'index.php?option=com_publications&task=submit'
						: 'index.php?option=com_projects&alias=' . $publication->_project->get('alias') . '&active=publications';

				$msg .= ' <a href="' . Route::url($route . '&pid=' . $publication->id) . '?version=' . $publication->versionAlias . '">' . Lang::txt('COM_PUBLICATIONS_STATUS_MSG_MANAGE_PUBLICATION') . '</a>.';
			}
		}

		if ($msg)
		{
			return '<p class="' . $class . ' statusmsg">' . $msg . '</p>';
		}

		return false;
	}

	/**
	 * Get status
	 *
	 * @param      int  $state
	 * @return     string HTML
	 */
	public static function getState( $state )
	{
		switch ($state)
		{
			case 0:
				return strtolower(Lang::txt('COM_PUBLICATIONS_UNPUBLISHED'));
				break;

			case 4:
				return strtolower(Lang::txt('COM_PUBLICATIONS_POSTED'));
				break;

			case 5:
				return strtolower(Lang::txt('COM_PUBLICATIONS_PENDING'));
				break;

			case 6:
				return strtolower(Lang::txt('COM_PUBLICATIONS_ARCHIVE'));
				break;

			case 1:
			default:
				return strtolower(Lang::txt('COM_PUBLICATIONS_PUBLISHED'));
				break;
		}
	}

	/**
	 * Show supplementary info
	 *
	 * @param      object  $publication   	Publication object
	 * @param      string  $option 			Component name
	 * @return     string HTML
	 */
	public static function showSubInfo( $publication, $option )
	{
		$action = $publication->state == 1 ? Lang::txt('COM_PUBLICATIONS_LISTED_IN') : Lang::txt('COM_PUBLICATIONS_IN');
		$html = '<p class="pubinfo">' . $action . ' ' . ' <a href="' . Route::url('index.php?option='
			. $option . '&category=' . $publication->_category->url_alias).'">' . $publication->_category->name . '</a>';

		// Show group if group project
		$groupId = $publication->project_group ? $publication->project_group : $publication->group_owner;
		if ($groupId)
		{
			$group = new \Hubzero\User\Group();
			if (\Hubzero\User\Group::exists($groupId))
			{
				$group = \Hubzero\User\Group::getInstance( $groupId );
				$html .= ' | ' . Lang::txt('COM_PUBLICATIONS_PUBLICATION_BY_GROUP')
						.' <a href="/groups/' . $group->get('cn') . '">'
						. $group->get('description').'</a>';
			}
		}
		$html .= '</p>'."\n";

		return $html;
	}

	/**
	 * Show title
	 *
	 * @param      string  $option 			Component name
	 * @param      object  $publication   	Publication object
	 * @return     string HTML
	 */
	public static function title( $option, $publication )
	{
		$txt   = stripslashes($publication->title);

		// Include version label?
		if (isset($publication->_curationModel))
		{
			$params = $publication->_curationModel->_manifest->params;
			if (isset($params->appendVersionLabel) && $params->appendVersionLabel == 1)
			{
				$txt .= ' ' . $publication->version_label;
			}
		}

		$html  = '<h2>' . $txt . '</h2>' . "\n";
		$html  = '<header id="content-header">' . $html . '</header>';

		return $html;
	}

	/**
	 * Show pre-defined publication footer (curated)
	 *
	 * @param      object  $publication   	Publication object
	 * @return     string HTML
	 */
	public static function footer( $publication, $html = '' )
	{
		if (isset($publication->_curationModel))
		{
			$params = $publication->_curationModel->_manifest->params;
			if (isset($params->footer) && $params->footer)
			{
				$html = '<div class="pub-footer">' . $params->footer . '</div>';
			}
		}

		return $html;
	}

	/**
	 * Draw black button
	 *
	 * @param      string  $option 			Component name
	 * @param      object  $publication   	Publication object
	 * @param      string  $version     	Version name
	 * @param      array   $content 		Publication attachments
	 * @param      string  $path
	 * @param      string  $serveas
	 * @param      boolean $restricted
	 * @param      boolean $authorized
	 * @return     string HTML
	 */
	public static function drawPrimaryButton(
		$option, $publication, $version,
		$content, $serveas = 'download',
		$restricted = 0, $authorized = 0 )
	{

		$task 		= 'serve';
		$url  		= Route::url('index.php?option=com_publications&id='
					. $publication->id . '&v=' . $publication->version_number . '&task=' . $task);
		$action 	= '';
		$xtra 		= '';
		$title  	= 'Access publication';
		$pop    	= '';
		$class  	= 'btn btn-primary icon-next ';
		$disabled 	= 0;
		$msg		= 'Access Publication';

		// Is content available?
		if ($publication->state == 0)
		{
			$class     .= 'link_disabled';
			$pop 		= Lang::txt('COM_PUBLICATIONS_STATE_UNPUBLISHED_POP');
			$disabled   = 1;
		}
		elseif ($restricted && !$authorized)
		{
			$class 		.= 'link_disabled';
			$pop 		= $publication->access == 1
			     ? Lang::txt('COM_PUBLICATIONS_STATE_REGISTERED_POP')
			     : Lang::txt('COM_PUBLICATIONS_STATE_RESTRICTED_POP');
			$disabled = 1;
		}
		if ($content['1'][0]->type == 'link' )
		{
			$serveas = 'external';
		}
		if ($content['1'][0]->type == 'tool' )
		{
			$serveas = 'invoke';
		}

		$primary = $content['1'][0];
		switch ($serveas)
		{
			case 'download':
			case 'tardownload':
			default:
				$msg   = Lang::txt('COM_PUBLICATIONS_DOWNLOAD_PUBLICATION');
				$xtra  = count($content['1']) == 1
					? strtoupper(self::getFileExtension($content['1'][0]->path))
					: NULL;
				$extra = (count($content['1']) > 1 || $serveas == 'tardownload') ? 'ZIP' : NULL;
				break;

			case 'video':
			case 'inlineview':
				$msg   = Lang::txt('COM_PUBLICATIONS_VIEW_PUBLICATION');

				if (!$disabled)
				{
					$class .= 'play';
				}
				break;

			case 'invoke':
				$msg    = Lang::txt('Launch tool');
				$class .= 'launchtool';
				break;

			case 'external':

				if ($content['1'][0]->type == 'note')
				{
				//	$class = 'play'; // lightboxed
				}
				else
				{
					$action = 'rel="external"';
				}

				break;
		}

		$title = $title ? $title : $msg;
		$pop   = $pop ? '<p class="warning">' . $pop . '</p>' : '';

		return self::primaryButton($class, $url, $msg, $xtra, $title, $action, $disabled, $pop);
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
	public static function drawLauncher( $icon, $pub, $url, $title, $disabled, $pop, $action = 'download',  $showArchive = false)
	{
		if ($disabled)
		{
			// TBD
			echo '<p class="unavailable warning">' . Lang::txt('COM_PUBLICATIONS_ERROR_CONTENT_UNAVAILABLE') . '</p>';
		}
		else
		{
			$archiveUrl = Route::url('index.php?option=com_publications&id=' . $pub->id . '&task=serve&v=' . $pub->version_number . '&render=archive');
			?>
			<div class="button-highlighter">
				<p class="launch-primary <?php echo $icon; ?>"><a href="<?php echo $url; ?>" title="<?php echo $title; ?>" id="launch-primary"></a></p>
				<?php if ($showArchive == true) { ?>
				<div class="launch-choices hidden" id="launch-choices">
					<div>
						<p><a href="<?php echo $url; ?>" title="<?php echo $title; ?>" class="download"><?php echo $title; ?></a></p>
						<p><a href="<?php echo $archiveUrl; ?>" class="archival" title="<?php echo Lang::txt('COM_PUBLICATIONS_DOWNLOAD_BUNDLE'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_ARCHIVE_PACKAGE'); ?></a></p>
					</div>
				</div>
			<?php } ?>
			</div>

	<?php	}
	}

	/**
	 * Write publication category
	 *
	 * @param      string $cat_alias
	 * @param      string $typetitle
	 * @return     string HTML
	 */
	public static function writePubCategory( $cat_alias = '', $typetitle = '' )
	{
		$html = '';

		if (!$cat_alias && !$typetitle)
		{
			return false;
		}

		if ($cat_alias)
		{
			$cls = str_replace(' ', '', $cat_alias);
			$title = $cat_alias;
		}
		elseif ($pubtitle)
		{
			$normalized = strtolower($typetitle);
			$cls = preg_replace("/[^a-zA-Z0-9]/", "", $normalized);

			if (substr($normalized, -3) == 'ies')
			{
				$title = $normalized;
				$cls = $cls;
			}
			else
			{
				$title = substr($normalized, 0, -1);
				$cls = substr($cls, 0, -1);
			}
		}

		$html .= '<span class="' . $cls . '"></span> ' . $title;

		return $html;
	}

	/**
	 * Show publication title
	 *
	 * @param      object $pub
	 * @param      string $url
	 * @param      string $tabtitle
	 * @param      string $append
	 * @return     string HTML
	 */
	public static function showPubTitle( $pub, $url, $tabtitle = '', $append = '' )
	{
		$typetitle = self::writePubCategory($pub->cat_alias, $pub->cat_name);
		$tabtitle  = $tabtitle ? $tabtitle : ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS'));
		$pubUrl    = Route::url($url . '&pid=' . $pub->id) .'?version=' . $pub->version_number;
		?>
		<div id="plg-header">
			<h3 class="publications c-header"><a href="<?php echo $url; ?>" title="<?php echo $tabtitle; ?>"><?php echo $tabtitle; ?></a> &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist">"<?php if ($append) { echo '<a href="' . $pubUrl . '" >'; } ?><?php echo \Hubzero\Utility\String::truncate($pub->title, 65); ?>"<?php if ($append) { echo '</a>'; } ?></span>
			<?php if ($append) { echo $append; } ?>
			</h3>
		</div>
	<?php
	}

	/**
	 * Show pub title for provisioned projects
	 *
	 * @param      object $pub
	 * @param      string $url
	 * @param      string $append
	 * @return     string HTML
	 */
	public static function showPubTitleProvisioned( $pub, $url, $append = '' )
	{
	?>
		<h3 class="prov-header">
			<a href="<?php echo $url; ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; "<?php echo \Hubzero\Utility\String::truncate($pub->title, 65); ?>"
			<?php if ($append) { echo $append; } ?>
		</h3>
	<?php
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
	public static function primaryButton($class, $href, $msg,
		$xtra = '', $title = '', $action = '', $disabled = false, $pop = '')
	{
		$view = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'site',
			'name'   => 'view',
			'layout' => '_primary'
		));
		$view->option   = 'com_publications';
		$view->disabled = $disabled;
		$view->class    = $class;
		$view->href     = $href;
		$view->title    = $title;
		$view->action   = $action;
		$view->xtra     = $xtra;
		$view->pop      = $pop;
		$view->msg      = $msg;

		return $view->loadTemplate();
	}

	/**
	 * Include status bar - publication steps/sections/version navigation
	 *
	 * @return     array
	 */
	public static function drawStatusBar($item, $step = NULL, $showSubSteps = false, $review = 0)
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'edit',
				'layout'	=>'statusbar'
			)
		);
		$view->row 			 = $item->row;
		$view->version 		 = $item->version;
		$view->panels 		 = $item->panels;
		$view->active 		 = isset($item->active) ? $item->active : NULL;
		$view->move 		 = isset($item->move) ? $item->move : 0;
		$view->step 		 = $step;
		$view->lastpane 	 = $item->lastpane;
		$view->option 		 = $item->option;
		$view->project 		 = $item->project;
		$view->current_idx 	 = $item->current_idx;
		$view->last_idx 	 = $item->last_idx;
		$view->checked 		 = $item->checked;
		$view->url 			 = $item->url;
		$view->review		 = $review;
		$view->show_substeps = $showSubSteps;
		$view->display();
	}

	/**
	 * Get publication property
	 *
	 * @param      object $row
	 * @param      string $get
	 * @param      boolean $version
	 * @return     string HTML
	 */
	public static function getPubStateProperty($row, $get = 'class', $version = 1)
	{
		$dateFormat = 'M d, Y';

		$status = '';
		$date   = '';
		$class  = '';

		$now = Date::toSql();

		switch ($row->state)
		{
			case 0:
				$class  = 'unpublished';
				$status = Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED');
				$date = strtolower(Lang::txt('COM_PUBLICATIONS_UNPUBLISHED'))
					.' ' . \JHTML::_('date', $row->published_down, $dateFormat);
				break;

			case 1:
				$class  = 'published';
				$status.= Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED');
				$date   = $row->published_up > $now ? Lang::txt('to be') . ' ' : '';
				$date  .= strtolower(Lang::txt('COM_PUBLICATIONS_RELEASED'))
					.' ' . \JHTML::_('date', $row->published_up, $dateFormat);
				break;

			case 3:
			default:
				$class = 'draft';
				$status = Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT');
				$date = strtolower(Lang::txt('COM_PUBLICATIONS_STARTED'))
					.' ' . \JHTML::_('date', $row->created, $dateFormat);
				break;

			case 4:
				$class   = 'ready';
				if ($row->accepted != '0000-00-00 00:00:00' )
				{
					$status .= Lang::txt('COM_PUBLICATIONS_VERSION_REVERTED');
					$date = strtolower(Lang::txt('COM_PUBLICATIONS_ACCEPTED'))
						.' ' . \JHTML::_('date', $row->accepted, $dateFormat);
				}
				else
				{
					$status .= Lang::txt('COM_PUBLICATIONS_VERSION_READY');
					$date = strtolower(Lang::txt('COM_PUBLICATIONS_RELEASED'))
						.' ' . \JHTML::_('date', $row->published_up, $dateFormat);
				}

				break;

			case 5:
				$class  = 'pending';
				$status = Lang::txt('COM_PUBLICATIONS_VERSION_PENDING');
				$date  .= strtolower(Lang::txt('COM_PUBLICATIONS_SUBMITTED'))
					.' ' . \JHTML::_('date', $row->submitted, $dateFormat);
				break;

			case 7:
				$class  = 'wip';
				$status = Lang::txt('COM_PUBLICATIONS_VERSION_WIP');
				$date  .= strtolower(Lang::txt('COM_PUBLICATIONS_SUBMITTED'))
					.' ' . \JHTML::_('date', $row->submitted, $dateFormat);
				break;
		}

		switch ($get)
		{
			case 'class':
				return $class;
				break;

			case 'status':
				return $status;
				break;

			case 'date':
				return $date;
				break;
		}
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
	 * Encode some basic characters
	 *
	 * @param      string  $str    Text to convert
	 * @param      integer $quotes Include quotes?
	 * @return     string
	 */
	public static function encode_html($str, $quotes = 1)
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

			$path = PATH_APP . $path;

			jimport('joomla.filesystem.file');
			$type = strtoupper(\JFile::getExt($path));

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
					$fs = ($fsize) ? $fs : \Hubzero\Utility\Number::formatBytes($fs);
				break;
			}

			$html .= ($fs) ? ', ' . $fs : '';
		}
		$html .= ')</span>';

		return $html;
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
		$text = preg_replace('/<a\s+.*href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i', '\2', $text);
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
	 * Get the extension of a file
	 *
	 * @param      string $url File path/name
	 * @return     string
	 */
	public static function getFileExtension($url)
	{
		jimport('joomla.filesystem.file');
		return \JFile::getExt($url);
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
	 * Remove paragraph tags and break tags
	 *
	 * @param      string $pee Text to unparagraph
	 * @return     string
	 */
	public static function _txtUnpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}

	/**
	 * Create thumb name
	 *
	 * @param      string $image
	 * @param      string $tn
	 * @param      string $ext
	 *
	 * @return     string
	 */
	public static function createThumbName( $image=null, $tn='_thumb', $ext = 'png' )
	{
		jimport('joomla.filesystem.file');
		return \JFile::stripExt($image) . $tn . '.' . $ext;
	}

	/**
	 * Get disk space
	 *
	 * @param      array  	$rows		Publications objet array
	 *
	 * @return     integer
	 */
	public static function getDiskUsage( $rows = array() )
	{
		$used = 0;

		if (!empty($rows))
		{
			$pubconfig = Component::params( 'com_publications' );
			$base = trim($pubconfig->get('webpath'), DS);

			foreach ($rows as $row)
			{
				$path = DS . $base . DS . \Hubzero\Utility\String::pad( $row->id );
				$used = $used + self::computeDiskUsage($path, PATH_APP, false);
			}
		}

		return $used;
	}

	/**
	 * Get used disk space in path
	 *
	 * @param      string 	$path
	 * @param      string 	$prefix
	 * @param      boolean 	$git
	 *
	 * @return     integer
	 */
	public static function computeDiskUsage($path = '', $prefix = '', $git = true)
	{
		$used = 0;
		if ($path && is_dir($prefix . $path))
		{
			chdir($prefix . $path);

			$where = $git == true ? ' .[!.]*' : '';
			exec('du -sk ' . $where, $out);

			if ($out && isset($out[0]))
			{
				$dir = $git == true ? '.git' : '.';
				$kb = str_replace($dir, '', trim($out[0]));
				$used = $kb * 1024;
			}
		}

		return $used;
	}

	/**
	 * Send email
	 *
	 * @param      array 	$config
	 * @param      object 	$publication
	 * @param      array 	$addressees
	 * @param      string 	$subject
	 * @param      string 	$message
	 * @return     void
	 */
	public static function notify( $config, $publication, $addressees = array(),
		$subject = NULL, $message = NULL, $hubMessage = false)
	{
		if (!$subject || !$message || empty($addressees))
		{
			return false;
		}

		// Is messaging turned on?
		if ($config->get('email') != 1)
		{
			return false;
		}

		// Set up email config
		$from = array();
		$from['name']  = Config::get('config.sitename') . ' ' . Lang::txt('COM_PUBLICATIONS');
		$from['email'] = Config::get('config.mailfrom');

		// Html email
		$from['multipart'] = md5(date('U'));

		// Get message body
		$eview = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'site',
			'name'   => 'emails',
			'layout' => '_plain'
		));

		$eview->publication 	= $publication;
		$eview->message			= $message;
		$eview->subject			= $subject;

		$body = array();
		$body['plaintext'] 	= $eview->loadTemplate();
		$body['plaintext'] 	= str_replace("\n", "\r\n", $body['plaintext']);

		// HTML email
		$eview->setLayout('_html');
		$body['multipart'] = $eview->loadTemplate();
		$body['multipart'] = str_replace("\n", "\r\n", $body['multipart']);

		$body_plain = is_array($body) && isset($body['plaintext']) ? $body['plaintext'] : $body;
		$body_html  = is_array($body) && isset($body['multipart']) ? $body['multipart'] : NULL;

		// Send HUB message
		if ($hubMessage)
		{
			Event::trigger( 'xmessage.onSendMessage',
				array(
					'publication_status_changed',
					$subject,
					$body,
					$from,
					$addressees,
					'com_publications'
				)
			);
		}
		else
		{
			// Send email
			foreach ($addressees as $userid)
			{
				$user = \Hubzero\User\Profile::getInstance($userid);
				if ($user === false)
				{
					continue;
				}

				$mail = new \Hubzero\Mail\Message();
				$mail->setSubject($subject)
					->addTo($user->get('email'), $user->get('name'))
					->addFrom($from['email'], $from['name'])
					->setPriority('normal');

				$mail->addPart($body_plain, 'text/plain');

				if ($body_html)
				{
					$mail->addPart($body_html, 'text/html');
				}

				$mail->send();
			}
		}
	}

	/**
	 * Short description for 'alert'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $msg Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1); </script>\n";
	}
}