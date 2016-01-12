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
 * @author	Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('citations.css')
	 ->js();

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';

if (isset($this->messages))
{
	foreach ($this->messages as $message)
	{
		echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
	}
}
?>

<div id="content-header-extra"><!-- Citation management buttons -->
	<?php if ($this->isManager) : ?>
		<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
			<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SET_FORMAT'); ?>
		</a>
	<?php endif; ?>
</div><!-- / Citations management buttons -->

<div class="frm" id="browsebox"><!-- .frm #browsebox -->
	<form action="<?php echo Route::url(Request::current()); ?>" id="citeform" method="GET" class="withBatchDownload">
		<section class="main section"> <!-- .main .section -->
			<div class="subject">
				<div class="container data-entry"> <!-- citation search box -->
					<!-- @TODO replace with hubgraph -->
					<input class="entry-search-submit" type="submit" value="Search" /> <!-- search button -->
					<fieldset class="entry-search"> <!-- text box container -->
						<legend><?php echo Lang::txt('PLG_GROUPS_CITATIONS_SEARCH_CITATIONS'); ?></legend>
						<input type="text" name="filters[search]" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- /.container .data-entry -->

				<div class="container"> <!-- .container for citation type (aff, nonaff, all) -->
					<?php if ($this->config->get('display') != "group"): ?>
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
							<li><a <?php if ($this->filters['filter'] == '' || $this->filters['filter'] == 'all') { echo 'class="active"'; } ?> href="<?php echo Route::url($base . '&action=browse'.$queryString.'&filters[filter]=all'); ?>"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_ALL'); ?></a></li>
								<li><a <?php if ($this->filters['filter'] == 'member') { echo 'class="active"'; } ?> href="<?php echo Route::url($base . '&action=browse'.$queryString.'&filters[filter]=member'); ?>"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_MEMBERCONTRIBUTED'); ?></a></li>
						</ul>
					</nav>
					<?php endif; ?>
				<?php if ($this->citations->count() > 0) : ?>
					<table class="citations entries">
						<thead>
							<tr>
								<th class="batch">
									<input type="checkbox" class="checkall-download" />
								</th>
								<th colspan="6"><?php echo Lang::txt('PLG_GROUPS_CITATIONS'); ?></th>
							</tr>
							<?php if ($this->isManager) : ?>
								<tr class="hidden">
									<div class="admin">
										<a class="btn icon-window-publish bulk" data-link="<?php echo Route::url($base. '&action=publish&bulk=true'); ?>">
											<?php echo Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH_SELECTED'); ?>
										</a>
										<a class="btn icon-delete bulk" data-protected="true" data-link="<?php echo Route::url($base. '&action=delete&bulk=true'); ?>">
											<?php echo Lang::txt('PLG_GROUPS_CITATIONS_DELETE_SELECTED'); ?>
										</a>
										</td>
									</div>
								</tr>
							<?php endif; ?>
						</thead>
						<tbody>
							<?php $x = 0; ?>
							<?php foreach ($this->citations as $cite) : ?>
								<?php if (!$this->isManager && $cite->published == $cite::STATE_UNPUBLISHED) { continue; } ?> 
								<tr class="citation-row <?php echo ($cite->published == $cite::STATE_UNPUBLISHED ? 'unpublished' : ''); ?>">
									<td class="batch">
										<input type="checkbox" class="download-marker" name="download_marker[]" value="<?php echo $cite->id; ?>" />
									</td>
									<?php if ($this->label != "none") : ?>
										<td class="citation-label <?php echo $this->citations_label_class; ?>">
											<?php
												/**
												 * @TODO replace with Relational
												 **/
												$type = "";
												foreach ($this->types as $t) {
													if ($t->id == $cite->type) {
														$type = $t->type_title;
													}
												}
												$type = ($type != "") ? $type : "Generic";

												switch ($this->label)
												{
													case "number":
														echo "<span class=\"number\">{$cite->id}.</span>";
														break;
													case "type":
														echo "<span class=\"type\">{$type}</span>";
														break;
													case "both":
														echo "<span class=\"number\">{$cite->id}. </span>";
														echo "<span class=\"type\">{$type}</span>";
														break;
												}
											?>
										</td>
									<?php endif; ?>
									<td class="citation-container">
										<?php
											$formatted = $cite->formatted($this->config->toArray(), $this->filters['search']);

											if ($cite->doi)
											{
												$formatted = str_replace(
													'doi:' . $cite->doi,
													'<a href="' . $cite->url . '" rel="external">' . 'doi:' . $cite->doi . '</a>',
													$formatted
												);
											}

											echo $formatted;

											//get this citations rollover param
											//$params = new \Hubzero\Config\Registry($cite->params);
											$citation_rollover = 0;
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
													<p class="sponsor"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_ABSTRACT_BY'); ?> <?php echo $final; ?></p>
												<?php endif; ?>
												<p><?php echo nl2br($cite->abstract); ?></p>
											</div>
										<?php endif; ?>
									</td>
									<?php if ($this->isManager === true && $cite->scope == 'group') : ?>
										<td class="col-edit"><a class="icon-edit edit individual" href="<?php echo Route::url($base. '&action=edit&id=' .$cite->id ); ?>"></span>
											<span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_EDIT'); ?></span>
										</a></td>
										<td class="col-delete"><a class="icon-delete delete individual protected" href="<?php echo Route::url($base. '&action=delete&id=' . $cite->id); ?>">
											<span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DELETE'); ?></span>
										</a></td>
										<td class="col-publish"><a class="icon-window-publish individual publish" href="<?php echo Route::url($base. '&action=publish&id=' . $cite->id); ?>">
											<span><?php echo ($cite->published == $cite::STATE_PUBLISHED ? Lang::txt('PLG_GROUPS_CITATIONS_UNPUBLISH') : '<strong>' . Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH') . '</strong>'); ?></span>
										</a></td>
									<?php else: ?>
										<td class="col"></td>
										<td class="col"></td>
										<td class="col"></td>
									<?php endif; ?>
								</tr>
								<tr>
									<td <?php if ($this->label == "none") { echo 'colspan="5"'; } else { echo 'colspan="6"'; } ?> class="citation-details <?php echo ($cite->published == $cite::STATE_UNPUBLISHED ? 'unpublished-details' : ''); ?>">
										<?php if ($this->config->get('citations_show_badges', 'yes') == "yes"): ?>
											<?php echo $cite->badgeCloud(); ?> 
										 <?php endif; ?>
										<?php if ($this->config->get('citations_show_tags', 'yes') == "yes"): ?>
											<?php echo $cite->tagCloud(); ?>
										<?php endif; ?>
										<?php	echo $cite->citationDetails($this->openurl); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php else : ?>
						<p class="warning"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
					<?php endif; ?>
					<?php echo $this->citations->pagination; ?>
					<div class="clearfix"></div>
				</div><!-- /.container -->
			</div><!-- /.subject -->
			<div class="aside">
				<fieldset id="download-batch">
					<strong><?php echo Lang::txt('PLG_GROUPS_CITATIONS_EXPORT_MULTIPLE'); ?></strong>
					<p><?php echo Lang::txt('PLG_GROUPS_CITATIONS_EXPORT_MULTIPLE_DESC'); ?></p>

					<input type="submit" name="download" class="download" id="download-endnote" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_ENDNOTE'); ?>" />
					|
					<input type="submit" name="download" class="download" id="download-bibtex" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_BIBTEX'); ?>" />
				<!-- for serving up the file download -->
					<iframe id="download-frame"></iframe>
				<!-- end file serving -->
				</fieldset>
				<fieldset>
					<label>
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_TYPE'); ?>
						<select name="filters[type]" id="type">
							<option value=""><?php echo Lang::txt('PLG_GROUPS_CITATIONS_ALL'); ?></option>
							<?php foreach ($this->types as $t) : ?>
								<?php $sel = ($this->filters['type'] == $t->id) ? "selected=\"selected\"" : ""; ?>
								<option <?php echo $sel; ?> value="<?php echo $t->id; ?>"><?php echo $t->type_title; ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<label>
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_TAGS'); ?>:
						<?php
							$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'filters[tag]', 'actags', '', $this->filters['tag'])));  // type, field name, field id, class, value
							if (count($tf) > 0) : ?>
								<?php echo $tf[0]; ?>
							<?php else: ?>
								<input type="text" name="filters[tag]" value="<?php echo $this->filters['tag']; ?>" />
							<?php endif; ?>
					</label>
					<label>
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_AUTHORED_BY'); ?>
						<input type="text" name="filters[author]" value="<?php echo $this->filters['author']; ?>" />
					</label>
					<label>
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_PUBLISHED_IN'); ?>
						<input type="text" name="filters[publishedin]" value="<?php echo $this->filters['publishedin']; ?>" />
					</label>
					<label for="year_start">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_YEAR'); ?><br />
						<input type="text" name="filters[year_start]" class="half" value="<?php echo $this->filters['year_start']; ?>" />
						to
						<input type="text" name="filters[year_end]" class="half" value="<?php echo $this->filters['year_end']; ?>" />
					</label>
					<label>
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SORT_BY'); ?>
						<select name="filters[sort]" id="sort" class="">
							<?php foreach ($this->sorts as $k => $v) : ?>
								<option value="<?php echo $k; ?>" <?php echo (trim($this->filters['sort']) == $k ? 'selected' : ''); ?>>
									<?php echo $v; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</label>
					<input type="hidden" name="idlist" value="<?php echo $this->filters['idlist']; ?>"/>
					<?php
					/* [!] - zooley: Removed as doesn't seem to be used and 'limit=' in the referer URL can override
					         pagination settings
					<input type="hidden" name="referer" value="<?php echo urlencode($this->escape(@$_SERVER['HTTP_REFERER'])); ?>" />
					*/
					?>
					<input type="hidden" name="action" value="browse" />

					<div class="btn-cluster">
						<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_FILTER'); ?>" />
						<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations'); ?>" class="btn">Reset</a>
					</div>
				</fieldset>
			</div><!-- /.aside -->
		</section>
	</form>
</div><!-- /.frm /#browsebox -->
