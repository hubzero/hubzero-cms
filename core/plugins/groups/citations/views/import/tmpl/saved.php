<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
