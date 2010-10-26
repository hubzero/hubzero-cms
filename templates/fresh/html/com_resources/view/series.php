<?php 
/**
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license	GNU General Public License, version 2 (GPLv2) 
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

$xhub =& XFactory::getHub();
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
			include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php');
			$tag = new TagsTag( $database );
			$tag->loadTag($config->get('supportedtag'));

			$sl = $config->get('supportedlink');
			if ($sl) {
				$link = $sl;
			} else {
				$link = JRoute::_('index.php?option=com_tags'.a.'tag='.$tag->tag);
			}

			$xtra = '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
		}
		$html .= ResourcesHtml::metadata($params, $resource->ranking, $statshtml, $resource->id, $sections, $xtra);
	}
	
	$html .= '<div class="type_head"><h1>'.$xhub->getCfg('hubShortName').'</h1><h2>';
	switch ($resource->type)
		{
			case 31: $html .= 'Series'; break;
			case 7:  $html .= 'Tool'; break;
			case 41:  $html .= 'Video'; break;
			case 66:  $html .= 'Learning Object'; break;
			case 4:
			default:
					$html .= 'Resource'; break;
		}	
	$html .= '</h2></div>';
	
	$ccount = $helper->getStandaloneCount( $filters );
	
	
	
	
	
	
	
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
	$html .= '<p class="ataglance">';
	$html .= $resource->introtext ? Hubzero_View_Helper_Html::shortenText(stripslashes($resource->introtext), 250, 0) : '';
	//$html .= ' <a href="">'.JText::_('Learn more').' &rsaquo;</a>'."\n";
	$html .= '</p>'."\n";
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
		$xhub =& XFactory::getHub();
		
		
		
		$html .= $tab != 'play' ? ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' ) : '';
				
			
		
		// Display some supporting documents
		$filterdocs = ($resource->type == 6 or $resource->type == 31 or $resource->type == 2) ? 'no' : 'all';
		$helper->getChildren( $resource->id, 0, $filterdocs );
		$children = $helper->children;
		
		$iTunes = 0;		
		$supdocs = 0;
		$totaldocs = 0;
		$realdocs = 0;
		$fctype = is_object($helper->firstChild) ? ResourcesHtml::getFileExtension($helper->firstChild->path) : '';
	
		/*	// View more link?
			if ($ccount > 0) {
				//standalone children
				$html .= t.t.'<p class="supdocs"><span class="viewalldocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.a.'#series').'">'.JText::_('Additional Series Links').' ('.$ccount.')</a></span></p>'."\n";
			}*/
			
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
			
						
			if ($supdocs > 0 && $otherdocs > 0) {
				$supln .= ' <li class="otherdocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.a.'active=supportingdocs').'" title="'.JText::_('View All').' '.$realdocs.' '.JText::_('Supporting Documents').' ">'.$otherdocs.' '.JText::_('more').' &rsaquo;</a></li>'."\n";
			} else if (!$supdocs && $realdocs > 0 && $tab != 'play' && is_object($helper->firstChild)) {
				$html .= t.t.'<p class="supdocs"><span class="viewalldocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.a.'active=supportingdocs').'">'.JText::_('Additional Documents').' ('.$realdocs.')</a></span></p>'."\n";
			}
			
			
			$supln .= '</ul>'."\n";
			$supdocs = $supdocs && $tab != 'play'  ? $supln : 0;
		}
		
		// Show icons of other available formats
		if ($supdocs) {
			$html .= t.t.t.$supdocs."\n";		
		}
		
		$html .= $feeds ? $feeds : '';
		$html .= $tab != 'play' ? ResourcesHtml::license( $params->get( 'license', '' ) ) : '';			
	} // --- end else (if group check passed)
	
	$html .= ' </div><!-- / .aside launcharea -->'."\n";	
	$html .= ' </div><!-- / .subject -->'."\n";
			
	if ($resource->access == 3 && (!in_array($resource->group_owner, $usersgroups) || $authorized=0)) {
		// show nothing else
		$html .= '</div><!-- / .main section -->'."\n";		
	} else {
		$html .= '<div class="clear sep"></div>'."\n";	
		$html .= '</div><!-- / .main section -->'."\n";		
		$html .= '<div class="main section noborder">'."\n";
		$html .= ' <div class="aside extracontent">'."\n";
		
	if ($ccount >= 0) {
		// Initiate paging for children
		//jimport('joomla.html.pagination');
		//$pageNav = new JPagination( $ccount, $filters['start'], $filters['limit'] );

		// Get children
		$children = $helper->getStandaloneChildren( $filters );

		// Build the results
		/*
		$sortbys = array();
		$sortbys['date'] = JText::_('DATE');
		$sortbys['title'] = JText::_('TITLE');
		$sortbys['author'] = JText::_('AUTHOR');
		if ($config->get('show_ranking')) {
			$sortbys['ranking'] = JText::_('RANKING');
		}
		$sortbys['ordering'] = JText::_('ORDERING');
		*/
		if ($resource->alias) {
			$url = 'index.php?option='.$option.'&alias='.$resource->alias;
		} else {
			$url = 'index.php?option='.$option.'&id='.$resource->id;
		}
		/*
		$paging = $pageNav->getListFooter();
		$paging = str_replace('resources/?','resources/'.$resource->id.'?',$paging);
		$paging = str_replace('resources?','resources/'.$resource->id.'?',$paging);
		$paging = str_replace('?/resources/'.$resource->id,'?',$paging);
		$paging = str_replace('?','?sortby='.$filters['sortby'].'&',$paging);
		$paging = str_replace('&&','&',$paging);
		$paging = str_replace('&amp;&amp;','&amp;',$paging);
		*/
		//$html .= '<div class ="series_aside">';
		//$html .= t.'<a name="series"></a>'."\n";
		//$html .= '<h3>'. JText::_('In This Series') .'</h3>'."\n";
		$html .= '<form method="get" action="'.JRoute::_($url).'">'."\n";
		$html .= t.'<div class="aside">'."\n";
		//$html .= t.t.'<fieldset class="controls">'."\n";
		//$html .= t.t.t.'<label>'."\n";
		//$html .= t.t.t.t.JText::_('COM_RESOURCES_SORT_BY').':'."\n";
		//$html .= t.t.t.t.ResourcesHtml::formSelect('sortby', $sortbys, $filters['sortby'], '')."\n";
		//$html .= t.t.t.'</label>'."\n";
		//$html .= t.t.t.'<input type="submit" value="'.JText::_('COM_RESOURCES_GO').'" />'."\n";
		//$html .= t.t.'</fieldset>'."\n";
		//$html .= t.'</div><!-- / .aside -->'."\n";
		//$html .= t.'<div class="subject">'."\n";
		//$html .= ResourcesHtml::writeSeriesResults( $database, $children, $authorized );
		//$html .= t.'<div class="clear"></div><!-- / .clear -->'."\n";
		//$html .= $paging;
		//$html .= t.'</div><!-- / .subject -->'."\n";
		//$html .= t.'<div class="clear"></div><!-- / .clear -->'."\n";
		//$html .= '</form>'."\n";
		$html .= '</div>';
	}
		
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
			ximport('xmodule');
			$html .= XModuleHelper::renderModules('extracontent');
		}		
		$html .= ' </div><!-- / .aside extracontent -->'."\n";		
		
		$html .= ' <div class="subject tabbed">'."\n";
		$html .= ResourcesHtml::tabs( $option, $resource->id, $cats, $tab, $resource->alias );
		$html .= ResourcesHtml::sections( $sections, $cats, $tab, 'hide', 'main' );	
		$html .= '</div><!-- / .subject -->'."\n";
		$html .= '<div class="clear"></div>'."\n";		
		if ($tab == 'about') {	
			if ($ccount > 0) {
					// Initiate paging for children
					jimport('joomla.html.pagination');
					$pageNav = new JPagination( $ccount, $filters['start'], $filters['limit'] );
		
					// Build the results
					$sortbys = array();
					$sortbys['date'] = JText::_('DATE');
					$sortbys['title'] = JText::_('TITLE');
					$sortbys['author'] = JText::_('AUTHOR');
					if ($config->get('show_ranking')) {
						$sortbys['ranking'] = JText::_('RANKING');
					}
					$sortbys['ordering'] = JText::_('ORDERING');
	
					if ($resource->alias) {
						$url = 'index.php?option='.$option.'&alias='.$resource->alias;
					} else {
						$url = 'index.php?option='.$option.'&id='.$resource->id;
					}
					
					$paging = $pageNav->getListFooter();
					$paging = str_replace('resources/?','resources/'.$resource->id.'?',$paging);
					$paging = str_replace('resources?','resources/'.$resource->id.'?',$paging);
					$paging = str_replace('?/resources/'.$resource->id,'?',$paging);
					$paging = str_replace('?','?sortby='.$filters['sortby'].'&',$paging);
					$paging = str_replace('&&','&',$paging);
					$paging = str_replace('&amp;&amp;','&amp;',$paging);
	
					$html .= t.'<a name="series"></a>'."\n";
					$html .= '<h3>'. JText::_('Series Item Details') .'</h3>'."\n";
					$html .= '<form method="get" action="'.JRoute::_($url).'">'."\n";
					$html .= t.'<div class="aside">'."\n";
					$html .= t.t.'<fieldset class="controls">'."\n";
					$html .= t.t.t.'<label>'."\n";
					$html .= t.t.t.t.JText::_('COM_RESOURCES_SORT_BY').':'."\n";
					$html .= t.t.t.t.ResourcesHtml::formSelect('sortby', $sortbys, $filters['sortby'], '')."\n";
					$html .= t.t.t.'</label>'."\n";
					$html .= t.t.t.'<input type="submit" value="'.JText::_('COM_RESOURCES_GO').'" />'."\n";
					$html .= t.t.'</fieldset>'."\n";
					$html .= t.'</div><!-- / .aside -->'."\n";
					$html .= t.'<div class="subject">'."\n";
					$html .= ResourcesHtml::writeSeriesResults( $database, $children, $authorized , 3, false);
					$html .= t.'<div class="clear"></div><!-- / .clear -->'."\n";
					$html .= $paging;
					$html .= t.'</div><!-- / .subject -->'."\n";
					$html .= t.'<div class="clear"></div><!-- / .clear -->'."\n";
					$html .= '</form>'."\n";
				}
		}
		$html .= '</div><!-- / .main section -->'."\n";
		
	}
	$html .= '<div class="clear"></div>'."\n";
	
	echo $html;
?>


<?php 
// Show course listings under 'about' tab
		

?>