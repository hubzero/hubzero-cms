<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

//citation params
$label    = $this->config->get('citation_label', 'number');
$rollover = $this->config->get('citation_rollover', 'no');
$rollover = ($rollover == 'yes') ? 1 : 0;

//batch downloads
$batch_download = $this->config->get("citation_batch_download", 1);

//Include COinS
$coins = $this->config->get("citation_coins", 1);

//do we want to number li items
if ($label == 'none') {
	$citations_label_class = 'no-label';
} elseif ($label == 'number') {
	$citations_label_class = 'number-label';
} elseif ($label == 'type') {
	$citations_label_class = 'type-label';
} elseif ($label == 'both') {
	$citations_label_class = 'both-label';
}

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<?php if ($this->allow_import == 1 || ($this->allow_import == 2 && $this->isAdmin)) : ?>
				<li>
					<a class="btn icon-add" href="<?php echo Route::url('index.php?option=com_citations&task=add'); ?>">
						<?php echo Lang::txt('COM_CITATIONS_SUBMIT_CITATION'); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($this->allow_bulk_import == 1 || ($this->allow_bulk_import == 2 && $this->isAdmin)) : ?>
				<li>
					<a class="btn icon-upload" href="<?php echo Route::url('index.php?option='.$this->option.'&task=import'); ?>">
						<?php echo Lang::txt('COM_CITATIONS_IMPORT_CITATION'); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</header>

