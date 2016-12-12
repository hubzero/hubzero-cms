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

namespace Components\Resources\Helpers;

use Document;
use Pathway;
use Lang;
use User;
use Date;

include_once(__DIR__ . DS . 'tags.php');

/**
 * Resources helper class for misc. HTML and display
 */
class Html
{
	/**
	 * Generate a select form
	 *
	 * @param   string  $content     Content to filter
	 * @param   array   $disallowed  List of styles to remove
	 * @return  string  HTML
	 */
	public static function stripStyles($content, $disallowed = array())
	{
		if (empty($disallowed))
		{
			$disallowed = array(
				// Font specific
				'font-family',
				'font-variant',
				'font-size',
				'font-size-adjust',
				'font',
				'color',
				'line-height',
				'letter-spacing',
				'text-decoration',
				'text-decoration-color',
				'text-decoration-line',
				'text-decoration-style',
				'text-indent',
				'text-shadow',
				'text-transform',
				'word-break',
				'word-spacing',
				'word-wrap',
				// Other styles
				'animation',
				'animation-delay',
				'animation-direction',
				'animation-duration',
				'animation-fill-mode',
				'animation-iteration-count',
				'animation-name',
				'animation-play-state',
				'animation-timing-function',
				'background',
				'background-attachment',
				'background-clip',
				'background-color',
				'background-image',
				'background-origin',
				'background-position',
				'background-repeat',
				'background-size',
				'box-shadow',
				'cursor',
			);
		}

		$content = preg_replace_callback(
			'/<[^>]+style[\x00-\x20]*=[\`\'\"]*([^\`\'\"]+)[\`\'\"]*[^>]*>/i',
			function ($match) use ($content, $disallowed)
			{
				$properties = array();

				$declarations = explode(';', $match[1]);
				$declarations = array_map('trim', $declarations);

				foreach ($declarations as $declaration)
				{
					@list($property, $value) = explode(':', $declaration);
					$property = strtolower(trim($property));

					if (!$property) continue;

					if (!in_array($property, $disallowed))
					{
						$properties[] = $property . ': ' . trim($value);
					}
				}

				$replacement = trim(implode($properties, '; '));

				return str_replace($match[1], $replacement, $match[0]);
			},
			$content
		);

		return $content;
	}

	/**
	 * Short description for 'writeRating'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $rating Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function writeRating($rating)
	{
		switch ($rating)
		{
			case 0.5: $class = ' half';      break;
			case 1:   $class = ' one';       break;
			case 1.5: $class = ' onehalf';   break;
			case 2:   $class = ' two';       break;
			case 2.5: $class = ' twohalf';   break;
			case 3:   $class = ' three';     break;
			case 3.5: $class = ' threehalf'; break;
			case 4:   $class = ' four';      break;
			case 4.5: $class = ' fourhalf';  break;
			case 5:   $class = ' five';      break;
			case 0:
			default:  $class = ' none';      break;
		}

		return '<p class="avgrating'.$class.'"><span>Rating: '.$rating.' out of 5 stars</span></p>';
	}

	/**
	 * Generate a select access list
	 *
	 * @param      array  $as    Access levels
	 * @param      string $value Value to select
	 * @return     string HTML
	 */
	public static function selectAccess($as, $value, $name = 'access')
	{
		$as = explode(',',$as);
		$html  = '<select name="' . $name . '" id="field-' . str_replace(array('[',']'), '', $name) . '">' . "\n";
		for ($i=0, $n=count($as); $i < $n; $i++)
		{
			$html .= "\t" . '<option value="' . $i . '"';
			if ($value == $i)
			{
				$html .= ' selected="selected"';
			}
			$html .= '>' . trim($as[$i]) . '</option>' . "\n";
		}
		$html .= '</select>' . "\n";
		return $html;
	}

	/**
	 * Generate a select list for groups
	 *
	 * @param      array  $groups Groups to populate list
	 * @param      string $value  Value to select
	 * @return     string HTML
	 */
	public static function selectGroup($groups, $value, $name = 'group_owner', $class = '')
	{
		$html  = '<select class="'.$class.'" name="'.$name.'" id="field-' . str_replace(array('[',']'), '', $name) . '"';
		if (!$groups)
		{
			$html .= ' disabled="disabled"';
		}
		$html .= '>' . "\n";
		$html .= ' <option value="">' . Lang::txt('Select group ...') . '</option>' . "\n";
		if ($groups)
		{
			foreach ($groups as $group)
			{
				$html .= ' <option value="' . $group->cn . '"';
				if ($value == $group->cn)
				{
					$html .= ' selected="selected"';
				}
				$html .= '>' . stripslashes($group->description) . '</option>' . "\n";
			}
		}
		$html .= '</select>' . "\n";
		return $html;
	}

