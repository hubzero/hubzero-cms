<?php
/**
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license	http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

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
{
	?>
	<div id="content-header">
	<section class="main section upperpane">
		<div class="subject">
			<div class="grid overviewcontainer">
				<div class="col span8">
					<?php echo str_replace(' id="content-header"', '', \Components\Publications\Helpers\Html::title($this->publication)); ?>

					<?php if ($this->publication->params->get('show_authors') && $this->publication->_authors) { ?>
						<div id="authorslist">
							<?php echo \Components\Publications\Helpers\Html::showContributors($this->publication->_authors, true, false, false, false, $this->publication->params->get('format_authors', 0)); ?>
						</div>
					<?php }	?>

					<p class="ataglance"><?php echo $this->publication->description ? \Hubzero\Utility\String::truncate(stripslashes($this->publication->description), 250) : ''; ?></p>

					<?php echo \Components\Publications\Helpers\Html::showSubInfo($this->publication); // Show published date and category ?>
				</div><!-- / .overviewcontainer -->
				<div class="col span4 omega launcharea">
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
					$html .=  $this->tab != 'play' ? \Components\Publications\Helpers\Html::drawSupportingItems($this->publication) : '';

					// Show version information
					$html .=  $this->tab != 'play' ? \Components\Publications\Helpers\Html::showVersionInfo($this->publication) : '';

					// Show license information
					$html .= $this->tab != 'play' && $this->publication->license() && $this->publication->license()->name != 'standard'
							? \Components\Publications\Helpers\Html::showLicense($this->publication, 'play') : '';

					echo $html;
					?>
				</div><!-- / .aside launcharea -->
			</div>

			<?php
			// Show status for authorized users
			if ($this->contributable)
			{
				echo \Components\Publications\Helpers\Html::showAccessMessage($this->publication);
			}
			?>
		</div><!-- / .subject -->
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
	</section><!-- / .main section -->
</div>
	<?php
}

$html = '';

// Part below
if ($this->publication->access('view-all'))
{
	$html .= '<div class="clear sep"></div>'."\n";

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

	$html .= \Components\Publications\Helpers\Html::sections($this->sections, $this->cats, $this->tab, 'hide', 'main');

	// Add footer notice
	if ($this->tab == 'about')
	{
		$html .= \Components\Publications\Helpers\Html::footer($this->publication);
	}

	$html .= '</div><!-- / .subject -->'."\n";
	$html .= ' <div class="aside extracontent">'."\n";
}

// Show related content
$out = Event::trigger('publications.onPublicationSub', array($this->publication, $this->option, 1));
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
//$html .= '<div class="clear"></div>'."\n";

echo $html;
