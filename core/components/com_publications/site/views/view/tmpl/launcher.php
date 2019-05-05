<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->css('jquery.fancybox.css', 'system')
	->js();

$this->css('
	.launcher-image .imager {
		background-image: url("' . Route::url('index.php?option=com_publications&id=' . $this->publication->id . '&v=' . $this->publication->version_number) . '/Image:master");
	}
');

// Get primary elements
$elements = $this->publication->_curationModel->getElements(1);

// Get attachment type model
$attModel = new \Components\Publications\Models\Attachments($this->database);

?>
<!--[if gte IE 9]>
  <style type="text/css">
    .gradient {
       filter: none;
    }
  </style>
<![endif]-->
<div class="launcher-image">
	<div class="imager"> </div>
</div>
<section id="launcher" class="main section launcher grad-blue gradient">
	<div class="grid">
		<div class="col span6">
			<div class="launcher-inside-wrap">
				<?php // Show published date and category
					echo \Components\Publications\Helpers\Html::showSubInfo($this->publication);
				?>
				<h3><?php echo \Hubzero\Utility\Str::truncate(stripslashes($this->publication->title), 150); ?></h3>
				<?php
				// Display authors
				if ($this->publication->params->get('show_authors'))
				{
					if ($this->publication->_authors)
					{
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
					<p class="ataglance"><?php echo \Hubzero\Utility\Str::truncate(stripslashes($this->publication->abstract), 250); ?></p>
					<?php
				}
				?>
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
					$this->publication
				);
				echo \Components\Publications\Helpers\Html::showLicense(
					$this->publication, 'play'
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
		echo \Components\Publications\Helpers\Html::showAccessMessage($this->publication);
	}
	?>
</div>
