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

// Get primary elements
$elements = $this->publication->_curationModel->getElements(1);

$authorized = ($this->restricted && !$this->authorized) ? false : true;

// Get attachment type model
$attModel = new PublicationsModelAttachments($this->database);

$authorized = ($this->restricted && !$this->authorized) ? false : true;

$base_path = $this->config->get('webpath');
$path = $this->publication->_helpers->pubHelper->buildPath(
	$this->publication->id,
	$this->publication->version_id,
	$base_path,
	''
);

$masterImage = $path . DS . 'master.png';
//$masterImage = NULL;

// Build pub url
$route = $this->publication->project_provisioned == 1
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias=' . $this->publication->project_alias . '&active=publications';
$editurl = JRoute::_($route . '&pid=' . $this->publication->id) . '?version=' . $this->version;
?>
<!--[if gte IE 9]>
  <style type="text/css">
    .gradient {
       filter: none;
    }
  </style>
<![endif]-->
<div class="launcher-image">
	<div class="imager" <?php if (is_file(JPATH_ROOT . $masterImage)) { ?> style="background-image:url('<?php echo $masterImage; ?>');" <?php } ?> > </div>
</div>
<section id="launcher" class="main section launcher grad-blue gradient">
	<div class="grid">
		<div class="col span6">
		  <div class="launcher-inside-wrap">
			<?php // Show published date and category
				echo PublicationsHtml::showSubInfo( $this->publication, $this->option );
			?>
			<h3><?php echo \Hubzero\Utility\String::truncate(stripslashes($this->publication->title), 150); ?></h3>
			<?php
			// Display authors
			if ($this->params->get('show_authors')) {
				if ($this->publication->_authors) {
					$html  = '<div id="authorslist">'."\n";
					$html .= $this->publication->_helpers->pubHelper->showContributors(
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
						$authorized
					);
					echo $launcher;
				}
			?>
			<div class="version-info">
				<?php echo PublicationsHtml::showVersionInfo(
					$this->publication,
					$this->version,
					$this->option,
					$this->config,
					$this->lastPubRelease
				);
				echo PublicationsHtml::showLicense(
					$this->publication,
					$this->version,
					$this->option,
					$this->license,
					'play'
				) ?>
			</div>
		</div>
		<div class="col span2 omega">
			<div class="meta">
<?php

	if ($this->publication->state == 1)
	{
		echo PublicationsHtml::drawMetadata(
			$this->option,
			$this->params,
			$this->publication,
			$this->sections,
			$this->version,
			$this->lastPubRelease
		);
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
	echo PublicationsHtml::showAccessMessage(
		$this->publication,
		$this->option,
		$this->authorized,
		$this->restricted,
		$editurl
	);
} ?>
</div>