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

// Which layout?
$launcherLayout  = $this->config->get('launcher_layout', 0);
$authorized = ($this->restricted && !$this->authorized) ? false : true;

$juser = JFactory::getUser();
$html = '';

if ($launcherLayout)
{
	// New launcher layout
	$view = new JView(
		array('name' => 'view', 'layout' => 'launcher')
	);
	$view->publication 	 	= $this->publication;
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
}
else
{
	$html  = '<section class="main upperpane">'."\n";
	$html .= '<div class="aside rankarea">'."\n";

	// Show stats
	$statshtml = '';
	$this->helper->getCitations();
	$this->helper->getLastCitationDate();
	$stats = new AndmoreStats($this->database, $this->publication->id,
		$this->publication->master_rating, count($this->helper->citations), $this->helper->lastCitationDate);
	$statshtml = $stats->display();

	$xtra = '';

	// Show audience
	if ($this->params->get('show_audience'))
	{
		$ra 		= new PublicationAudience( $this->database );
		$audience 	= $ra->getAudience(
			$this->publication->id,
			$this->publication->version_id,
			$getlabels = 1,
			$numlevels = 4
		);
		$ral 		= new PublicationAudienceLevel ( $this->database );
		$levels 	= $ral->getLevels( 4, array(), 0 );
		$skillpop 	=  PublicationsHtml::skillLevelPopup($levels, $this->params->get('audiencelink'));
		$xtra 	   .= PublicationsHtml::showSkillLevel($audience, $numlevels = 4, $skillpop);
	}

	// Supported publication?
	$supported = null;
	$rt = new PublicationTags( $this->database );
	$supported = $rt->checkTagUsage( $this->config->get('supportedtag'), $this->publication->id );

	if ($supported)
	{
		include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');
		$tag = new TagsTableTag( $this->database );
		$tag->loadTag($this->config->get('supportedtag'));

		$sl = $this->config->get('supportedlink');
		if ($sl) {
			$link = $sl;
		} else {
			$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
		}

		$xtra = '<p class="supported"><a href="'.$link.'">'.$tag->raw_tag.'</a></p>';
	}

	$html .= PublicationsHtml::metadata($this->option, $this->params, $this->publication, $statshtml, $this->sections, $this->version, $xtra, $this->lastPubRelease);
	$html .= '</div><!-- / .aside -->'."\n";
	$html .= '<div class="subject">'."\n";
	$html .= ' <div class="overviewcontainer">'."\n";
	$html .= PublicationsHtml::title( $this->option, $this->publication );

	// Display authors
	if ($this->params->get('show_authors')) {
		if ($this->authors) {
			$html .= '<div id="authorslist">'."\n";
			$html .= $this->helper->showContributors( $this->authors, true, false, false, $this->params->get('format_authors', 0) )."\n";
			$html .= '</div>'."\n";
		}
	}

	// Display mini abstract
	$html .= '<p class="ataglance">';
	$html .= $this->publication->abstract ? \Hubzero\Utility\String::truncate(stripslashes($this->publication->abstract), 250) : '';
	$html .= '</p>'."\n";

	// Show published date and category
	$html .= PublicationsHtml::showSubInfo( $this->publication, $this->option );

	$html .= ' </div><!-- / .overviewcontainer -->'."\n";
	$html .= ' <div class="aside launcharea">'."\n";
	$feeds = '';

	// Sort out primary files and draw a launch button
	if ($this->config->get('curation', 0) && $this->tab != 'play')
	{
		// Get primary elements
		$elements = $this->publication->_curationModel->getElements(1);

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->database);

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
	elseif ($this->content['primary'] && count($this->content['primary']) > 0 && $this->tab != 'play')
	{
		$primaryParams 	 = new JParameter( $this->content['primary'][0]->params );
		$serveas 		 = $primaryParams->get('serveas');
		$html 			.=  PublicationsHtml::drawPrimaryButton( $this->option, $this->publication, $this->version, $this->content, $this->path, $serveas, $this->restricted, $this->authorized );
	}
	elseif ($this->tab != 'play' && $this->publication->state != 0)
	{
		$html .= '<p class="error statusmsg">'.JText::_('COM_PUBLICATIONS_ERROR_CONTENT_UNAVAILABLE').'</p>';
	}

	// Sort out supporting docs
	$html .= $this->tab != 'play' && $this->publication->state != 0
		   ? PublicationsHtml::sortSupportingDocs( $this->publication, $this->version, $this->option, $this->content['secondary'], $this->restricted, $this->archPath )
		   : '';

	// Show version information
	$html .=  $this->tab != 'play' ? PublicationsHtml::showVersionInfo( $this->publication, $this->version, $this->option, $this->config, $this->lastPubRelease ) : '';

	// Show license information
	$html .= $this->tab != 'play' && $this->license && $this->license->name != 'standard'
			? PublicationsHtml::showLicense( $this->publication, $this->version, $this->option, $this->license, 'play' ) : '';

	$html .= ' </div><!-- / .aside launcharea -->'."\n";
	$html .= '<div class="clear"></div>'."\n";
	$editurl = JRoute::_('index.php?option=com_projects&alias='
		. $this->publication->project_alias . '&active=publications&pid=' . $this->publication->id);
	$editurl.= '?version='.$this->version;

	// Build pub url
	$route = $this->publication->project_provisioned == 1
				? 'index.php?option=com_publications&task=submit'
				: 'index.php?option=com_projects&alias=' . $this->publication->project_alias . '&active=publications';
	$editurl = JRoute::_($route . '&pid=' . $this->publication->id).'?version='.$this->version;

	// Show status for authorized users
	if ($this->contributable)
	{
		$html .= PublicationsHtml::showAccessMessage( $this->publication, $this->option, $this->authorized, $this->restricted, $editurl );
	}

	$html .= '</div><!-- / .subject -->'."\n";
}

if ($this->publication->access == 2 && (!$this->authorized && $this->restricted)) {
	// show nothing else
	$html .= '</section><!-- / .main section -->'."\n";
} else {
	$html .= '<div class="clear sep"></div>'."\n";
	$html .= '</section><!-- / .main section -->'."\n";

	$html .= '<section class="main section noborder">'."\n";
	$html .= ' <div class="subject tabbed">'."\n";
	$html .= PublicationsHtml::tabs(
		$this->option,
		$this->publication->id,
		$this->cats,
		$this->tab,
		$this->publication->alias,
		$this->version
	);

	$html .= PublicationsHtml::sections( $this->sections, $this->cats, $this->tab, 'hide', 'main' );

	// Add footer notice
	if ($this->tab == 'about')
	{
		$html .= PublicationsHtml::footer( $this->publication );
	}

	$html .= '</div><!-- / .subject -->'."\n";
	$html .= ' <div class="aside extracontent">'."\n";
}


JPluginHelper::importPlugin( 'publications');
$dispatcher = JDispatcher::getInstance();

// Show related content
$out = $dispatcher->trigger( 'onPublicationSub', array($this->publication, $this->option, 1) );
if (count($out) > 0) {
	foreach ($out as $ou)
	{
		if (isset($ou['html'])) {
			$html .= $ou['html'];
		}
	}
}

// Show what's popular
if ($this->tab == 'about') {
	$html .= \Hubzero\Module\Helper::renderModules('extracontent');
}
$html .= ' </div><!-- / .aside extracontent -->'."\n";
$html .= '</section><!-- / .main section -->'."\n";
$html .= '<div class="clear"></div>'."\n";

echo $html;