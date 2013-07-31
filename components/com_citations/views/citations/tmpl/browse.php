<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//helper lib
ximport('Hubzero_View_Helper_Html');

//citation params
$label = $this->config->get("citation_label", "number");
$rollover = $this->config->get("citation_rollover", "no");
$rollover = ($rollover == "yes") ? 1 : 0;
$template = $this->config->get("citation_format", "");

//batch downloads
$batch_download = $this->config->get("citation_batch_download", 1);

//Include COinS
$coins = $this->config->get("citation_coins", 1);

//do we want to number li items
if ($label == "none") {
	$citations_label_class = "no-label";
} elseif ($label == "number") {
	$citations_label_class = "number-label";
} elseif ($label == "type") {
	$citations_label_class = "type-label";
} elseif ($label == "both") {
	$citations_label_class = "both-label";
}

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<?php if ($this->allow_import == 1 || ($this->allow_import == 2 && $this->isAdmin)) : ?>
			<li class="last">
				<a class="add btn" href="<?php echo JRoute::_('index.php?option=com_citations&task=add'); ?>"><?php echo JText::_('Submit a Citation'); ?></a>
			</li>
		<?php endif; ?>
	</ul>
</div>

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" id="citeform" method="GET" class="<?php if ($batch_download) { echo " withBatchDownload"; } ?>">
		<div class="aside">
			<fieldset>
				<label>
					<?php echo JText::_('Type'); ?>
					<select name="type" id="type">
						<option value="">All</option>
						<?php foreach($this->types as $t) : ?>
							<?php $sel = ($this->filters['type'] == $t['id']) ? "selected=\"selected\"" : ""; ?>
 							<option <?php echo $sel; ?> value="<?php echo $t['id']; ?>"><?php echo $t['type_title']; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<?php echo JText::_('Tags'); ?>:
					<?php 
						JPluginHelper::importPlugin('hubzero');
						$dispatcher =& JDispatcher::getInstance();
						$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tag', 'actags', '', $this->filters['tag'])));  // type, field name, field id, class, value
						if (count($tf) > 0) : ?>
							<?php echo $tf[0]; ?>
						<?php else: ?>
							<input type="text" name="tag" value="<?php echo $this->filters['tag']; ?>" />
						<?php endif; ?>
				</label>
				<label>
					<?php echo JText::_('Authored By'); ?>
					<input type="text" name="author" value="<?php echo $this->filters['author']; ?>" />
				</label>
				<label>
					<?php echo JText::_('Published In'); ?>
					<input type="text" name="publishedin" value="<?php echo $this->filters['publishedin']; ?>" />
				</label>
				<label for="year_start">
					<?php echo JText::_('Year'); ?><br />
					<input type="text" name="year_start" class="half" value="<?php echo $this->filters['year_start']; ?>" />
					to
					<input type="text" name="year_end" class="half" value="<?php echo $this->filters['year_end']; ?>" />
				</label>
				<?php if($this->isAdmin) { ?>
					<fieldset>
						<label>
							<?php echo JText::_('Uploaded Between'); ?>
							<input type="text" name="startuploaddate" value="" />
							<div class="hint">YYYY-MM-DD</div>
						</label>
						<label>
							<?php echo JText::_('and'); ?><br/>
							<input type="text" name="enduploaddate" value="" />
							<div class="hint">YYYY-MM-DD</div>
						</label>
					</fieldset>
				<?php } ?>
				<label>
					<?php echo JText::_('SORT_BY'); ?>
					<select name="sort" id="sort" class="">
						<?php foreach($this->sorts as $k => $v) : ?>
							<?php $sel = ($k == $this->filters['sort']) ? "selected" : "";
							if(($this->isAdmin !== true) && ($v == "Date uploaded"))
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
					<legend><?php echo JText::_('Reference Type'); ?></legend>
					<label>
						<input class="option" type="checkbox" name="reftype[research]" value="1"<?php if (isset($this->filters['reftype']['research'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Research'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="reftype[education]" value="1"<?php if (isset($this->filters['reftype']['education'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Education'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="reftype[eduresearch]" value="1"<?php if (isset($this->filters['reftype']['eduresearch'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Education/Research'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="reftype[cyberinfrastructure]" value="1"<?php if (isset($this->filters['reftype']['cyberinfrastructure'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Cyberinfrastructure'); ?>
					</label>
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_('Author Geography'); ?></legend>
					<label>
						<input class="option" type="checkbox" name="geo[us]" value="1"<?php if (isset($this->filters['geo']['us'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('US'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="geo[na]" value="1"<?php if (isset($this->filters['geo']['na'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('North America'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="geo[eu]" value="1"<?php if (isset($this->filters['geo']['eu'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Europe'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="geo[as]" value="1"<?php if (isset($this->filters['geo']['as'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Asia'); ?>
					</label>
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_('Author Affiliation'); ?></legend>
					<label>
						<input class="option" type="checkbox" name="aff[university]" value="1"<?php if (isset($this->filters['aff']['university'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('University'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="aff[industry]" value="1"<?php if (isset($this->filters['aff']['industry'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Industry'); ?>
					</label>
					<label>
						<input class="option" type="checkbox" name="aff[government]" value="1"<?php if (isset($this->filters['aff']['government'])) { echo ' checked="checked"'; } ?> /> 
						<?php echo JText::_('Government'); ?>
					</label>
				</fieldset>

				<input type="hidden" name="idlist" value="<?php echo $this->filters['idlist']; ?>"/>   
				<input type="hidden" name="referer" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />

				<p class="submit">
					<input type="submit" value="Filter" />
				</p>
				
			</fieldset>
			
			<?php if ($batch_download) : ?>
				<fieldset id="download-batch">
					<strong><?php echo JText::_('Export Multiple Citations'); ?></strong>
					<p><?php echo JText::_('Check the citations that you would like to have exported.'); ?></p>
					
					<input type="submit" name="download" class="download-endnote" value="EndNote" /> 
					| 
					<input type="submit" name="download" class="download-bibtex" value="BibTex" />
				</fieldset>
			<?php endif; ?>
		</div><!-- /.aside -->
		
		<div class="subject">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend>Search Citations</legend>
					<input type="text" name="search" id="entry-search-field" value="<?php echo stripslashes($this->filters['search']); ?>" placeholder="Search Citations by Title, Author, ISBN, DOI, Publisher, and Abstract" />
				</fieldset>
			</div><!-- /.container .data-entry -->
			<div class="container">
				<ul class="entries-menu filter-options">
					<?php
						$queryString = "";
						$exclude = array("filter");
						foreach($this->filters as $k => $v)
						{
							if($v != "" && !in_array($k, $exclude))
							{
								if(is_array($v))
								{
									foreach($v as $k2 => $v2)
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
					<li><a <?php if($this->filters['filter'] == '') { echo 'class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_citations&task=browse'.$queryString.'&filter='); ?>">All</a></li>
					<li><a <?php if($this->filters['filter'] == 'aff') { echo 'class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_citations&task=browse'.$queryString.'&filter=aff'); ?>">Affiliated</a></li>
					<li><a <?php if($this->filters['filter'] == 'nonaff') { echo 'class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_citations&task=browse'.$queryString.'&filter=nonaff'); ?>">Non-Affiliated</a></li>
				</ul>
				<div class="clearfix"></div>
					
				<?php if(count($this->citations) > 0) : ?>
					<?php
						$formatter = new CitationFormat();
						$formatter->setTemplate($template);

						// Fixes the counter so it starts counting at the current citation number instead of restarting on 1 at every page
						$counter = $this->filters['start'];

						if($counter == '')
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
								<th colspan="3">Citations</th>
							</tr>
							<?php if($this->isAdmin) : ?>
								<tr class="hidden"></tr>
							<?php endif; ?>
						</thead>
						<tbody>
							<?php $x = 0; ?>
							<?php foreach($this->citations as $cite) : ?>
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
												foreach($this->types as $t) {
													if ($t['id'] == $cite->type) {
														$type = $t['type_title'];
													}
												}
												$type = ($type != "") ? $type : "Generic";

												switch($label)
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
										<?php echo $formatter->formatCitation($cite, $this->filters['search'], $coins, $this->config); ?>
										<?php
											//get this citations rollover param
											$params = new JParameter($cite->params);
											$citation_rollover = $params->get('rollover', $rollover);
										?>
										<?php if ($citation_rollover && $cite->abstract != "") : ?>
											<div class="citation-notes">
												<?php
													$cs = new CitationsSponsor($this->database);
													$sponsors = $cs->getCitationSponsor($cite->id);
													$final = "";
													if ($sponsors)
													{
														foreach($sponsors as $s)
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
													<p class="sponsor">Abstract courtesy of <?php echo $final; ?></p>
												<?php endif; ?>
												<p><?php echo nl2br($cite->abstract); ?></p>
											</div>
										<?php endif; ?>
									</td>
									<?php if($this->isAdmin === true) { ?>
										<td class="col-edit"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$cite->id); ?>">Edit</a></td>
									<?php } ?>
								</tr>
								<tr>
									<td colspan="<?php if ($label == "none") { echo 2; } else { echo 3; }; ?>" class="citation-details">
										<?php
											$singleCitationView = $this->config->get('citation_single_view', 1);
											if (!$singleCitationView)
											{
												echo $formatter->citationDetails($cite, $this->database, $this->config, $this->openurl); 
											}
										?>
										<?php if ($this->config->get("citation_show_badges","no") == "yes") : ?>
											<?php echo $formatter->citationBadges($cite, $this->database); ?>
										<?php endif; ?>

										<?php if ($this->config->get("citation_show_tags","no") == "yes") : ?>
											<?php echo $formatter->citationTags($cite, $this->database); ?>
										<?php endif; ?>
									</td>
								</tr>
								<?php $counter++; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p class="warning"><?php echo JText::_('COM_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
				<?php endif; ?>
				<?php 
					$this->pageNav->setAdditionalUrlParam('task', 'browse');
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
									$this->pageNav->setAdditionalUrlParam($key . '[' . $k . ']', $v);
								}
							break;

							default:
								$this->pageNav->setAdditionalUrlParam($key, $value);
							break;
						}
					}
					echo $this->pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
			</div><!-- /.container -->
		</div><!-- /.subject -->
	</form>
</div>