	/**
	 * Generate a section select list
	 *
	 * @param      string  $name  Name of the field
	 * @param      array   $array Values to populate list
	 * @param      integer $value Value to select
	 * @param      string  $class Class name of field
	 * @param      string  $id    ID of field
	 * @return     string HTML
	 */
	public static function selectSection($name, $array, $value, $class='', $id)
	{
		$html  = '<select name="' . $name . '" id="' . $name . '" onchange="return listItemTask(\'cb' . $id . '\',\'regroup\')"';
		$html .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		$html .= ' <option value="0"';
		$html .= ($id == $value || $value == 0) ? ' selected="selected"' : '';
		$html .= '>' . Lang::txt('[ none ]') . '</option>' . "\n";
		foreach ($array as $anode)
		{
			$selected = ($anode->id == $value || $anode->type == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="' . $anode->id . '"' . $selected . '>' . $anode->type . '</option>' . "\n";
		}
		$html .= '</select>' . "\n";
		return $html;
	}

	/**
	 * Generate a type select list
	 *
	 * @param      array  $arr      Values to populate list
	 * @param      string $name     Name of the field
	 * @param      mixed  $value    Value to select
	 * @param      string $shownone Show a 'none' option?
	 * @param      string $class    Class name of field
	 * @param      string $js       Scripts to add to field
	 * @param      string $skip     ITems to skip
	 * @return     string HTML
	 */
	public static function selectType($arr, $name, $value='', $shownone='', $class='', $js='', $skip='')
	{
		$html  = '<select name="' . $name . '" id="' . $name . '"' . $js;
		$html .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		if ($shownone != '')
		{
			$html .= "\t" . '<option value=""';
			$html .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
			$html .= '>' . $shownone . '</option>' . "\n";
		}
		if ($skip)
		{
			$skips = explode(',', $skip);
		}
		else
		{
			$skips = array();
		}
		foreach ($arr as $anode)
		{
			if (!in_array($anode->id, $skips))
			{
				$selected = ($value && ($anode->id == $value || $anode->type == $value))
					  ? ' selected="selected"'
					  : '';
				$html .= "\t" . '<option value="' . $anode->id . '"' . $selected . '>' . stripslashes($anode->type) . '</option>' . "\n";
			}
		}
		$html .= '</select>' . "\n";
		return $html;
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
	 * Convert a date to a path
	 *
	 * @param      string $date Date to convert (0000-00-00 00:00:00)
	 * @return     string
	 */
	public static function dateToPath($date)
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		return $dir_year . DS . $dir_month;
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
			$dir_year  = Date::of($date)->format('Y');
			$dir_month = Date::of($date)->format('m');

			if (!is_dir($base . DS . $dir_year . DS . $dir_month . DS . $dir_id) && intval($dir_year) <= 2013 && intval($dir_month) <= 11)
			{
				$dir_year  = Date::of($date)->toLocal('Y');
				$dir_month = Date::of($date)->toLocal('m');
			}
		}
		else
		{
			$dir_year  = Date::format('Y');
			$dir_month = Date::format('m');
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

				$params = new \Hubzero\Config\Registry($child->params);

				$ftype 	  = self::getFileExtension($child->path);
				//$class    = $params->get('class', $ftype);
				$doctitle = $params->get('title', $title);

				// Things we want to highlight
				$toShow = array('User Guide', 'Syllabus', 'iTunes', 'iTunes U', 'Audio', 'Video', 'Slides', 'YouTube', 'Vimeo');

				$url = self::processPath($option, $child, $publication->id);
				$extra = '';

				foreach ($toShow as $item)
				{
					if (strtolower($doctitle) !=  preg_replace('/' . strtolower($item) . '/', '', strtolower($doctitle))
					 && !in_array($item, $shown))
					{
						$class = str_replace(' ', '', strtolower($item));
						$childParams  = new \Hubzero\Config\Registry($child->params);
						$childAttribs = new \Hubzero\Config\Registry($child->attribs);
						$linkAction = $childParams->get('link_action', 0);
						$width      = $childAttribs->get('width', 640);
						$height     = $childAttribs->get('height', 360);

						$url = str_replace( '&amp;', '&', $url);
						$url = str_replace( '&', '&amp;', $url);

						if ($linkAction == 1)
						{
							$supli[] = ' <li><a class="'.$class.'" rel="external" href="'.$url.'" title="'.$child->title.'"' . $extra . '>'.$item.'</a></li>'."\n";
						}
						elseif ($linkAction == 2)
						{
							$class .= ' play';
							$class .= ' ' . $width . 'x' . $height;
							$supli[] = ' <li><a class="'.$class.'" href="'.$url.'" title="'.$child->title.'"' . $extra . '>'.$item.'</a></li>'."\n";
						}
						else
						{
							$supli[] = ' <li><a class="'.$class.'" href="'.$url.'" title="'.$child->title.'"' . $extra . '>'.$item.'</a></li>'."\n";
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
			$supln .= ' <li class="otherdocs"><a href="' . Route::url('index.php?option=' . $option
				. '&id=' . $publication->id . '&active=supportingdocs')
				.'" title="' . Lang::txt('COM_RESOURCES_VIEW_ALL') . ' ' . $docs.' ' . Lang::txt('COM_RESOURCES_SUPPORTING_DOCUMENTS').' ">'
				. $otherdocs . ' ' . Lang::txt('COM_RESOURCES_MORE') . '</a></li>' . "\n";
		}

		if (!$sdocs && $docs > 0)
		{
			$html .= "\t\t" . '<p class="viewalldocs"><a href="' . Route::url('index.php?option='
				. $option . '&id=' . $publication->id . '&active=supportingdocs') . '">'
				. Lang::txt('COM_RESOURCES_ADDITIONAL_MATERIALS_AVAILABLE') . ' (' . $docs .')</a></p>'."\n";
		}

		$supln .= '</ul>'."\n";
		$html .= $sdocs ? $supln : '';
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
		return \Filesystem::name($pic) . '-tn.gif';
	}

	/**
	 * Display a link to the license associated with this resource
	 *
	 * @param      array $license License name
	 * @return     string HTML
	 */
	public static function license($license)
	{
		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'license.php');

		$license = str_replace(' ', '-', strtolower($license));
		$license = preg_replace("/[^a-zA-Z0-9\-_]/", '', $license);

		$database = \App::get('db');
		$rl = new \Components\Resources\Tables\License($database);
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
				$html = '<p class="' . $rl->name . ' license">Licensed according to <a rel="license" class="popup" href="' . Route::url('index.php?option=com_resources&task=license&resource=' . substr($rl->name, 6) . '&no_html=1') . '">this deed</a>.</p>';
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
					$url = Route::url('index.php?option=' . $option . '&alias=' . $alias . '&active=' . $name);
				}
				else
				{
					$url = Route::url('index.php?option=' . $option . '&id=' . $id . '&active=' . $name);
				}
				if (strtolower($name) == $active)
				{
					Pathway::append($cat[$name], $url);

					if ($active != 'about')
					{
						Document::setTitle(Document::getTitle() . ': ' . $cat[$name]);
					}
				}
				$html .= "\t\t" . '<li id="sm-' . $i . '"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" data-rel="' . $name . '" href="' . $url . '"><span>' . $cat[$name] . '</span></a></li>' . "\n";
				$i++;
			}
		}
		$html .= "\t".'</ul>' . "\n";

