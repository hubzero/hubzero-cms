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
 * @author    Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js();

$base = $this->member->getLink() . '&active=citations';

//citation params
$label    = $this->config->get("citation_label", "number");
$rollover = $this->config->get("citation_rollover", "no");
$rollover = ($rollover == "yes") ? 1 : 0;

// citation format
$citationsFormat = new \Components\Citations\Tables\Format($this->database);
$template = ($citationsFormat->getDefaultFormat()) ? $citationsFormat->getDefaultFormat()->format : null;

//batch downloads
$batch_download = $this->config->get("citation_batch_download", 1);

//Include COinS
$coins = $this->config->get("citation_coins", 1);

//do we want to number li items
if ($label == "none")
{
	$citations_label_class = "no-label";
}
elseif ($label == "number")
{
	$citations_label_class = "number-label";
}
elseif ($label == "type")
{
	$citations_label_class = "type-label";
}
elseif ($label == "both")
{
	$citations_label_class = "both-label";
}

if (isset($this->messages))
{
	foreach ($this->messages as $message)
	{
		echo '<p class="' . $message['type'] . '">' . $message['message'] . '</p>';
	}
}

$juser = JFactory::getUser();
?>
<div id="content-header-extra">
	<?php if ($this->isAdmin) : ?>
		<a class="btn icon-add" href="<?php echo Route::url($base . '&action=add'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
	<?php endif; ?>
</div>

<div class="frm" id="browsebox">

<?php if ($juser->get('id') == $this->member->get('uidNumber') && !$this->grand_total) { ?>

	<div class="introduction">
		<div class="introduction-message">
			<p><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_EMPTY'); ?></p>
		</div>
		<div class="introduction-questions">
			<p><strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS_EXPLANATION'); ?></p>

			<p><strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_HOW_TO_START'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_HOW_TO_START_EXPLANATION'); ?></p>
		</div>
	</div><!-- / .introduction -->

