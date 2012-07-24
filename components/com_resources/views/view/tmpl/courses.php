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
defined('_JEXEC') or die( 'Restricted access' );

	/* Non-Tool Resource page view  */

	$option 		= $this->option;
	$config 		= $this->config;
	$resource 		= $this->resource;
	$params 		= $this->params;
	$authorized 	= $this->authorized;
	$cats 			= $this->cats;
	$tab 			= $this->tab;
	$sections 		= $this->sections;
	$database 		= $this->database;
	$usersgroups 	= $this->usersgroups;
	$helper 		= $this->helper;
	$attribs 		= $this->attribs;
	$fsize 			= $this->fsize;
	$filters 		= $this->filters;
	$live_site = rtrim(JURI::base(),'/');
	
	$juser =& JFactory::getUser();

	$html  = '';
	$html .= '<div class="main section upperpane">'."\n";
	$html .= '<div class="aside rankarea">'."\n";

	// Show resource ratings
	$statshtml = '';
	if ($params->get('show_ranking')) {
		$helper->getCitations();
		$helper->getLastCitationDate();
		$stats = new AndmoreStats($database, $resource->id, $resource->type, $resource->rating, count($helper->citations), $helper->lastCitationDate);
		$statshtml = $stats->display();
	}

	if ($params->get('show_metadata')) {
		$supported = null;
		$database =& JFactory::getDBO();
		$rt = new ResourcesTags( $database );
		$supported = $rt->checkTagUsage( $config->get('supportedtag'), $resource->id );

		$xtra = '';
		if ($supported) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');
			$tag = new TagsTag( $database );
			$tag->loadTag($config->get('supportedtag'));

			$sl = $config->get('supportedlink');
			if ($sl) {
				$link = $sl;
			} else {
				$link = JRoute::_('index.php?option=com_tags'.'&tag='.$tag->tag);
			}

			$xtra = '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
		}
		$html .= ResourcesHtml::metadata($params, $resource->ranking, $statshtml, $resource->id, $sections, $xtra);
	}

	$html .= ' </div><!-- / .aside -->'."\n";
	$html .= '<div class="subject">'."\n";
	$html .= ' <div class="overviewcontainer">'."\n";
	$html .= ResourcesHtml::title( $option, $resource, $params, $authorized, $config, 0 );

	// Display authors
	if ($params->get('show_authors')) {
		$helper->getContributors(true, 1);
		if ($helper->contributors && $helper->contributors != '<br />') {
			$html .= ' <div id="authorslist">'."\n";
			$html .= $helper->contributors."\n";
			$html .= '</div>'."\n";
		}
	}

	// Display "at a glance"
	//$html .= '<p class="ataglance">';
	//$html .= $resource->introtext ? Hubzero_View_Helper_Html::shortenText(stripslashes($resource->introtext), 250, 0) : '';
	//$html .= ' <a href="">'.JText::_('Learn more').' &rsaquo;</a>'."\n";
	//$html .= JText::_('in') . ' <a href="' . JRoute::_('index.php?option=' . $option . '&type=' . $resource->_type->alias). '">' . $resource->_type->type . '</a>';
	//$html .= '</p>'."\n";
	$html .= ' </div><!-- / .overviewcontainer -->'."\n";

	$html .= ' <div class="aside launcharea">'."\n";
	$feeds = '';

	// Private/Public resource access check
	if ($resource->access == 3 && !in_array($resource->group_owner, $usersgroups) && !$authorized) {
		$ghtml = JText::_('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP').' ';
		$allowedgroups = $resource->getGroups();
		foreach ($allowedgroups as $allowedgroup)
		{
			$ghtml .= '<a href="'.JRoute::_('index.php?option=com_groups&gid='.$allowedgroup).'">'.$allowedgroup.'</a>, ';
		}
		$ghtml = substr($ghtml,0,strlen($ghtml) - 2);
		$html .= ResourcesHtml::warning( $ghtml )."\n";
	} else {
		// get launch button
		$helper->getFirstChild();

		switch ($resource->type)
		{
			case 4:
				// Write primary button and downloads for a Learning Module
				$html .= $tab != 'play' ? ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' ) : '';
			break;

			case 6:
			case 31:
			case 2:
				// Get a count of standalone children
				$ccount = $helper->getStandaloneCount( $filters );

				if ($ccount > 0) {
					$html .= ResourcesHtml::primary_child( $option, $resource, '', '' );
				}
				$feeds .= "\t\t".'<p>'."\n";
				$feeds .= "\t\t\t".'<a class="feed" id="resource-audio-feed" href="'. $live_site .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a><br />'."\n";
				$feeds .= "\t\t\t".'<a class="feed" id="resource-video-feed" href="'. $live_site .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a><br />'."\n";
				$feeds .= "\t\t\t".'<a class="feed" id="resource-slides-feed" href="'. $live_site .'/resources/'.$resource->id.'/feed.rss?format=slides">'.JText::_('Slides/Notes podcast').'</a>'."\n";
				$feeds .= "\t\t".'</p>'."\n";
			break;

			case 8:
				$feeds .= "\t\t".'<p><a class="feed" id="resource-audio-feed" href="'. $live_site .'/resources/'.$resource->id.'/feed.rss?format=audio">'.JText::_('Audio podcast').'</a><br />'."\n";
				$feeds .= "\t\t".'<a class="feed" id="resource-video-feed" href="'. $live_site .'/resources/'.$resource->id.'/feed.rss?format=video">'.JText::_('Video podcast').'</a></p>'."\n";
				// do nothing
			break;

			default:
				$html .= $tab != 'play' && is_object($helper->firstChild) ? ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' ) : '';
			break;
		}

		// Display some supporting documents
		$filterdocs = ($resource->type == 6 or $resource->type == 31 or $resource->type == 2) ? 'no' : 'all';
		$helper->getChildren( $resource->id, 0, $filterdocs );
		$children = $helper->children;

		$iTunes = 0;
		$supdocs = 0;
		$totaldocs = 0;
		$realdocs = 0;
		$fctype = is_object($helper->firstChild) ? ResourcesHtml::getFileExtension($helper->firstChild->path) : '';

		// Single out featured children resources
		if ($children != NULL) {
			$supln  = '<ul class="supdocln">'."\n";
			$supli  = array();

			foreach ($children as $child)
			{
				if ($child->access == 0 || ($child->access == 1 && !$juser->get('guest')) || ($resource->type == 4 && $child->access == 1)) {
					if (($resource->type == 4 && $child->access == 1) or $resource->type != 4) {
						$totaldocs++;
					}

					// exclude first child
					$realdocs = is_object($helper->firstChild) && $resource->type != 4 && $resource->type != 6 ? $totaldocs - 1 : $totaldocs ;

					$ftype = ResourcesHtml::getFileExtension($child->path);
					$url = ResourcesHtml::processPath($option, $child, $resource->id);

					$title = ($child->logicaltitle)
							? $child->logicaltitle
							: stripslashes($child->title);

					$child->title = str_replace( '"', '&quot;', $child->title );
					$child->title = str_replace( '&amp;', '&', $child->title );
					$child->title = str_replace( '&', '&amp;', $child->title );
					$child->title = str_replace( '&amp;quot;', '&quot;', $child->title );

					$linktitle = stripslashes($child->title) == $title ? $title : $title.' - '.stripslashes($child->title);

				  	if (strtolower($fctype) != strtolower($ftype) or $resource->type == 6) {
						// iTunes?
						if (strtolower(stripslashes($child->title)) !=  preg_replace('/itunes u/', '', strtolower(stripslashes($child->title)))) {
							$supli[] = ' <li><a class="itunes" href="'.$url.'" title="'.$linktitle.'">'.JText::_('iTunes U').'</a></li>'."\n";
						}

						// PDF slides?
						if (strtolower($ftype) == 'pdf' && $title == 'Presentation Slides') {
							$supli[] = ' <li><a class="pdf" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Slides').'</a></li>'."\n";
						}

						// Audio podcast?
						if (strtolower($ftype) == 'mp3' && strtolower(stripslashes($title)) !=  preg_replace('/audio/', '', strtolower(stripslashes($title)))) {
							$supli[] = ' <li><a class="mp3" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Audio').'</a></li>'."\n";
						}

						// Video podcast?
						if (strtolower($ftype) == 'mp4' && strtolower(stripslashes($title)) !=  preg_replace('/video/', '', strtolower(stripslashes($title)))) {
							$supli[] = ' <li><a class="mp4" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Video').'</a></li>'."\n";
						}

						// High Res video?
						if (strtolower($ftype) == 'mov' && strtolower(stripslashes($title)) !=  preg_replace('/video/', '', strtolower(stripslashes($title)))) {
							$supli[] = ' <li><a class="mov" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Video').'</a></li>'."\n";
						}

						// Syllabus?
						if (strtolower($ftype) == 'pdf' && strtolower(stripslashes($title)) !=  preg_replace('/syllabus/', '', strtolower(stripslashes($title)))) {
							$supli[] = ' <li><a class="pdf" href="'.$url.'" title="'.$linktitle.'">'.JText::_('Syllabus').'</a></li>'."\n";
						}
					}
				}
			}

			$supdocs = count( $supli ) > 2 ? 2 : count( $supli );
			$otherdocs = $realdocs - $supdocs;
			$otherdocs = ($supdocs + $otherdocs) == 3  ? 0 : $otherdocs;

			for ($i=0; $i < count( $supli ); $i++)
			{
				$supln .=  $i < 2 ? $supli[$i] : '';
				$supln .=  $i == 2 && !$otherdocs ? $supli[$i] : '';
			}

			// View more link?			
			if ($supdocs > 0 && $otherdocs > 0) {
				$supln .= ' <li class="otherdocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&active=supportingdocs').'" title="'.JText::_('View All').' '.$realdocs.' '.JText::_('Supporting Documents').' ">'.$otherdocs.' '.JText::_('more').' &rsaquo;</a></li>'."\n";
			} else if (!$supdocs && $realdocs > 0 && $tab != 'play' && is_object($helper->firstChild)) {
				$html .= "\t\t".'<p class="supdocs"><span class="viewalldocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&active=supportingdocs').'">'.JText::_('Additional materials available').' ('.$realdocs.')</a></span></p>'."\n";
			}

			$supln .= '</ul>'."\n";
			$supdocs = $supdocs && $tab != 'play'  ? $supln : 0;
		}

		// Show icons of other available formats
		if ($supdocs) {
			$html .= "\t\t\t".$supdocs."\n";
		}

		$html .= $feeds ? $feeds : '';
		$html .= $tab != 'play' ? ResourcesHtml::license( $params->get( 'license', '' ) ) : '';
	} // --- end else (if group check passed)

	$html .= ' </div><!-- / .aside launcharea -->'."\n";
	$html .= ' </div><!-- / .subject -->'."\n";

	if ($resource->access == 3 && (!in_array($resource->group_owner, $usersgroups) && !$authorized)) {
		// show nothing else
		$html .= '</div><!-- / .main section -->'."\n";
	} else {
		$html .= '<div class="clear sep"></div>'."\n";
		$html .= '</div><!-- / .main section -->'."\n";
		$html .= '<div class="main section noborder">'."\n";
		$html .= ' <div class="aside extracontent">'."\n";

		// Get Releated Resources plugin
		JPluginHelper::importPlugin( 'resources', 'related' );
		$dispatcher =& JDispatcher::getInstance();

		// Show related content
		$out = $dispatcher->trigger( 'onResourcesSub', array($resource, $option, 1) );
		if (count($out) > 0) {
			foreach ($out as $ou)
			{
				if (isset($ou['html'])) {
					$html .= $ou['html'];
				}
			}
		}

		// Show what's popular
		if ($tab == 'about') {
			ximport('Hubzero_Module_Helper');
			$html .= Hubzero_Module_Helper::renderModules('extracontent');
		}
		$html .= ' </div><!-- / .aside extracontent -->'."\n";

		$html .= ' <div class="subject tabbed">'."\n";
		$html .= ResourcesHtml::tabs( $option, $resource->id, $cats, $tab, $resource->alias );
		$html .= ResourcesHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
		$html .= '</div><!-- / .subject -->'."\n";
		$html .= '<div class="clear"></div>'."\n";

		// Show course listings under 'about' tab
		if ($tab == 'about') {
			// Course children
			$schildren = $helper->getStandaloneChildren( $filters );
			if ($schildren) {
				//$html .= ResourcesHtml::writeResultsTable( $database, $resource, $schildren, $option );
				$o = 'even';

				$html .= '<a name="series"></a>'."\n";
				$html .= '<table class="child-listing" summary="'.JText::_('A table of resources associated to this resource').'">'."\n";
				$html .= "\t".'<colgroup class="lecture_name"></colgroup>' . "\n";
				$html .= "\t".'<colgroup class="lecture_online"></colgroup>' . "\n";
				$html .= "\t".'<colgroup class="lecture_video"></colgroup>' . "\n";
				$html .= "\t".'<colgroup class="lecture_notes"></colgroup>' . "\n";
				$html .= "\t".'<colgroup class="lecture_supp"></colgroup>' . "\n";
				$html .= "\t".'<colgroup class="lecture_exercises"></colgroup>' . "\n";
				$html .= "\t".'<thead>'."\n";
				$html .= "\t\t".'<tr>'."\n";
				$html .= "\t\t\t".'<th>'.JText::_('Lecture Number/Topic').'</th>'."\n";
				$html .= "\t\t\t".'<th width="12%">'.JText::_('Online Lecture').'</th>'."\n";
				$html .= "\t\t\t".'<th>'.JText::_('Video').'</th>'."\n";
				$html .= "\t\t\t".'<th>'.JText::_('Lecture Notes').'</th>'."\n";
				$html .= "\t\t\t".'<th>'.JText::_('Supplemental Material').'</th>'."\n";
				$html .= "\t\t\t".'<th>'.JText::_('Suggested Exercises').'</th>'."\n";
				$html .= "\t\t".'</tr>'."\n";
				$html .= "\t".'</thead>'."\n";
				$html .= "\t".' <tbody>'."\n";
				
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}
				foreach ($schildren as $child)
				{
					// Retrieve the grandchildren
					$helper = new ResourcesHelper($child->id, $database);
					$helper->getChildren();

					$child_params = new $paramsClass( $child->params );
					$link_action = $child_params->get( 'link_action', '' );

					$child->title = ResourcesHtml::encode_html($child->title);

					$o = ($o == 'odd') ? 'even' : 'odd';

					$html .= "\t\t".'<tr class="'.$o.'">'."\n";
					$html .= "\t\t\t".'<td>';
					if ($child->standalone == 1) {
						$html .= '<a href="'.JRoute::_('index.php?option='.$option.'&id='.$child->id).'"';
						if ($link_action == 1) {
							$html .= ' target="_blank"';
						} elseif ($link_action == 2) {
							$html .= ' onclick="popupWindow(\''.$url.'\', \''.$child->title.'\', 400, 400, \'auto\');"';
						}
						$html .= '>'.$child->title.'</a>';
						if ($child->type != 31) {
							$html .= ($child->introtext) ? '<br />'.Hubzero_View_Helper_Html::shortenText(stripslashes($child->introtext),200,0) : '';
						}
					}
					$html .= '</td>'."\n";
					if ($helper->children && count($helper->children) > 0) {
						$videoi   		= '';
						$breeze    		= '';
						$hubpresenter 	= '';
						$pdf       		= '';
						$video     		= '';
						$exercises 		= '';
						$supp      		= '';
						$grandchildren 	= $helper->children;
						foreach ($grandchildren as $grandchild)
						{
							$grandchild->title = ResourcesHtml::encode_html($grandchild->title);
							$grandchild->path = ResourcesHtml::processPath($option, $grandchild, $child->id);

							$grandchild_rt = new ResourcesType( $database );
							$grandchild_rt->load($grandchild->type);
							$alias = $grandchild_rt->alias;

							switch ($alias)
							{
								case "player":
								case "quicktime":
									$videoi .= (!$videoi) ? '<a href="'.$grandchild->path.'">'.JText::_('View').'</a>' : '';
									break;
								case "breeze":
									$breeze .= (!$breeze) ? '<a title="View Presentation - Flash Version" class="breeze flash" href="'.$grandchild->path.'&amp;no_html=1" title="'.htmlentities(stripslashes($grandchild->title)).'">'.JText::_('View Flash').'</a>' : '';
									break;
								case "hubpresenter":
									$hubpresenter .= (!$hubpresenter) ? '<a title="View Presentation - HTML5 Version" class="hubpresenter html5" href="'.$grandchild->path.'" title="'.htmlentities(stripslashes($grandchild->title)).'">'.JText::_('View HTML').'</a>' : '';
									break;
								case "pdf":
								default:
									if ($grandchild->logicaltype == 14) {
										$pdf .= '<a href="'.$grandchild->path.'">'.JText::_('Notes').'</a>'."\n";
									} elseif ($grandchild->logicaltype == 51) {
										$exercises .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a>'."\n";
									} else {
										$supp .= '<a href="'.$grandchild->path.'">'.stripslashes($grandchild->title).'</a><br />'."\n";
									}
									break;
							}
						}

						if($hubpresenter) {
							$html .= "\t\t\t".'<td>'.$hubpresenter.'<br>'.$breeze.'</td>'."\n";
						} else {
							$html .= "\t\t\t".'<td>'.$breeze.'</td>'."\n";
						}
						$html .= "\t\t\t".'<td>'.$videoi.'</td>'."\n";
						$html .= "\t\t\t".'<td>'.$pdf.'</td>'."\n";
						$html .= "\t\t\t".'<td>'.$supp.'</td>'."\n";
						$html .= "\t\t\t".'<td>'.$exercises.'</td>'."\n";
					} else {
						//$html .= "\t\t\t".'<td colspan="5">'.JText::_('Currently unavilable').'</td>'."\n";
						$html .= "\t\t\t".'<td colspan="5"> </td>'."\n";
					}
					$html .= "\t\t".'</tr>'."\n";
				}
				$html .= "\t".'</tbody>'."\n";
				$html .= '</table>'."\n";
			}
		}
		$html .= '</div><!-- / .main section -->'."\n";
	}
	$html .= '<div class="clear"></div>'."\n";

	echo $html;
?>