		return $html;
	}

	/**
	 * Generate COins microformat
	 *
	 * @param      object $cite     Resource citation data
	 * @param      object $resource Resource
	 * @param      object $config   Component config
	 * @param      object $helper   Helper
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
		$tconfig = Component::params('com_tools');
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
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

		$html  = '<p>' . Lang::txt('COM_RESOURCES_CITATION_INSTRUCTIONS') . '</p>' . "\n";
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
			$html .= \Components\Citations\Helpers\Format::formatReference($cite);
			if (is_numeric($rev) || (is_string($rev) && $rev != 'dev'))
			{
				$html .= "\t\t" . '<p class="details">' . "\n";
				$html .= "\t\t\t" . '<a href="index.php?option=' . $option . '&task=citation&id=' . $id . '&citationFormat=bibtex&no_html=1&rev=' . $rev . '" title="' . Lang::txt('COM_RESOURCES_DOWNLOAD_BIBTEX_FORMAT') . '">BibTex</a> <span>|</span> ' . "\n";
				$html .= "\t\t\t" . '<a href="index.php?option=' . $option . '&task=citation&id=' . $id . '&citationFormat=endnote&no_html=1&rev=' . $rev . '" title="' . Lang::txt('COM_RESOURCES_DOWNLOAD_ENDNOTE_FORMAT') . '">EndNote</a>' . "\n";
				$html .= "\t\t" . '</p>' . "\n";
			}
			$html .= "\t" . '</li>' . "\n";
			$html .= '</ul>' . "\n";

		}

		/*if ($type == 7)
		{
			$html .= '<p>'.Lang::txt('In addition, we would appreciate it if you would add the following acknowledgment to your publication:').'</p>' . "\n";
			$html .= '<ul class="citations results">' . "\n";
			$html .= "\t".'<li>' . "\n";
			$html .= "\t\t".'<p>'.Lang::txt('Simulation services for results presented here were provided by the Network for Computational Nanotechnology (NCN) at nanoHUB.org').'</p>' . "\n";
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
	 * Get the extension of a file
	 *
	 * @param      string $url File path/name
	 * @return     string
	 */
	public static function getFileExtension($url)
	{
		return \Filesystem::extension($url);
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
		$database = \App::get('db');

		//$rt = new \Components\Resources\Tables\Type($database);
		//$rt->load($item->type);
		$rt = \Components\Resources\Tables\Type::getRecordInstance($item->type);
		$type = $rt->alias;

		if ($item->standalone == 1)
		{
			$url = Route::url('index.php?option=' . $option . '&id=' .  $item->id);
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
						$url = Route::url('index.php?option=' . $option . '&id=' .  $item->id);
					}
				break;

				case 'video':
					$url = Route::url('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=video');
				break;

				case 'hubpresenter':
					$url = Route::url('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=watch');
				break;

				case 'breeze':
					$url = Route::url('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=play');
				break;

				default:
					if ($action == 2)
					{
						$url = Route::url('index.php?option=' . $option . '&id=' . $pid . '&resid=' . $item->id . '&task=play');
					}
					else
					{
						if (strstr($item->path, 'http') || substr($item->path, 0, 3) == 'mms')
						{
							$url = $item->path;
						}
						else
						{
							$url = Route::url('index.php?option=' . $option . '&id=' . $item->id . '&task=download&file=' . basename($item->path));
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
	 * @param      object $resource   Resource
	 * @param      object $firstChild First resource child
	 * @param      string $xact       Extra parameters to add
	 * @return     string
	 */
	public static function primary_child($option, $resource, $firstChild, $xact='')
	{
		$database = \App::get('db');

		$html = '';

		switch ($resource->type)
		{
			case 7:
				$authorized = User::authorise('core.manage', 'com_tools.' . $resource->id);

				$mconfig = Component::params('com_tools');

				// Ensure we have a connection to the middleware
				if (!$mconfig->get('mw_on')
				 || ($mconfig->get('mw_on') > 1 && !$authorized))
				{
					$pop   = '<p class="warning">' . Lang::txt('COM_RESOURCES_TOOL_SESSION_INVOKE_DISABLED') . '</p>';
					$html .= self::primaryButton('link_disabled', '', Lang::txt('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 1, $pop);
					return $html;
				}

				//get tool params
				$params = Component::params('com_tools');
				$launchOnIpad = $params->get('launch_ipad', 0);

				// Generate the URL that launches a tool session
				$lurl ='';
				$database = \App::get('db');
				$tables = $database->getTableList();
				$table = $database->getPrefix() . 'tool_version';

				if (in_array($table,$tables))
				{
					if (isset($resource->revision) && $resource->toolpublished)
					{

						$sess = $resource->tool ? $resource->tool : $resource->alias . '_r' . $resource->revision;
						$v = (!isset($resource->revision) or $resource->revision=='dev') ? 'test' : $resource->revision;

						$lurl = 'index.php?option=com_tools&app=' . $resource->alias . '&task=invoke&version=' . $v;
					}
					elseif (!isset($resource->revision) or $resource->revision=='dev')
					{
						// serve dev version
						$lurl = 'index.php?option=com_tools&app=' . $resource->alias . '&task=invoke&version=dev';
					}
				}
				else
				{
					$lurl = 'index.php?option=com_tools&task=invoke&app=' . $resource->alias;
				}

				require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'tool.php');

				// Create some tool objects
				$toolgroups = null;
				$hztv = \Components\Tools\Models\Version::getInstance($resource->tool);
				if ($hztv)
				{
					$ht = \Components\Tools\Models\Tool::getInstance($hztv->toolid);
					if ($ht)
					{ // @FIXME: this only seems to fail on hubbub VMs where workspace resource is incomplete/incorrect (bad data in DB?)
						$toolgroups = $ht->getToolGroupsRestriction($hztv->toolid, $resource->tool);
					}
				}

				// Get current users groups
				$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'members');
				$ingroup = false;
				$groups = array();
				if ($xgroups)
				{
					foreach ($xgroups as $xgroup)
					{
						$groups[] = $xgroup->cn;
					}
					// Check if they're in the admin tool group
					$admingroup = Component::params('com_tools')->get('admingroup');
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

				if (!User::isGuest() && !$ingroup && $toolgroups)
				{ // see if tool is restricted to a group and if current user is in that group
					$pop = '<p class="warning">' . Lang::txt('COM_RESOURCES_TOOL_IS_RESTRICTED') . '</p>';
					$html .= self::primaryButton('link_disabled', '', Lang::txt('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 1, $pop);
				}
				else if ((isset($resource->revision) && $resource->toolpublished) or !isset($resource->revision))
				{ // dev or published tool
					//if (User::isGuest()) {
						// Not logged-in = show message
						//$html .= self::primaryButton('launchtool disabled', $lurl, Lang::txt('COM_RESOURCES_LAUNCH_TOOL'));
						//$html .= self::warning('You must <a href="'.Route::url('index.php?option=com_users&view=login').'">log in</a> before you can run this tool.')."\n";
					//} else {
						$pop = (User::isGuest()) ? '<p class="warning">' . Lang::txt('COM_RESOURCES_TOOL_LOGIN_REQUIRED_TO_RUN') . '</p>' : '';
						$pop = ($resource->revision =='dev') ? '<p class="warning">' . Lang::txt('COM_RESOURCES_TOOL_VERSION_UNDER_DEVELOPMENT') . '</p>' : $pop;
						$html .= self::primaryButton('launchtool', $lurl, Lang::txt('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 0, $pop);
					//}
				}
				else
				{ // tool unpublished
					$pop   = '<p class="warning">' . Lang::txt('COM_RESOURCES_TOOL_VERSION_UNPUBLISHED') . '</p>';
					$html .= self::primaryButton('link_disabled', '', Lang::txt('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 1, $pop);
				}
			break;

			case 4:
				// write primary button and downloads for a Learning Module
				$html .= self::primaryButton('', Route::url('index.php?option=com_resources&id=' . $resource->id . '&task=play'), 'Start learning module');
			break;

			case 6:
			case 8:
			case 31:
			case 2:
				// do nothing
				$mesg  = Lang::txt('COM_RESOURCES_VIEW') . ' ';
				$mesg .= $resource->type == 6 ? 'Course Lectures' : '';
				$mesg .= $resource->type == 2 ? 'Workshop ' : '';
				$mesg .= $resource->type == 6 ? '' : 'Series';
				$html .= self::primaryButton('download', Route::url('index.php?option=com_resources&id=' . $resource->id) . '#series', $mesg, '', $mesg, '');
			break;

			default:
				$firstChild->title = str_replace('"', '&quot;', $firstChild->title);
				$firstChild->title = str_replace('&amp;', '&', $firstChild->title);
				$firstChild->title = str_replace('&', '&amp;', $firstChild->title);

				$mesg   = '';
				$class  = '';
				$action = '';
				$xtra   = '';

				//$lt = new \Components\Resources\Tables\Type($database);
				//$lt->load($firstChild->logicaltype);
				$lt = \Components\Resources\Tables\Type::getRecordInstance($firstChild->logicaltype);
				$ltparams = new \Hubzero\Config\Registry($lt->params);

				//$rt = new \Components\Resources\Tables\Type($database);
				//$rt->load($firstChild->type);
				$rt = \Components\Resources\Tables\Type::getRecordInstance($firstChild->type);
				$tparams = new \Hubzero\Config\Registry($rt->params);

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
						$mesg  = Lang::txt('COM_RESOURCES_DOWNLOAD');
						$class = 'download';
						//$action = 'rel="download"';
						$linkAction = 3;
					break;

					case 'lightbox':
						$mesg = Lang::txt('COM_RESOURCES_VIEW_RESOURCE');
						$class = 'play';
						//$action = 'rel="internal"';
						$linkAction = 2;
					break;

					case 'newwindow':
					case 'external':
						$mesg = Lang::txt('COM_RESOURCES_VIEW_RESOURCE');
						//$class = 'popup';
						$action = 'rel="external"';
						$linkAction = 1;
					break;

					case 'extension':
					default:
						$linkAction = 0;

						$mediatypes = array('elink','quicktime','presentation','presentation_audio','breeze','quiz','player','video_stream','video','hubpresenter');
						$downtypes = array('thesis','handout','manual','software_download');

						if (in_array($lt->alias, $downtypes))
						{
							$mesg  = Lang::txt('COM_RESOURCES_DOWNLOAD');
							$class = 'download';
						}
						elseif (in_array($rt->alias, $mediatypes))
						{
							$mesg  = Lang::txt('COM_RESOURCES_VIEW_PRESENTATION');
							$mediatypes = array('flash_paper','breeze','32','26');
							if (in_array($firstChild->type, $mediatypes))
							{
								$class = 'play';
							}
						}
						else
						{
							$mesg  = Lang::txt('COM_RESOURCES_DOWNLOAD');
							$class = 'download';
						}

						if ($firstChild->standalone == 1)
						{
							$mesg  = Lang::txt('COM_RESOURCES_VIEW_RESOURCE');
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
							$mesg  = Lang::txt('COM_RESOURCES_VIEW_LINK');
						}
					break;
				}

				// IF (not a simulator) THEN show the first child as the primary button
				if ($firstChild->access==1 && User::isGuest())
				{
					// first child is for registered users only and the visitor is not logged in
					$pop  = '<p class="warning">' . Lang::txt('COM_RESOURCES_LOGIN_REQUIRED_TO_DOWNLOAD') . '</p>' . "\n";
					$html .= self::primaryButton($class . ' disabled', Route::url('index.php?option=com_users&view=login'), $mesg, '', '', '', '', $pop);
				}
				else
				{
					// If the child is marked as protected
					if ($firstChild->access == 3)
					{
						// Get the groups the user has access to
						$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
						$usersgroups = array();
						if (!empty($xgroups))
						{
							foreach ($xgroups as $group)
							{
								if ($group->regconfirmed)
								{
									$usersgroups[] = $group->cn;
								}
							}
						}

						// Get the groups that can access this resource
						$allowedgroups = $resource->getGroups();

						// Find what groups the user has in common with the resource, if any
						$common = array_intersect($usersgroups, $allowedgroups);

						// Check if the user is apart of the group that owns the resource
						// or if they have any groups in common
						if (!in_array($resource->group_owner, $usersgroups) || count($common) <= 0)
						{
							$html .= '<p class="warning">';
							if (User::isGuest())
							{
								$html .= Lang::txt('COM_RESOURCES_ERROR_MUST_BE_LOGGED_IN', base64_encode(\Request::path()));
							}
							else
							{
								$ghtml = array();
								foreach ($allowedgroups as $allowedgroup)
								{
									$ghtml[] = '<a href="' . Route::url('index.php?option=com_groups&cn=' . $allowedgroup) . '">' . $allowedgroup . '</a>';
								}
								$html .= Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP') . ' ' . implode(', ', $ghtml);
							}
							$html .= '</p>';

							return $html;
						}
					}

					$childParams = new \Hubzero\Config\Registry($firstChild->params);
					$linkAction = intval($childParams->get('link_action', $linkAction));

					$url = self::processPath($option, $firstChild, $resource->id, $linkAction);

					switch ($linkAction)
					{
						case 3:
							$mesg  = Lang::txt('COM_RESOURCES_DOWNLOAD');
							$class = 'download';
						break;

						case 2:
							$mesg  = Lang::txt('COM_RESOURCES_VIEW_RESOURCE');
							$class = 'play';
						break;

						case 1:
							$mesg = Lang::txt('COM_RESOURCES_VIEW_RESOURCE');
							//$class = 'popup';
							$action = 'rel="external"';
						break;

						case 0:
						default:
							// Do nothing
						break;
					}

					$attribs = new \Hubzero\Config\Registry($firstChild->attribs);
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
					//$rt = new \Components\Resources\Tables\Type($database);
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
						$class = 'video';
					}

					$pt = \Components\Resources\Tables\Type::getRecordInstance($resource->type);
					if ($pt->alias == 'databases')
					{
						$mesg = "View Data";
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
		$view = new \Hubzero\Component\View(array(
			'base_path' => Component::path('com_resources') . DS . 'site',
			'name'   => 'view',
			'layout' => '_primary'
		));
		$view->option   = 'com_resources';
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
		// Check if the path has the extension (e.g. Databases don't)
		elseif (strrpos($path, '.') === false)
		{
			// no caption
			return '';
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

			$type = strtoupper(\Filesystem::extension($path));

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
}
