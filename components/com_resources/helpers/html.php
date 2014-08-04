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

				$params = new JRegistry( $child->params );

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
						$childParams  = new JRegistry($child->params);
						$childAttribs = new JRegistry($child->attribs);
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
		$juser    = JFactory::getUser();
		$database = JFactory::getDBO();

		$html = '';

		switch ($resource->type)
		{
			case 7:
				$authorized = $juser->authorise('core.manage', 'com_tools.' . $resource->id);

				$juser = JFactory::getUser();

				$mconfig = JComponentHelper::getParams('com_tools');

				// Ensure we have a connection to the middleware
				if (!$mconfig->get('mw_on')
				 || ($mconfig->get('mw_on') > 1 && !$authorized))
				{
					$pop   = '<p class="warning">' . JText::_('COM_RESOURCES_TOOL_SESSION_INVOKE_DISABLED') . '</p>';
					$html .= self::primaryButton('link_disabled', '', JText::_('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 1, $pop);
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
						if ($isiPad && $launchOnIpad)
						{
							$lurl = 'nanohub://tools/invoke/' . $resource->alias . '/' . $v;
						}
						else
						{
							$lurl = 'index.php?option=com_tools&app=' . $resource->alias . '&task=invoke&version=' . $v;
						}

					}
					elseif (!isset($resource->revision) or $resource->revision=='dev')
					{
						// serve dev version
						if ($isiPad && $launchOnIpad)
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
					if ($isiPad && $launchOnIpad)
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
					$pop = '<p class="warning">' . JText::_('COM_RESOURCES_TOOL_IS_RESTRICTED') . '</p>';
					$html .= self::primaryButton('link_disabled', '', JText::_('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 1, $pop);
				}
				else if ((isset($resource->revision) && $resource->toolpublished) or !isset($resource->revision))
				{ // dev or published tool
					//if ($juser->get('guest')) {
						// Not logged-in = show message
						//$html .= self::primaryButton('launchtool disabled', $lurl, JText::_('COM_RESOURCES_LAUNCH_TOOL'));
						//$html .= self::warning('You must <a href="'.JRoute::_('index.php?option=com_users&view=login').'">log in</a> before you can run this tool.')."\n";
					//} else {
						$pop = ($juser->get('guest')) ? '<p class="warning">' . JText::_('COM_RESOURCES_TOOL_LOGIN_REQUIRED_TO_RUN') . '</p>' : '';
						$pop = ($resource->revision =='dev') ? '<p class="warning">' . JText::_('COM_RESOURCES_TOOL_VERSION_UNDER_DEVELOPMENT') . '</p>' : $pop;
						$html .= self::primaryButton('launchtool', $lurl, JText::_('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 0, $pop);
					//}
				}
				else
				{ // tool unpublished
					$pop   = '<p class="warning">' . JText::_('COM_RESOURCES_TOOL_VERSION_UNPUBLISHED') . '</p>';
					$html .= self::primaryButton('link_disabled', '', JText::_('COM_RESOURCES_LAUNCH_TOOL'), '', '', '', 1, $pop);
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
				$mesg  = JText::_('COM_RESOURCES_VIEW') . ' ';
				$mesg .= $resource->type == 6 ? 'Course Lectures' : '';
				$mesg .= $resource->type == 2 ? 'Workshop ' : '';
				$mesg .= $resource->type == 6 ? '' : 'Series';
				$html .= self::primaryButton('download', JRoute::_('index.php?option=com_resources&id=' . $resource->id) . '#series', $mesg, '', $mesg, '');
			break;

			default:
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
				$ltparams = new JRegistry($lt->params);

				//$rt = new ResourcesType($database);
				//$rt->load($firstChild->type);
				$rt = ResourcesType::getRecordInstance($firstChild->type);
				$tparams = new JRegistry($rt->params);

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
						$mesg  = JText::_('COM_RESOURCES_DOWNLOAD');
						$class = 'download';
						//$action = 'rel="download"';
						$linkAction = 3;
					break;

					case 'lightbox':
						$mesg = JText::_('COM_RESOURCES_VIEW_RESOURCE');
						$class = 'play';
						//$action = 'rel="internal"';
						$linkAction = 2;
					break;

					case 'newwindow':
						$mesg = JText::_('COM_RESOURCES_VIEW_RESOURCE');
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
							$mesg  = JText::_('COM_RESOURCES_DOWNLOAD');
							$class = 'download';
						}
						elseif (in_array($rt->alias, $mediatypes))
						{
							$mesg  = JText::_('COM_RESOURCES_VIEW_PRESENTATION');
							$mediatypes = array('flash_paper','breeze','32','26');
							if (in_array($firstChild->type, $mediatypes))
							{
								$class = 'play';
							}
						}
						else
						{
							$mesg  = JText::_('COM_RESOURCES_DOWNLOAD');
							$class = 'download';
						}

						if ($firstChild->standalone == 1)
						{
							$mesg  = JText::_('COM_RESOURCES_VIEW_RESOURCE');
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
							$mesg  = JText::_('COM_RESOURCES_VIEW_LINK');
						}
					break;
				}

				// IF (not a simulator) THEN show the first child as the primary button
				if ($firstChild->access==1 && $juser->get('guest'))
				{
					// first child is for registered users only and the visitor is not logged in
					$pop  = '<p class="warning">' . JText::_('COM_RESOURCES_LOGIN_REQUIRED_TO_DOWNLOAD') . '</p>' . "\n";
					$html .= self::primaryButton($class . ' disabled', JRoute::_('index.php?option=com_users&view=login'), $mesg, '', '', '', '', $pop);
				}
				else
				{
					$childParams = new JRegistry($firstChild->params);
					$linkAction = intval($childParams->get('link_action', $linkAction));

					$url = self::processPath($option, $firstChild, $resource->id, $linkAction);

					switch ($linkAction)
					{
						case 3:
							$mesg  = JText::_('COM_RESOURCES_DOWNLOAD');
							$class = 'download';
						break;

						case 2:
							$mesg  = JText::_('COM_RESOURCES_VIEW_RESOURCE');
							$class = 'play';
						break;

						case 1:
							$mesg = JText::_('COM_RESOURCES_VIEW_RESOURCE');
							//$class = 'popup';
							$action = 'rel="external"';
						break;

						case 0:
						default:
							// Do nothing
						break;
					}

					$attribs = new JRegistry($firstChild->attribs);
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
		//$path = '/www/myhub/components';
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
}
