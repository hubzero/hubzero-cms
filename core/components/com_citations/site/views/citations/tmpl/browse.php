<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

//citation params
$label    = $this->config->get('citation_label', 'number');
$rollover = $this->config->get('citation_rollover', 'no');
$rollover = ($rollover == 'yes') ? 1 : 0;
$citationsFormat = new \Components\Citations\Tables\Format($this->database);
$template = ($citationsFormat->getDefaultFormat()) ? $citationsFormat->getDefaultFormat()->format : null;

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
						<thead>
							<tr>
								<?php if ($batch_download) : ?>
									<th class="batch">
										<input type="checkbox" class="checkall-download" />
									</th>
								<?php endif; ?>
								<th colspan="3"><?php echo Lang::txt('COM_CITATIONS'); ?></th>
							</tr>
							<?php if ($this->isAdmin) : ?>
								<tr class="hidden"></tr>
							<?php endif; ?>
						</thead>
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
										<td class="priority-3 citation-label <?php echo $citations_label_class; ?>">
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
										</td>
									<?php endif; ?>
									<td class="citation-container">
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
											$params = new \Hubzero\Config\Registry($cite->params);
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
												echo $formatter->citationDetails($cite, $this->database, $this->config, $this->openurl);
											}
											?>
											<?php if ($this->config->get("citation_show_badges","no") == "yes") : ?>
												<?php echo \Components\Citations\Helpers\Format::citationBadges($cite, $this->database); ?>
											<?php endif; ?>

											<?php if ($this->config->get("citation_show_tags","no") == "yes") : ?>
												<?php echo \Components\Citations\Helpers\Format::citationTags($cite, $this->database); ?>
											<?php endif; ?>
										</div>
									</td>
									<?php if ($this->isAdmin === true) : ?>
										<td class="col-edit">
											<a class="icon-edit" href="<?php echo Route::url('index.php?option='.$this->option.'&task=edit&id='.$cite->id); ?>">
												<?php echo Lang::txt('COM_CITATIONS_EDIT'); ?>
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
					$pageNav = $this->pagination(
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

					<input type="submit" name="download" class="download-endnote" value="<?php echo Lang::txt('COM_CITATIONS_ENDNOTE'); ?>" />
					|
					<input type="submit" name="download" class="download-bibtex" value="<?php echo Lang::txt('COM_CITATIONS_BIBTEX'); ?>" />
				</fieldset>
			<?php endif; ?>

			<fieldset>
				<label>
					<?php echo Lang::txt('COM_CITATIONS_TYPE'); ?>
					<select name="type" id="type">
						<option value=""><?php echo Lang::txt('COM_CITATIONS_ALL'); ?></option>
						<?php foreach ($this->types as $t) : ?>
							<?php $sel = ($this->filters['type'] == $t['id']) ? "selected=\"selected\"" : ""; ?>
							<option <?php echo $sel; ?> value="<?php echo $t['id']; ?>"><?php echo $t['type_title']; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<?php echo Lang::txt('COM_CITATIONS_TAGS'); ?>:
					<?php
						$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tag', 'actags', '', $this->filters['tag'])));  // type, field name, field id, class, value
						if (count($tf) > 0) : ?>
							<?php echo $tf[0]; ?>
						<?php else: ?>
							<input type="text" name="tag" value="<?php echo $this->filters['tag']; ?>" />
						<?php endif; ?>
				</label>
				<label>
					<?php echo Lang::txt('COM_CITATIONS_AUTHORED_BY'); ?>
					<input type="text" name="author" value="<?php echo $this->filters['author']; ?>" />
				</label>
				<label>
					<?php echo Lang::txt('COM_CITATIONS_PUBLISHED_IN'); ?>
					<input type="text" name="publishedin" value="<?php echo $this->filters['publishedin']; ?>" />
				</label>
				<label for="year_start">
					<?php echo Lang::txt('COM_CITATIONS_YEAR'); ?><br />
					<input type="text" name="year_start" class="half" value="<?php echo $this->filters['year_start']; ?>" />
					to
					<input type="text" name="year_end" class="half" value="<?php echo $this->filters['year_end']; ?>" />
				</label>
				<?php if ($this->isAdmin) { ?>
					<fieldset>
						<label>
							<?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN'); ?>
							<input type="text" name="startuploaddate" value="<?php echo str_replace(' 00:00:00', '', $this->filters['startuploaddate']); ?>" />
							<div class="hint"><?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN_HINT'); ?></div>
						</label>
						<label>
							<?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN_AND'); ?><br/>
							<input type="text" name="enduploaddate" value="<?php echo str_replace(' 00:00:00', '', $this->filters['enduploaddate']); ?>" />
							<div class="hint"><?php echo Lang::txt('COM_CITATIONS_UPLOADED_BETWEEN_HINT'); ?></div>
						</label>
					</fieldset>
				<?php } ?>
				<label>
					<?php echo Lang::txt('COM_CITATIONS_SORT_BY'); ?>
					<select name="sort" id="sort" class="">
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
					<label>
						<input class="option" type="checkbox" name="reftype[research]" value="1"<?php if (isset($this->filters['reftype']['research'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_RESEARCH'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="reftype[education]" value="1"<?php if (isset($this->filters['reftype']['education'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_EDUCATION'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="reftype[eduresearch]" value="1"<?php if (isset($this->filters['reftype']['eduresearch'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_EDUCATIONRESEARCH'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="reftype[cyberinfrastructure]" value="1"<?php if (isset($this->filters['reftype']['cyberinfrastructure'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_REFERENCE_TYPE_CYBERINFRASTRUCTURE'); ?>
					</label>
				</fieldset>
				<fieldset>
					<legend><?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY'); ?></legend>
					<label>
						<input class="option" type="checkbox" name="geo[us]" value="1"<?php if (isset($this->filters['geo']['us'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_US'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="geo[na]" value="1"<?php if (isset($this->filters['geo']['na'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_NORTH_AMERICA'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="geo[eu]" value="1"<?php if (isset($this->filters['geo']['eu'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_EUROPE'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="geo[as]" value="1"<?php if (isset($this->filters['geo']['as'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_GEOGRAPHY_ASIA'); ?>
					</label>
				</fieldset>
				<fieldset>
					<legend><?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION'); ?></legend>
					<label>
						<input class="option" type="checkbox" name="aff[university]" value="1"<?php if (isset($this->filters['aff']['university'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION_UNIVERSITY'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="aff[industry]" value="1"<?php if (isset($this->filters['aff']['industry'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION_INDUSTRY'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="aff[government]" value="1"<?php if (isset($this->filters['aff']['government'])) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_CITATIONS_AUTHOR_AFFILIATION_GOVERNMENT'); ?>
					</label>
				</fieldset>

				<input type="hidden" name="idlist" value="<?php echo $this->filters['idlist']; ?>"/>
				<input type="hidden" name="referer" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />

				<p class="submit">
					<input type="submit" value="<?php echo Lang::txt('COM_CITATIONS_FILTER'); ?>" />
				</p>
			</fieldset>
		</div><!-- /.aside -->
	</section>
</form>
