<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('import.css')
     ->js('import.js');

//citation params
$label    = $this->config->get('citation_label', 'number');
$rollover = $this->config->get('citation_rollover', 'no');

$citationsFormat = new \Components\Citations\Helpers\Format($this->database);
$template = $citationsFormat->getDefaultFormat();

//batch downloads
$batch_download = $this->config->get("citation_batch_download", 1);

//do we want to number li items
if ($label == 'none')
{
	$citations_label_class = ' no-label';
}
elseif ($label == 'type')
{
	$citations_label_class = ' type-label';
}
elseif ($label == 'both')
{
	$citations_label_class = ' both-label';
}
else
{
	$citations_label_class = ' both-label';
}

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';
?>
<div id="content-header-extra">
	<ul id="useroptions">
		<li>
			<a class="icon-browse browse btn" href="<?php echo Route::url($base); ?>">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_BACK'); ?>
			</a>
		</li>
	</ul>
</div>

<section id="import" class="section">
	<div class="section-inner">
		<?php
			foreach ($this->messages as $message)
			{
				echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
			}
		?>

		<ul id="steps">
			<li>
				<a href="<?php echo Route::url($base . '&action=import'); ?>" class="passed">
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1_NAME'); ?></span>
				</a>
			</li>
			<li>
				<a href="<?php echo Route::url($base . '&action=review'); ?>" class="passed">
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2_NAME'); ?></span>
				</a>
			</li>
			<li>
				<a href="<?php echo Route::url($base . '&action=saved'); ?>" class="active">
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME'); ?></span>
				</a>
			</li>
		</ul><!-- / #steps -->

		<?php if (count($this->citations) > 0) : ?>
			<?php
				$formatter = new \Components\Citations\Helpers\Format();
				$formatter->setTemplate($template);

				$counter = 1;
			?>

			<h3><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SUCCESS'); ?></h3>

			<table class="citations">
				<tbody>
					<?php foreach ($this->citations as $cite) : ?>
						<tr>
							<?php if ($label != "none") : ?>
								<td class="citation-label <?php echo $this->escape($citations_label_class); ?>">
									<?php
										$type = '';
										foreach ($this->types as $t)
										{
											if ($t['id'] == $cite->type)
											{
												$type = $t['type_title'];
											}
										}
										$type = ($type != '') ? $type : 'Generic';

										switch ($label)
										{
											case 'number':
												echo "<span class=\"number\">{$counter}.</span>";
												break;
											case 'type':
												echo "<span class=\"type\">{$type}</span>";
												break;
											case 'both':
												echo "<span class=\"number\">{$counter}.</span>";
												echo "<span class=\"type\">{$type}</span>";
												break;
										}
									?>
								</td>
							<?php endif; ?>
							<td class="citation-container">
								<?php echo $formatter->formatCitation($cite, $this->filters['search'], false, $this->config); ?>

								<?php if ($rollover == 'yes' && $cite->abstract != '') : ?>
									<div class="citation-notes">
										<p><?php echo nl2br($cite->abstract); ?></p>
									</div>
								<?php endif; ?>

								<div class="citation-details">
									<?php echo $formatter->citationDetails($cite, $this->database, $this->config, $this->openurl); ?>

									<?php if ($this->config->get('citation_show_badges', 'no') == 'yes') : ?>
										<?php echo \Components\Citations\Helpers\Format::citationBadges($cite, $this->database); ?>
									<?php endif; ?>

									<?php if ($this->config->get('citation_show_tags', 'no') == 'yes') : ?>
										<?php echo \Components\Citations\Helpers\Format::citationTags($cite, $this->database); ?>
									<?php endif; ?>
								</div>
							</td>
						</tr>
						<?php $counter++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</section><!-- / .section -->