<?php } else { ?>

	<section class="main section">
		<form action="<?php echo Route::url(JURI::current()); ?>" id="citeform" method="get" class="section-inner <?php if ($batch_download) { echo " withBatchDownload"; } ?>">

				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="Search" />
					<fieldset class="entry-search">
						<legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SEARCH_CITATIONS'); ?></legend>
						<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- /.container .data-entry -->

				<div class="container">
					<ul class="entries-menu sort-options">
						<?php foreach ($this->sorts as $k => $v) : ?>
						<li>
							<?php $sel = ($k == $this->filters['sort']) ? 'class="active"' : ''; ?>
							<a <?php echo $sel; ?> href="<?php echo Route::url($base . '&sort=' . $k . ($this->filters['type'] ? '&type=' . $this->filters['type'] : '')); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SORT_BY', $v); ?>">&darr; <?php echo $v; ?></a>
						</li>
						<?php endforeach; ?>
					</ul>
					<ul class="entries-menu filter-options">
						<li>
							<label for="filter-type">
								<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_TYPE'); ?>
								<select name="type" id="filter-type">
									<option value=""><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_ALL'); ?></option>
									<?php foreach ($this->types as $t) : ?>
										<?php $sel = ($this->filters['type'] == $t['id']) ? 'selected="selected"' : ''; ?>
										<option <?php echo $sel; ?> value="<?php echo $t['id']; ?>"><?php echo $t['type_title']; ?></option>
									<?php endforeach; ?>
								</select>
								<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_FILTER'); ?>" />
							</label>
						</li>
					</ul>

					<input type="hidden" name="idlist" value="<?php echo $this->filters['idlist']; ?>"/>
					<input type="hidden" name="referer" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />

					<?php if (count($this->citations) > 0) : ?>
						<?php
							$formatter = new \Components\Citations\Helpers\Format();
							$formatter->setTemplate($template);

							// Fixes the counter so it starts counting at the current citation number instead of restarting on 1 at every page
							$counter = $this->filters['start'] + 1;
							if ($counter == '')
							{
								$counter = 1;
							}
						?>
						<table class="citations entries">
							<caption><?php echo Lang::txt('PLG_MEMBERS_CITATIONS'); ?></caption>
							<?php if ($batch_download) : ?>
							<thead>
								<tr>
									<th class="batch">
										<input type="checkbox" class="checkall-download" />
									</th>
									<th colspan="3">
										<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_EXPORT_MULTIPLE_DESC'); ?>
									</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="4">
										<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_EXPORT_AS'); ?>
										<input type="submit" name="download" class="download-endnote" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_ENDNOTE'); ?>" /> |
										<input type="submit" name="download" class="download-bibtex" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_BIBTEX'); ?>" />
									</td>
								</tr>
							</tfoot>
							<?php endif; ?>
							<tbody>
								<?php $x = 0; ?>
								<?php foreach ($this->citations as $cite) : ?>
									<tr>
										<?php if ($batch_download) : ?>
											<td class="batch">
												<input type="checkbox" class="download-marker" name="download_marker[]" value="<?php echo $cite->id; ?>" />
											</td>
										<?php endif; ?>

										<?php if ($label != "none") : ?>
											<td class="citation-label <?php echo $citations_label_class; ?>">
												<?php
													$type = "";
													foreach ($this->types as $t) {
														if ($t['id'] == $cite->type) {
															$type = $t['type_title'];
														}
													}
													$type = ($type != "") ? $type : "Generic";

													switch ($label)
													{
														case "number":
															echo "<span class=\"number\">{$counter}.</span>";
															break;
														case "type":
															echo "<span class=\"type\">{$type}</span>";
															break;
														case "both":
															echo "<span class=\"number\">{$counter}. </span>";
															echo "<span class=\"type\">{$type}</span>";
															break;
													}
												?>
											</td>
										<?php endif; ?>
										<td class="citation-container">
											<?php if (isset($cite->custom3)) : ?>
												<div class="identifier"><?php echo $cite->custom3; ?></div>
											<?php endif; ?>
											<?php
												$formatted = $cite->formatted
													? $cite->formatted
													: $formatter->formatCitation($cite,
														$this->filters['search'], $coins, $this->config);

												if ($cite->doi)
												{
													$formatted = str_replace('doi:' . $cite->doi,
														'<a href="' . $cite->url . '" rel="external">'
														. 'doi:' . $cite->doi . '</a>', $formatted);
												}

												echo $formatted; ?>
											<?php
												//get this citations rollover param
												$params = new JParameter($cite->params);
												$citation_rollover = $params->get('rollover', $rollover);
											?>
											<?php if ($citation_rollover && $cite->abstract != "") : ?>
												<div class="citation-notes">
													<?php
														$cs = new \Components\Citations\Tables\Sponsor($this->database);
														$sponsors = $cs->getCitationSponsor($cite->id);
														$final = "";
														if ($sponsors)
														{
															foreach ($sponsors as $s)
															{
																$sp = $cs->getSponsor($s);
																if ($sp)
																{
																	$final .= '<a rel="external" href="'.$sp[0]['link'].'">'.$sp[0]['sponsor'].'</a>, ';
																}
															}
														}
													?>
													<?php if ($final != '' && $this->config->get("citation_sponsors", "yes") == 'yes') : ?>
														<?php $final = substr($final, 0, -2); ?>
														<p class="sponsor"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_ABSTRACT_BY'); ?> <?php echo $final; ?></p>
													<?php endif; ?>
													<p><?php echo nl2br($cite->abstract); ?></p>
												</div>
											<?php endif; ?>

											<?php
												$singleCitationView = $this->config->get('citation_single_view', 0);
												if (!$singleCitationView)
												{
													echo $formatter->citationDetails($cite, $this->database, $this->config, $this->openurl, true);
												}
											?>
											<?php if ($this->config->get("citation_show_badges","no") == "yes") : ?>
												<?php echo \Components\Citations\Helpers\Format::citationBadges($cite, $this->database); ?>
											<?php endif; ?>

											<?php if ($this->config->get("citation_show_tags","no") == "yes") : ?>
												<?php echo \Components\Citations\Helpers\Format::citationTags($cite, $this->database); ?>
											<?php endif; ?>
										</td>
										<?php if ($this->isAdmin === true) : ?>
											<td class="col-options">
												<a class="delete icon-delete" data-confirm="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($base . '&action=delete&citation=' . $cite->id); ?>">
													<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_EDIT'); ?>
												</a>
												<a class="edit icon-edit" href="<?php echo Route::url($base . '&action=edit&citation=' . $cite->id); ?>">
													<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_EDIT'); ?>
												</a>
											</td>
										<?php endif; ?>
									</tr>
									<?php $counter++; ?>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<table class="citations entries">
							<caption><?php echo Lang::txt('PLG_MEMBERS_CITATIONS'); ?></caption>
							<tbody>
								<tr>
									<td>
										<p class="warning"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
									</td>
								</tr>
							</tbody>
						</table>
					<?php endif; ?>
					<?php
						// Initiate paging
						jimport('joomla.html.pagination');
						$pageNav = new JPagination(
							$this->total,
							$this->filters['start'],
							$this->filters['limit']
						);
						$pageNav->setAdditionalUrlParam('task', 'browse');
						foreach ($this->filters as $key => $value)
						{
							switch ($key)
							{
								case 'limit':
								case 'idlist';
								case 'start':
								break;

								case 'reftype':
								case 'aff':
								case 'geo':
									foreach ($value as $k => $v)
									{
										$pageNav->setAdditionalUrlParam($key . '[' . $k . ']', $v);
									}
								break;

								default:
									$pageNav->setAdditionalUrlParam($key, $value);
								break;
							}
						}
						echo $pageNav->getListFooter();
					?>
					<div class="clearfix"></div>
				</div><!-- /.container -->
		</form>
	</section>

<?php } ?>

</div>