<form action="<?php echo Route::url('index.php?option='.$this->option.'&task=browse'); ?>" id="citeform" method="get" class="<?php if ($batch_download) { echo " withBatchDownload"; } ?>">
	<section class="main section">
		<div class="section-inner hz-layout-with-aside">
			<div class="subject">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="Search" />
					<fieldset class="entry-search">
						<legend><?php echo Lang::txt('COM_CITATIONS_SEARCH_CITATIONS'); ?></legend>
						<label for="entry-search-field"><?php echo Lang::txt('COM_CITATIONS_BROWSE_SEARCH_HELP'); ?></label>
						<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- /.container .data-entry -->
				<div class="container">
					<nav class="entries-filters">
						<ul class="entries-menu filter-options">
							<?php
								$queryString = "";
								$exclude = array("filter");
								foreach ($this->filters as $k => $v)
								{
									if ($v != "" && !in_array($k, $exclude))
									{
										if (is_array($v))
										{
											foreach ($v as $k2 => $v2)
											{
												$queryString .= "&{$k}[{$k2}]={$v2}";
											}
										}
										else
										{
											$queryString .= "&{$k}={$v}";
										}
									}
								}
							?>
							<li><a <?php if ($this->filters['filter'] == '') { echo 'class="active"'; } ?> href="<?php echo Route::url('index.php?option=com_citations&task=browse'.$queryString.'&filter='); ?>"><?php echo Lang::txt('COM_CITATIONS_ALL'); ?></a></li>
							<li><a <?php if ($this->filters['filter'] == 'aff') { echo 'class="active"'; } ?> href="<?php echo Route::url('index.php?option=com_citations&task=browse'.$queryString.'&filter=aff'); ?>"><?php echo Lang::txt('COM_CITATIONS_AFFILIATED'); ?></a></li>
							<li><a <?php if ($this->filters['filter'] == 'nonaff') { echo 'class="active"'; } ?> href="<?php echo Route::url('index.php?option=com_citations&task=browse'.$queryString.'&filter=nonaff'); ?>"><?php echo Lang::txt('COM_CITATIONS_NONAFFILIATED'); ?></a></li>
						</ul>
					</nav>

					<?php if (count($this->citations) > 0) : ?>
						<?php

							// Fixes the counter so it starts counting at the current citation number instead of restarting on 1 at every page
							$counter = $this->filters['limitstart'] + 1;

							if ($counter == '')
							{
								$counter = 1;
							}

						?>
						<table class="citations entries">
							<thead>
								<tr>
									<?php if ($batch_download) : ?>
										<th class="batch">
											<input type="checkbox" class="checkall-download" />
										</th>
									<?php endif; ?>
									<th colspan="2"><?php echo Lang::txt('COM_CITATIONS'); ?></th>
								</tr>
								<?php if ($this->isAdmin) : ?>
									<tr class="hidden"></tr>
								<?php endif; ?>
							</thead>
							<tbody>
								<?php $x = 0; ?>
								<?php foreach ($this->citations as $cite) : ?>
									<tr>
										<?php $citeId = $cite->id; ?>
										<?php if ($batch_download) : ?>
											<td class="batch">
												<input type="checkbox" class="download-marker" name="download_marker[]" id="download_marker<?php echo $citeId; ?>" value="<?php echo $citeId; ?>" />
											</td>
										<?php endif; ?>
										<?php if ($label != "none") : ?>
											<td class="priority-3 citation-label <?php echo $citations_label_class; ?>">
												<label for="download_marker<?php echo $citeId; ?>">
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
															echo "<span class=\"number\">{$counter}.</span>";
															echo "<span class=\"type\">{$type}</span>";
															break;
													}
												?>
												</label>
											</td>
										<?php endif; ?>
										<td class="citation-container">
											<?php
												$formatted = $cite->formatted(array('format'=>$this->defaultFormat));
												if ($cite->doi)
												{
													$formatted = str_replace('doi:' . $cite->doi,
														'<a href="' . $cite->url . '" rel="external">'
														. 'doi:' . $cite->doi . '</a>', $formatted);
												}

												echo $formatted; ?>
											<?php
												//get this citations rollover param
												$params = new \Hubzero\Config\Registry($cite->params);
												$citation_rollover = $params->get('rollover', $rollover);
											?>
											<?php if ($citation_rollover && $cite->abstract != "") : ?>
												<div class="citation-notes">
													<?php
														$final = "";
														if ($cite->sponsors)
														{
															foreach ($cite->sponsors as $s)
															{
																$final .= '<a rel="external" href="' . $s->get('link') . '">' . $s->get('sponsor') . '</a>, ';
															}
														}
													?>
													<?php if ($final != '' && $this->config->get("citation_sponsors", "yes") == 'yes') : ?>
														<?php $final = substr($final, 0, -2); ?>
														<p class="sponsor"><?php echo Lang::txt('COM_CITATIONS_ABSTRACT_BY'); ?> <?php echo $final; ?></p>
													<?php endif; ?>
													<p><?php echo nl2br($cite->abstract); ?></p>
												</div>
											<?php endif; ?>
											<div class="citation-details">
												<?php
												$singleCitationView = $this->config->get('citation_single_view', 0);
												if (!$singleCitationView)
												{
													echo $cite->citationDetails($this->openurl);
												}
												?>
												<?php if ($this->config->get("citation_show_badges", "no") == "yes") : ?>
													<?php echo \Components\Citations\Helpers\Format::citationBadges($cite); ?>
												<?php endif; ?>

												<?php if ($this->config->get("citation_show_tags", "no") == "yes") : ?>
													<?php echo \Components\Citations\Helpers\Format::citationTags($cite); ?>
												<?php endif; ?>
											</div>
										</td>
										<?php if ($this->isAdmin === true) : ?>
											<td class="col-edit">
												<a class="icon-edit" href="<?php echo Route::url('index.php?option='.$this->option.'&task=edit&id=' . $citeId); ?>">
													<?php echo Lang::txt('JACTION_EDIT'); ?>
												</a>
											</td>
										<?php endif; ?>
									</tr>
									<?php $counter++; ?>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p class="warning"><?php echo Lang::txt('COM_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
					<?php endif; ?>
					<?php
						// Initiate paging
						$pageNav = $this->citations->pagination;
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
								case 'published':
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
						echo $pageNav->render();
					?>
					<div class="clearfix"></div>
				</div><!-- /.container -->
			</div><!-- /.subject -->
			<div class="aside">
			<?php if ($batch_download) : ?>
				<fieldset id="download-batch">
					<strong><?php echo Lang::txt('COM_CITATIONS_EXPORT_MULTIPLE'); ?></strong>
					<p><?php echo Lang::txt('COM_CITATIONS_EXPORT_MULTIPLE_DESC'); ?></p>

					<input type="submit" name="download" class="download" value="<?php echo Lang::txt('COM_CITATIONS_ENDNOTE'); ?>" />
					|
					<input type="submit" name="download" class="download" value="<?php echo Lang::txt('COM_CITATIONS_BIBTEX'); ?>" />
				</fieldset>
			<?php endif; ?>

			<fieldset>
				<label for="type">
					<?php echo Lang::txt('COM_CITATIONS_TYPE'); ?>
					<select name="type" id="type">
						<option value=""><?php echo Lang::txt('COM_CITATIONS_ALL'); ?></option>
						<?php foreach ($this->types as $t) : ?>
							<?php $sel = ($this->filters['type'] == $t['id']) ? "selected=\"selected\"" : ""; ?>
							<option <?php echo $sel; ?> value="<?php echo $t['id']; ?>"><?php echo $this->escape($t['type_title']); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label for="actags">
					<?php echo Lang::txt('COM_CITATIONS_TAGS'); ?>:
					<?php
						$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tag', 'actags', '', $this->escape($this->filters['tag']))));  // type, field name, field id, class, value
						if (count($tf) > 0) : ?>
							<?php echo $tf[0]; ?>
						<?php else: ?>
							<input type="text" name="tag" id="actags" value="<?php echo $this->escape($this->filters['tag']); ?>" />
						<?php endif; ?>
				</label>
				<label for="author">
					<?php echo Lang::txt('COM_CITATIONS_AUTHORED_BY'); ?>
					<input type="text" name="author" id="author" value="<?php echo $this->escape($this->filters['author']); ?>" />
				</label>
				<label for="publishedin">
					<?php echo Lang::txt('COM_CITATIONS_PUBLISHED_IN'); ?>
					<input type="text" name="publishedin" id="publishedin" value="<?php echo $this->escape($this->filters['publishedin']); ?>" />
				</label>
				<div class="grid">
					<div class="col span6">
						<label for="year_start">
							<?php echo Lang::txt('COM_CITATIONS_YEAR'); ?> (from)
							<input type="text" name="year_start" id="year_start" value="<?php echo $this->escape($this->filters['year_start']); ?>" />
						</label>
					</div>
					<div class="col span6 omega">
						<label for="year_end">
							<?php echo Lang::txt('COM_CITATIONS_YEAR'); ?> (to)
							<input type="text" name="year_end" id="year_end" value="<?php echo $this->escape($this->filters['year_end']); ?>" />
						</label>
					</div>
				</div>
				<?php if ($this->isAdmin) { ?>
					<fieldset>
						<label for="startuploaddate">
							<?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN'); ?>
							<input type="text" name="startuploaddate" id="startuploaddate" value="<?php echo str_replace(' 00:00:00', '', $this->escape($this->filters['startuploaddate'])); ?>" />
							<div class="hint"><?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN_HINT'); ?></div>
						</label>
						<label for="enduploaddate">
							<?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN_AND'); ?><br/>
							<input type="text" name="enduploaddate" id="enduploaddate" value="<?php echo str_replace(' 00:00:00', '', $this->escape($this->filters['enduploaddate'])); ?>" />
							<div class="hint"><?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN_HINT'); ?></div>
						</label>
					</fieldset>
				<?php } ?>
				<label for="sort">
					<?php echo Lang::txt('COM_CITATIONS_SORT_BY'); ?>
					<select name="sort" id="sort">
						<?php foreach ($this->sorts as $k => $v) : ?>
							<?php $sel = ($k == $this->filters['sort']) ? "selected" : "";
							if (($this->isAdmin !== true) && ($v == "Date uploaded"))
							{
								// Do nothing
							}
							else
							{
							?>
 								<option <?php echo $sel; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
							<?php } ?>
						<?php endforeach; ?>
					</select>
				</label>
				<fieldset>
					<legend><?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE'); ?></legend>
					<label for="reftype_research">
						<input class="option" type="checkbox" name="reftype[research]" id="reftype_research" value="1"<?php if (isset($this->filters['reftype']['research'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_RESEARCH'); ?>
					</label>
					<label for="reftype_education">
						<input class="option" type="checkbox" name="reftype[education]" id="reftype_education" value="1"<?php if (isset($this->filters['reftype']['education'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_EDUCATION'); ?>
					</label>
					<label for="reftype_eduresearch">
						<input class="option" type="checkbox" name="reftype[eduresearch]" id="reftype_eduresearch" value="1"<?php if (isset($this->filters['reftype']['eduresearch'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_EDUCATIONRESEARCH'); ?>
					</label>
					<label for="reftype_cyberinfrastructure">
						<input class="option" type="checkbox" name="reftype[cyberinfrastructure]" id="reftype_cyberinfrastructure" value="1"<?php if (isset($this->filters['reftype']['cyberinfrastructure'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_CYBERINFRASTRUCTURE'); ?>
					</label>
				</fieldset>
				<fieldset>
					<legend><?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY'); ?></legend>
					<label for="geo_us">
						<input class="option" type="checkbox" name="geo[us]" id="geo_us" value="1"<?php if (isset($this->filters['geo']['us'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_US'); ?>
					</label>
					<label for="geo_na">
						<input class="option" type="checkbox" name="geo[na]" id="geo_na" value="1"<?php if (isset($this->filters['geo']['na'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_NORTH_AMERICA'); ?>
					</label>
					<label for="geo_eu">
						<input class="option" type="checkbox" name="geo[eu]" id="geo_eu" value="1"<?php if (isset($this->filters['geo']['eu'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_EUROPE'); ?>
					</label>
					<label for="geo_as">
						<input class="option" type="checkbox" name="geo[as]" id="geo_as" value="1"<?php if (isset($this->filters['geo']['as'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_ASIA'); ?>
					</label>
				</fieldset>
				<fieldset>
					<legend><?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION'); ?></legend>
					<label for="aff_university">
						<input class="option" type="checkbox" name="aff[university]" id="aff_university" value="1"<?php if (isset($this->filters['aff']['university'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION_UNIVERSITY'); ?>
					</label>
					<label for="aff_industry">
						<input class="option" type="checkbox" name="aff[industry]" id="aff_industry" value="1"<?php if (isset($this->filters['aff']['industry'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION_INDUSTRY'); ?>
					</label>
					<label for="aff_government">
						<input class="option" type="checkbox" name="aff[government]" id="aff_government" value="1"<?php if (isset($this->filters['aff']['government'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION_GOVERNMENT'); ?>
					</label>
				</fieldset>

				<input type="hidden" name="idlist" value="<?php echo $this->escape($this->filters['idlist']); ?>"/>

				<p class="submit">
					<input type="submit" value="<?php echo Lang::txt('COM_CITATIONS_FILTER'); ?>" />
				</p>
			</fieldset>
		</div><!-- /.aside -->
		</div>
	</section>
</form>
