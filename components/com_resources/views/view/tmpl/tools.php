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

	/* Tool page view  */

	$option 		= $this->option;
	$config 		= $this->config;
	$tconfig 		= $this->tconfig;
	$resource 		= $this->resource;
	$params 		= $this->params;
	$authorized 	= $this->authorized;
	$cats 			= $this->cats;
	$tab 			= $this->tab;
	$sections 		= $this->sections;
	$database 		= $this->database;
	$usersgroups 	= $this->usersgroups;
	$helper 		= $this->helper;
	$thistool 		= $this->thistool;
	$curtool 		= $this->curtool;
	$alltools 		= $this->alltools;
	$revision 		= $this->revision;
	$attribs 		= $this->attribs;
	$fsize 			= $this->fsize;

	$juser =& JFactory::getUser();

	$html  = '<div class="main section upperpane">'."\n";
	$html .= '<div class="aside rankarea">'."\n";
	// Show resource ratings
	if (!$thistool) {
		$statshtml = '';

		if ($params->get('show_ranking')) {
			$helper->getCitations();
			$helper->getLastCitationDate();
			$stats = new ToolStats($database, $resource->id, $resource->type, $resource->rating, count($helper->citations), $helper->lastCitationDate);
			$statshtml = $stats->display();
		}

		if ($params->get('show_metadata')) {
			$supported = null;
			$database =& JFactory::getDBO();
			$rt = new ResourcesTags( $database );
			$supported = $rt->checkTagUsage( $config->get('supportedtag'), $resource->id );

			$xtra = '';

			if($params->get('show_audience')) {
				include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'audience.php');
				include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'audience.level.php');
				$ra = new ResourceAudience( $database );
				$audience = $ra->getAudience($resource->id, $versionid = 0 , $getlabels = 1, $numlevels = 4);
				$xtra .= ResourcesHtml:: showSkillLevel($audience, $showtips = 1, $numlevels = 4, $params->get('audiencelink') );
			}
			if ($supported) {
				include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');
				$tag = new TagsTag( $database );
				$tag->loadTag($config->get('supportedtag'));

				$sl = $config->get('supportedlink');
				if ($sl) {
					$link = $sl;
				} else {
					$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
				}

				$xtra .= '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
			}
			$html .= ResourcesHtml::metadata($params, $resource->ranking, $statshtml, $resource->id, $sections, $xtra);
		}
	} else if ($revision=='dev' or !$resource->toolpublished) {
		$txt_meta = ($revision=='dev') ? JText::_('This section will be filled when this tool version gets published.') : JText::_('This section is unavailable in an archive version of a tool.') ;

		if (isset($resource->curversion) && $resource->curversion) {
			$txt_meta .= ' '.JText::_('Consult the latest published version').' <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&rev='.$curtool->revision).'">'.$resource->curversion.'</a> '.JText::_('for most current information.');
		}

		// show empty meta data box
		$html .= '<div class="metaplaceholder"><p>'.$txt_meta.'</p></div>'."\n";
	}

	$html .= ' </div><!-- / .aside -->'."\n";
	$html .= '<div class="subject">'."\n";
	$html .= ' <div class="overviewcontainer">'."\n";
	$html .= ResourcesHtml::title( $option, $resource, $params, $authorized, $config, 0 );

	// Display authors
	if ($params->get('show_authors')) {
			// Get contributors of this version		
			if ($alltools && $resource->revision!='dev') {
				$helper->getToolAuthors($resource->alias, $resource->revision);
			}

			// Get contributors on this resource
			$helper->getContributors(true, 1);
			if ($helper->contributors && $helper->contributors != '<br />') {
				$html .= '<div id="authorslist">'."\n";
				$html .= $helper->contributors."\n";
				$html .= '</div>'."\n";
			}
	}

	// Display "at a glance"
	$html .= '<p class="ataglance">';
	$introtext = $resource->introtext ? Hubzero_View_Helper_Html::shortenText(stripslashes($resource->introtext), 255, 0) : Hubzero_View_Helper_Html::shortenText(stripslashes($resource->fulltext), 255, 0);
	$html .= $introtext;
	//$html .= ' <a href="">'.JText::_('Learn more').' &rsaquo;</a>';
	//$html .= JText::_('in') . ' <a href="' . JRoute::_('index.php?option=' . $option . '&type=' . $resource->_type->alias). '">' . $resource->_type->type . '</a>';
	$html .= '</p>'."\n";
	$html .= ' </div><!-- / .overviewcontainer -->'."\n";

	$html .= ' <div class="aside launcharea">'."\n";

	// perform check for groups

	// Private/Public resource access check
	if ($resource->access == 3 && !in_array($resource->group_owner, $usersgroups) && !$authorized) {
		$ghtml = JText::_('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP').' ';
		$allowedgroups = $resource->getGroups();
		foreach ($allowedgroups as $allowedgroup)
		{
			$ghtml .= '<a href="'.JRoute::_('index.php?option=com_groups&gid='.$allowedgroup).'">'.$allowedgroup.'</a>, ';
		}
		$ghtml = substr($ghtml,0,strlen($ghtml) - 2);
		$html .= '<p class="warning">'.$ghtml.'</p>'."\n";
	} else {
		// get launch button
		$helper->getFirstChild();
		$html .= ResourcesHtml::primary_child( $option, $resource, $helper->firstChild, '' );

		// Display version info
		$versiontext = '<strong>';
		if ($revision && $thistool) {
			$versiontext .= $thistool->version.'</strong>';
			if ($resource->revision!='dev') {
				$versiontext .=  '<br /> '.ucfirst(JText::_('COM_RESOURCES_PUBLISHED_ON')).' ';
				$versiontext .= ($thistool->released && $thistool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $thistool->released, '%d %b %Y'): JHTML::_('date', $resource->publish_up, '%d %b %Y');
				$versiontext .= ($thistool->unpublished && $thistool->unpublished != '0000-00-00 00:00:00') ? ', '.JText::_('COM_RESOURCES_UNPUBLISHED_ON').' '.JHTML::_('date', $thistool->unpublished, '%d %b %Y'): '';
			} else {
				$versiontext .= ' ('.JText::_('COM_RESOURCES_IN_DEVELOPMENT').')';
			}
		} else if ($curtool) {
			$versiontext .= $curtool->version.'</strong> - '.JText::_('COM_RESOURCES_PUBLISHED_ON').' ';
			$versiontext .= ($curtool->released && $curtool->released != '0000-00-00 00:00:00') ? JHTML::_('date', $curtool->released, '%d %b %Y'): JHTML::_('date', $resource->publish_up, '%d %b %Y');
		}

		if (!$thistool) {
			$html .= "\t\t\t\t".'<p class="curversion">'.JText::_('COM_RESOURCES_VERSION').' '.$versiontext.'</p>'."\n";
		} else if ($revision == 'dev') {
			$html .= "\t\t\t\t".'<p class="devversion">'.JText::_('COM_RESOURCES_VERSION').' '.$versiontext;
			$html .= $resource->toolpublished ? ' <span>'.JText::_('View').' <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&active=versions').'">'.JText::_('other versions').'</a></span>' : '';
			$html .='</p>'."\n";
		} else {
			// Show archive message		
			$msg = '<strong>'.JText::_('COM_RESOURCES_ARCHIVE').'</strong> '.JText::_('COM_RESOURCES_VERSION').' '.$versiontext;
			if (isset($resource->curversion) && $resource->curversion) {
				$msg .= ' <br />'.JText::_('COM_RESOURCES_LATEST_VERSION').': <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&rev='.$curtool->revision).'">'.$resource->curversion.'</a>.';
			}
			$msg .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&active=versions').'">'.JText::_('All versions').'</a>';
			$html .= ResourcesHtml::archive($msg)."\n";
		}

		// doi message
		if ($revision != 'dev' && ($resource->doi || $resource->doi_label)) {
			if($resource->doi && $tconfig->get('doi_shoulder'))
			{
				$doi = 'doi:' . $tconfig->get('doi_shoulder') . DS . strtoupper($resource->doi);
			}
			else
			{
				$doi = 'doi:10254/' . $tconfig->get('doi_prefix') . $resource->id . '.' . $resource->doi_label;
			}
			
			$html .= "\t\t".'<p class="doi">'.$doi.' <span><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&active=about').'#citethis">'.JText::_('cite this').'</a></span></p>'."\n";
		}

		// Open/closed source
		if (isset($resource->toolsource) && $resource->toolsource == 1 && isset($resource->tool)) { // open source
			$html .= '<p class="opensource_license">'.JText::_('Open source').': <a class="popup" href="index.php?option='.$this->option.'&task=license&tool='.$resource->tool.'&no_html=1">license</a> ';
			$html .= ($resource->taravailable) ? ' |  <a href="index.php/'.$resource->tarname.'?option='.$option.'&task=sourcecode&tool='.$resource->tool.'">'.JText::_('download').'</a> '."\n" : ' | <span class="unavail">'.JText::_('code unavaialble').'</span>'."\n";
			$html .= '</p>'."\n";
		} elseif (isset($resource->toolsource) && !$resource->toolsource) { // closed source, archive page
			$html .= '<p class="closedsource_license">'.JText::_('COM_RESOURCES_TOOL_IS_CLOSED_SOURCE').'</p>'."\n";
		}
		// do we have a first-time user guide?
		$helper->getChildren( $resource->id, 0, 'all' );
		$children = $helper->children;

		if (!$thistool) {
			$guide = 0;
			foreach ($children as $child)
			{
				$title = ($child->logicaltitle)
						? $child->logicaltitle
						: stripslashes($child->title);
				if ($child->access == 0 || ($child->access == 1 && !$juser->get('guest'))) {
					if (strtolower($title) !=  preg_replace('/user guide/', '', strtolower($title))) {
						$guide = $child;
					}
				}
			}
			$url = $guide ? ResourcesHtml::processPath($option, $guide, $resource->id) : '';
			$html .= "\t\t".'<p class="supdocs">'."\n";
			if ($url) {
				$html .= "\t\t\t".'<span><span class="guide"><a href="'.$url.'">'.JText::_('First-Time User Guide').'</a></span></span>'."\n";
			}
			$html .= "\t\t\t".'<span class="viewalldocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$resource->id.'&active=supportingdocs').'">'.JText::_('View All Supporting Documents').'</a></span>'."\n";
			$html .= "\t\t".'</p>'."\n";
		}
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

		// Link to all resources of this type
		/*$normalized_valid_chars = 'a-zA-Z0-9';
		$typenorm = preg_replace("/[^$normalized_valid_chars]/", "", $resource->getTypeTitle());
		$typenorm = strtolower($typenorm);
		$html .= ' <p class="viewalltypes">'.JText::_('View all').' <a href="'.JRoute::_('index.php?option='.$option.a.'type='.$typenorm).'">'.$resource->getTypeTitle().'</a></p>'."\n";*/

		// show what's popular
		if ($tab == 'about') {
			ximport('Hubzero_Module_Helper');
			$html .= Hubzero_Module_Helper::renderModules('extracontent');
		}

		$html .= ' </div><!-- / .aside -->'."\n";
		$html .= ' <div class="subject tabbed">'."\n";
		$html .= ResourcesHtml::tabs( $option, $resource->id, $cats, $tab, $resource->alias );
		$html .= ResourcesHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
		$html .= '</div><!-- / .subject -->'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '</div><!-- / .main section -->'."\n";
	}
	$html .= '<div class="clear"></div>'."\n";

	echo $html;
?>