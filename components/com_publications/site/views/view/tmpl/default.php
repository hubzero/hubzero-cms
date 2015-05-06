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

$this->css()
	 ->css('jquery.fancybox.css', 'system')
     ->js();

$html = '';

$this->publication->authors();
$this->publication->attachments();
$this->publication->license();

// New launcher layout?
if ($this->config->get('launcher_layout', 0))
{
	$this->view('launcher')
	     ->set('option', $this->option)
	     ->set('publication', $this->publication)
	     ->set('config', $this->config)
	     ->set('contributable', $this->contributable)
	     ->set('authorized', $this->authorized)
	     ->set('restricted', $this->restricted)
	     ->set('database', $this->database)
	     ->set('lastPubRelease', $this->lastPubRelease)
	     ->set('version', $this->version)
	     ->set('sections', $this->sections)
	     ->set('cats', $this->cats)
	     ->display();
}
else
{ ?>
	<section class="main upperpane">
		<div class="aside rankarea">

	<?php
	// Show metadata
	$this->view('_metadata')
	     ->set('option', $this->option)
	     ->set('publication', $this->publication)
	     ->set('config', $this->config)
	     ->set('version', $this->version)
	     ->set('sections', $this->sections)
	     ->set('cats', $this->cats)
	     ->set('params', $this->publication->params)
	     ->set('lastPubRelease', $this->lastPubRelease)
	     ->display();
?>
	</div><!-- / .aside -->

	<div class="subject">
		<div class="overviewcontainer">
			<?php echo \Components\Publications\Helpers\Html::title( $this->publication ); ?>
<?php
	// Display authors
	if ($this->publication->params->get('show_authors') && $this->publication->_authors) { ?>
		<div id="authorslist">
			<?php echo \Components\Publications\Helpers\Html::showContributors($this->publication->_authors, true, false, false, false, $this->publication->params->get('format_authors', 0)); ?>
		</div>
	<?php }	?>
	<p class="ataglance"><?php echo $this->publication->abstract ? \Hubzero\Utility\String::truncate(stripslashes($this->publication->abstract), 250) : ''; ?></p>

<?php
	// Show published date and category
	echo \Components\Publications\Helpers\Html::showSubInfo( $this->publication ); ?>
		</div><!-- / .overviewcontainer -->
		<div class="aside launcharea">
<?php

	// Sort out primary files and draw a launch button
	if ($this->tab != 'play')
	{
		// Get primary elements
		$elements = $this->publication->_curationModel->getElements(1);

		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->database);

		if ($elements)
		{
			$element = $elements[0];

			// Draw button
			$launcher = $attModel->drawLauncher(
				$element->manifest->params->type,
				$this->publication,
				$element,
				$elements,
				$this->publication->access('view-all')
			);

			$html .= $launcher;
		}
	}

	// Show additional docs
	$html .=  $this->tab != 'play' ? \Components\Publications\Helpers\Html::drawSupportingItems( $this->publication) : '';

	// Show version information
	$html .=  $this->tab != 'play' ? \Components\Publications\Helpers\Html::showVersionInfo( $this->publication) : '';

	// Show license information
	$html .= $this->tab != 'play' && $this->publication->license() && $this->publication->license()->name != 'standard'
			? \Components\Publications\Helpers\Html::showLicense( $this->publication, 'play' ) : '';

	$html .= ' </div><!-- / .aside launcharea -->'."\n";
	$html .= '<div class="clear"></div>'."\n";

	// Show status for authorized users
	if ($this->contributable)
	{
		$html .= \Components\Publications\Helpers\Html::showAccessMessage($this->publication);
	}

	$html .= '</div><!-- / .subject -->'."\n";
}

// Part below
if (!$this->publication->access('view-all'))
{
	// show nothing else
	$html .= '</section><!-- / .main section -->'."\n";
	echo $html;
	return;
}
else
{
	$html .= '<div class="clear sep"></div>'."\n";
	$html .= '</section><!-- / .main section -->'."\n";

	$html .= '<section class="main section noborder">'."\n";
	$html .= ' <div class="subject tabbed">'."\n";

	$html .= \Components\Publications\Helpers\Html::tabs(
		$this->option,
		$this->publication->id,
		$this->cats,
		$this->tab,
		$this->publication->alias,
		$this->version
	);

	$html .= \Components\Publications\Helpers\Html::sections( $this->sections, $this->cats, $this->tab, 'hide', 'main' );

	// Add footer notice
	if ($this->tab == 'about')
	{
		$html .= \Components\Publications\Helpers\Html::footer( $this->publication );
	}

	$html .= '</div><!-- / .subject -->'."\n";
	$html .= ' <div class="aside extracontent">'."\n";
}

// Show related content
$out = Event::trigger( 'publications.onPublicationSub', array($this->publication, $this->option, 1) );
if (count($out) > 0)
{
	foreach ($out as $ou)
	{
		if (isset($ou['html']))
		{
			$html .= $ou['html'];
		}
	}
}

// Show what's popular
if ($this->tab == 'about')
{
	$html .= \Hubzero\Module\Helper::renderModules('extracontent');
}
$html .= ' </div><!-- / .aside extracontent -->'."\n";
$html .= '</section><!-- / .main section -->'."\n";
$html .= '<div class="clear"></div>'."\n";

echo $html;