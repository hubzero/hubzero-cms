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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

	$useBlocks  = $this->config->get('curation', 0);
//	$experiment = $useBlocks ? true : false;
	$experiment = false;

	/* Non-Tool Publication page view  */

	$option 		= $this->option;
	$config 		= $this->config;
	$publication 	= $this->publication;
	$params 		= $this->params;
	$authorized 	= $this->authorized;
	$restricted 	= $this->restricted;
	$cats 			= $this->cats;
	$tab 			= $this->tab;
	$sections 		= $this->sections;
	$database 		= $this->database;
	$usersgroups 	= $this->usersgroups;
	$helper 		= $this->helper;
	$content    	= $this->content;
	$authors 		= $this->authors;
	$filters 		= $this->filters;
	$version 	    = $this->version;

	$juser = JFactory::getUser();

if ($experiment == false)
{

	$html  = '<section class="main upperpane">'."\n";
	$html .= '<div class="aside rankarea">'."\n";

	// Show stats
	$statshtml = '';
	$helper->getCitations();
	$helper->getLastCitationDate();
	$stats = new AndmoreStats($database, $publication->id,
		$publication->master_rating, count($helper->citations), $helper->lastCitationDate);
	$statshtml = $stats->display();

	$xtra = '';

	// Show audience
	if ($params->get('show_audience'))
	{
		$ra 		= new PublicationAudience( $database );
		$audience 	= $ra->getAudience(
			$publication->id,
			$publication->version_id,
			$getlabels = 1,
			$numlevels = 4
		);
		$ral 		= new PublicationAudienceLevel ( $database );
		$levels 	= $ral->getLevels( 4, array(), 0 );
		$skillpop 	=  PublicationsHtml::skillLevelPopup($levels, $params->get('audiencelink'));
		$xtra 	   .= PublicationsHtml::showSkillLevel($audience, $numlevels = 4, $skillpop);
	}

	// Supported publication?
	$supported = null;
	$rt = new PublicationTags( $database );
	$supported = $rt->checkTagUsage( $config->get('supportedtag'), $publication->id );

	if ($supported)
	{
		include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');
		$tag = new TagsTableTag( $database );
		$tag->loadTag($config->get('supportedtag'));

		$sl = $config->get('supportedlink');
		if ($sl) {
			$link = $sl;
		} else {
			$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
		}

		$xtra = '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
	}

	$html .= PublicationsHtml::metadata($option, $params, $publication, $statshtml, $sections, $version, $xtra, $this->lastPubRelease);
	$html .= '</div><!-- / .aside -->'."\n";
	$html .= '<div class="subject">'."\n";
	$html .= ' <div class="overviewcontainer">'."\n";
	$html .= PublicationsHtml::title( $option, $publication );

	// Display authors
	if ($params->get('show_authors')) {
		if ($authors) {
			$html .= '<div id="authorslist">'."\n";
			$html .= $helper->showContributors( $authors, true, false )."\n";
			$html .= '</div>'."\n";
		}
	}

	// Display mini abstract
	$html .= '<p class="ataglance">';
	$html .= $publication->abstract ? \Hubzero\Utility\String::truncate(stripslashes($publication->abstract), 250) : '';
	$html .= '</p>'."\n";

	// Show published date and category
	$html .= PublicationsHtml::showSubInfo( $publication, $option );

	$html .= ' </div><!-- / .overviewcontainer -->'."\n";
	$html .= ' <div class="aside launcharea">'."\n";
	$feeds = '';

	// Sort out primary files and draw a launch button
	if ($useBlocks && $tab != 'play')
	{
		// Get primary elements
		$elements = $this->publication->_curationModel->getElements(1);

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->database);

		$authorized = ($restricted && !$authorized) ? false : true;

		if ($elements)
		{
			$element = $elements[0];

			// Draw button
			$launcher = $attModel->drawLauncher(
				$element->manifest->params->type,
				$this->publication,
				$element,
				$elements,
				$authorized
			);

			$html .= $launcher;
		}
	}
	elseif ($content['primary'] && count($content['primary']) > 0 && $tab != 'play')
	{
		$primaryParams 	 = new JParameter( $content['primary'][0]->params );
		$serveas 		 = $primaryParams->get('serveas');
		$html 			.=  PublicationsHtml::drawPrimaryButton( $option, $publication, $version, $content, $this->path, $serveas, $restricted, $authorized );
	}
	elseif ($tab != 'play' && $publication->state != 0)
	{
		$html .= '<p class="error statusmsg">'.JText::_('COM_PUBLICATIONS_ERROR_CONTENT_UNAVAILABLE').'</p>';
	}

	// Sort out supporting docs
	$html .= $tab != 'play' && $publication->state != 0
		   ? PublicationsHtml::sortSupportingDocs( $publication, $version, $option, $content['secondary'], $restricted, $this->archPath )
		   : '';

	// Show version information
	$html .=  $tab != 'play' ? PublicationsHtml::showVersionInfo( $publication, $version, $option, $config, $this->lastPubRelease ) : '';

	// Show license information
	$html .= $tab != 'play' && $this->license && $this->license->name != 'standard'
			? PublicationsHtml::showLicense( $publication, $version, $option, $this->license, 'play' ) : '';

	$html .= ' </div><!-- / .aside launcharea -->'."\n";
	$html .= '<div class="clear"></div>'."\n";
	$editurl = JRoute::_('index.php?option=com_projects&alias='
		. $publication->project_alias . '&active=publications&pid=' . $publication->id);
	$editurl.= '?version='.$version;

	// Build pub url
	$route = $publication->project_provisioned == 1
				? 'index.php?option=com_publications&task=submit'
				: 'index.php?option=com_projects&alias=' . $publication->project_alias . '&active=publications';
	$editurl = JRoute::_($route . '&pid=' . $publication->id).'?version='.$version;

	// Show status for authorized users
	if ($this->contributable)
	{
		$html .= PublicationsHtml::showAccessMessage( $publication, $option, $this->authorized, $restricted, $editurl );
	}

	$html .= '</div><!-- / .subject -->'."\n";
	if ($publication->access == 2 && (!$authorized && $restricted)) {
		// show nothing else
		$html .= '</section><!-- / .main section -->'."\n";
	} else {
		$html .= '<div class="clear sep"></div>'."\n";
		$html .= '</section><!-- / .main section -->'."\n";

		$html .= '<section class="main section noborder">'."\n";
		$html .= ' <div class="subject tabbed">'."\n";
		$html .= PublicationsHtml::tabs( $option, $publication->id, $cats, $tab, $publication->alias, $version );
		$html .= PublicationsHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
		$html .= '</div><!-- / .subject -->'."\n";
		$html .= ' <div class="aside extracontent">'."\n";

		// Get Related plugin
		JPluginHelper::importPlugin( 'publications' );
		$dispatcher = JDispatcher::getInstance();

		// Show related content
		$out = $dispatcher->trigger( 'onPublicationSub', array($publication, $option, 1) );
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
			$html .= \Hubzero\Module\Helper::renderModules('extracontent');
		}
		$html .= ' </div><!-- / .aside extracontent -->'."\n";
		$html .= '</section><!-- / .main section -->'."\n";
	}
	$html .= '<div class="clear"></div>'."\n";

	echo $html;

}
else
{
	// New launcher layout
	$view = new JView(
		array('name' => 'view', 'layout' => 'launcher')
	);
	$view->publication 	 	= $publication;
	$view->contributable 	= $this->contributable;
	$view->option 		 	= $this->option;
	$view->config 		 	= $this->config;
	$view->params 		 	= $this->params;
	$view->authorized 	 	= $this->authorized;
	$view->restricted 	 	= $this->restricted;
	$view->database 	 	= $this->database;
	$view->lastPubRelease 	= $this->lastPubRelease;
	$view->version		 	= $this->version;
	$view->license			= $this->license;
	$view->sections			= $this->sections;
	$view->cats				= $this->cats;
	echo $view->loadTemplate();

	// Tabbed areas
	$html = '';
	$html .= '<section class="main section noborder">'."\n";
	$html .= ' <div class="subject tabbed">'."\n";
	$html .= PublicationsHtml::tabs(
		$option,
		$publication->id,
		$cats,
		$tab,
		$publication->alias,
		$version
	);
	$html .= PublicationsHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
	$html .= '</div><!-- / .subject -->'."\n";
	$html .= ' <div class="aside extracontent">'."\n";

	JPluginHelper::importPlugin( 'publications');
	$dispatcher = JDispatcher::getInstance();

	// Show related content
	$out = $dispatcher->trigger( 'onPublicationSub', array($publication, $option, 1) );
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
		$html .= \Hubzero\Module\Helper::renderModules('extracontent');
	}
	$html .= ' </div><!-- / .aside extracontent -->'."\n";
	$html .= '</section><!-- / .main section -->'."\n";

	echo $html;
}