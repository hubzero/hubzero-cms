<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('import.css')
     ->js('import.js');

//database object
$database = App::get('db');

//declare vars
$citations_require_attention    = $this->citations_require_attention;
$citations_require_no_attention = $this->citations_require_no_attention;

//dont show array
$no_show = array('errors', 'duplicate', 'bdsk-file-1');

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';
?>
<section id="import" class="section">
	<div class="section-inner">
		<?php if ($this->messages): ?>
		<?php
			foreach ($this->messages as $message)
			{
				echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
			}
		?>
		<?php endif; ?>

		<ul id="steps">
			<li><a href="<?php echo Route::url($base . '&action=import'); ?>" class="passed"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1_NAME'); ?></span></a></li>
			<li><a href="<?php echo Route::url($base . '&action=review'); ?>" class="active"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME'); ?></span></a></li>
		</ul><!-- / #steps -->

		<form method="post" id="hubForm" class="full" action="<?php echo Route::url($base . '&action=process'); ?>">
			<?php if ($citations_require_attention) : ?>
				<table class="upload-list require-action">
					<thead>
						<tr>
							<!--<th></th>-->
							<th><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_REQUIRE_ATTENTION', count($citations_require_attention)); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $counter = 0; ?>
						<?php foreach ($citations_require_attention as $c) : ?>
							<?php
								//load the duplicate citation
								$cc = $c['duplicate'];

								$type_title = $cc->relatedType->get('type_title');
								$tags = implode(', ', \Components\Citations\Helpers\Format::citationTags($c['duplicate'], false));
								$badges = implode(', ', \Components\Citations\Helpers\Format::citationBadges($c['duplicate'], false));
							?>
							<tr>
								<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
								<td>
									<span class="citation-title"><u><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_DUPLICATE'); ?></u>: <?php echo html_entity_decode($c['title']); ?></span>
									<span class="click-more"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SHOW_CITATION_DETAILS'); ?></span>
	<?php if (1) { ?>
									<table class="citation-details hide">
										<thead>
											<tr>
												<th><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_DETAILS'); ?></th>
												<th class="options">
													<label for="citation_action_attention-<?php echo $counter; ?>-replace">
														<input
															type="radio"
															class="citation_require_attention_option"
															name="citation_action_attention[<?php echo $counter; ?>]"
															id="citation_action_attention-<?php echo $counter; ?>-replace"
															value="overwrite"
															checked="checked" /> <?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_REPLACE'); ?>
													</label>
													<label for="citation_action_attention-<?php echo $counter; ?>-keep">
														<input
															type="radio"
															class="citation_require_attention_option"
															name="citation_action_attention[<?php echo $counter; ?>]"
															id="citation_action_attention-<?php echo $counter; ?>-keep"
															value="both" /> <?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_KEEP'); ?>
													</label>
													<label for="citation_action_attention-<?php echo $counter; ?>-nothing">
														<input
															type="radio"
															class="citation_require_attention_option"
															name="citation_action_attention[<?php echo $counter; ?>]"
															id="citation_action_attention-<?php echo $counter; ?>-nothing"
															value="discard" /> <?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_NOTHING'); ?>
													</label>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$recordAttributes = $cc->getAttributes();
											$changedKeys = array();
											foreach ($c as $attribute => $value)
											{
												if (!empty($recordAttributes[$attribute]) || !empty($value))
												{
													$changedKeys[] = $attribute;
												}
											}
											?>
											<?php foreach ($changedKeys as $k) : ?>
												<?php if (!in_array($k, $no_show)) : ?>
													<tr>
														<td class="key">
															<?php echo str_replace("_", " ", $k); ?>
														</td>
														<td>
															<table class="citation-differences">
																<tr>
																	<td><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_JUST_UPLOADED'); ?>:</td>
																	<td>
																		<span class="new insert"><?php echo html_entity_decode(nl2br($c[$k])); ?></span>
																	</td>
																</tr>
																<tr>
																	<td><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_ON_FILE'); ?>:</td>
																	<td>
																		<span class="old delete">
																			<?php
																				switch ($k)
																				{
																					case 'type':
																						echo $type_title;
																						break;
																					case 'tags':
																						echo $tags;
																						break;
																					case 'badges':
																						echo $badges;
																						break;
																					default:
																						if (in_array($k, array_keys($cc->getAttributes())))
																						{
																							echo html_entity_decode(nl2br($cc->$k));
																						}
																					break;
																				}
																			?>
																		</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												<?php endif; ?>
											<?php endforeach; ?>
										</tbody>
									</table>
	<?php
	}
	?>
								</td>
							</tr>
							<?php $counter++; ?>
						<?php endforeach; ?>
					<tbody>
				</table>
			<?php endif; ?>

			<!-- /////////////////////////////////////// -->

			<?php if ($citations_require_no_attention) : ?>
				<table class="upload-list no-action">
					<thead>
						<tr>
							<th><input type="checkbox" class="checkall" name="select-all-no-attention" checked="checked" /></th>
							<th><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION', count($citations_require_no_attention)); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $counter = 0; ?>
							<?php foreach ($citations_require_no_attention as $c) : ?>
							<tr>
								<td><input type="checkbox" class="check-single" name="citation_action_no_attention[<?php echo $counter++; ?>]" checked="checked" value="1" /></td>
								<td>
									<span class="citation-title">
										<?php
											if (array_key_exists("title", $c))
											{
												echo html_entity_decode($c['title']);
											}
											else
											{
												echo "NO TITLE FOUND";
											}
										?>
									</span>
									<span class="click-more"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SHOW_CITATION_DETAILS'); ?></span>
									<table class="citation-details hide">
										<thead>
											<tr>
												<th colspan="2"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_DETAILS'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach (array_keys($c) as $k) : ?>
												<?php if (!in_array($k, $no_show)) : ?>
													<tr>
														<td class="key"><?php echo str_replace("_", " ", $k); ?></td>
														<td><?php echo html_entity_decode(nl2br($c[$k])); ?></td>
													</tr>
												<?php endif; ?>
											<?php endforeach; ?>
										</tbody>
									</table>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<p class="submit">
				<input type="submit" class="btn btn-success" id="review-input" name="submit" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SUBMIT_IMPORTED'); ?>" />

				<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			</p>

			<?php echo Html::input('token'); ?>
			<input type="hidden" name="option" value="com_groups" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="citations" />
			<input type="hidden" name="action" value="process" />
		</form>
	</div>
</section><!-- / .section -->
