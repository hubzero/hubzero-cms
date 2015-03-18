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

// Get primary elements
$elements = $this->publication->_curationModel->getElements(1);

// Get attachment type model
$attModel = new PublicationsModelAttachments($this->database);

?>
<!--[if gte IE 9]>
  <style type="text/css">
    .gradient {
       filter: none;
    }
  </style>
<![endif]-->
<div class="launcher-image">
	<div class="imager" style="background-image:url('<?php echo JRoute::_('index.php?option=com_publications&id=' . $this->publication->id . '&v=' . $this->publication->version_number) . '/Image:master'; ?>');" > </div>
</div>
<section id="launcher" class="main section launcher grad-blue gradient">
	<div class="grid">
		<div class="col span6">
		  <div class="launcher-inside-wrap">
			<?php // Show published date and category
				echo \Components\Publications\Helpers\Html::showSubInfo( $this->publication, $this->option );
			?>
			<h3><?php echo \Hubzero\Utility\String::truncate(stripslashes($this->publication->title), 150); ?></h3>
			<?php
			// Display authors
			if ($this->publication->params->get('show_authors')) {
				if ($this->publication->_authors) {
					$html  = '<div id="authorslist">'."\n";
					$html .= \Components\Publications\Helpers\Html::showContributors(
						$this->publication->_authors,
						true,
						false
					)."\n";
					$html .= '</div>'."\n";
					echo $html;
				}
			}
			?>

			<?php
				// Display mini abstract
				if ($this->publication->abstract)
				{
					?>
					<p class="ataglance"><?php echo \Hubzero\Utility\String::truncate(stripslashes($this->publication->abstract), 250); ?></p>
			<?php } ?>
			</div>
		</div>
		<div class="col span4 launch-wrap">
			<?php
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
					echo $launcher;
				}
			?>
			<div class="version-info">
				<?php echo \Components\Publications\Helpers\Html::showVersionInfo(
					$this->publication,
					$this->version,
					$this->option,
					$this->config,
					$this->lastPubRelease
				);
				echo \Components\Publications\Helpers\Html::showLicense(
					$this->publication,
					$this->version,
					$this->option,
					$this->publication->_license,
					'play'
				) ?>
			</div>
		</div>
		<div class="col span2 omega">
			<div class="meta">
<?php

	if ($this->publication->state == 1 && $this->publication->main == 1)
	{
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
		     ->set('launcherLayout', true)
		     ->display();
	}
	 ?>
			</div>
		</div>
	</div>
</section>
<div class="launcher-notes">
<?php // Show status for authorized users
if ($this->contributable)
{
	echo \Components\Publications\Helpers\Html::showAccessMessage( $this->publication );
} ?>
</div>
