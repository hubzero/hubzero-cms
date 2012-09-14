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
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul>
		<li class="last">
			<a class="main-page btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('Main page'); ?></a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	
	<?php
		foreach($this->messages as $message) {
			echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
		}
	?>
	
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" id="citeform" method="post">
		<div class="aside <?php if ($batch_download) { echo " withBatchDownload"; } ?>">
			<?php if ($batch_download) : ?>
				<fieldset id="download-batch">
					<strong><?php echo JText::_('Export Multiple Citations'); ?></strong>
					<p><?php echo JText::_('Check the citations that you would like to have exported.'); ?></p>
					
					<input type="submit" name="download" class="download-endnote" value="EndNote" /> 
					| 
					<input type="submit" name="download" class="download-bibtex" value="BibTex" />
					<input type="hidden" name="task" value="downloadbatch" id="download-batch-input" />
				</fieldset>
			<?php endif; ?>
			<fieldset>
				<label>
					<?php echo JText::_('SORT_BY'); ?>
					<select name="sort" id="sort" class="">
						<?php foreach($this->sorts as $k => $v) : ?>
							<?php $sel = ($k == $this->filters['sort']) ? "selected" : ""; ?>
							<option <?php echo $sel; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				
				<label>
					<?php echo JText::_('Type'); ?>
					<select name="type" id="type">
						<option value="all">All</option>
						<?php
							foreach($this->types as $t) {
								$sel = ($this->filters['type'] == $t['id']) ? "selected=\"selected\"" : "";
 								echo "<option {$sel} value=\"{$t['id']}\">{$t['type_title']}</option>";
							}
						?>
					</select>
				</label>
				
				<label>
					<?php echo JText::_('Affiliation'); ?>
					<select name="filter" id="filter" class="">
						<?php foreach($this->filter as $k => $v) : ?>
							<?php $sel = ($k == $this->filters['filter']) ? "selected" : ""; ?>
							<option <?php echo $sel; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				
				<label>
					<?php echo JText::_('FOR_YEAR'); ?>
					<select name="year">
						<option value=""<?php if ($this->filters['year'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('All'); ?></option>
<?php
	$y = date("Y");
	$y++;
	for ($i=1995, $n=$y; $i < $n; $i++)
	{
?>
						<option value="<?php echo $i; ?>"<?php if ($this->filters['year'] == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
<?php
	}
?>
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
				
				<label>
					<?php echo JText::_('SEARCH_TITLE'); ?>
					<input type="text" name="search" value="<?php echo $this->escape(stripslashes($this->filters['search'])); ?>" />
				</label>
				
				<p class="submit">
					<input type="submit" name="go" value="<?php echo JText::_('GO'); ?>" />
				</p>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="browse" />
			</fieldset>
		</div><!-- / .aside -->
		<div class="subject">
			<?php if (count($this->citations) > 0) : ?>
				<?php
					$formatter = new CitationFormat();
					$formatter->setTemplate($template);

					$counter = 1;
				?>
				<table class="citations">
					<thead>
						<tr>
							<?php if ($batch_download) : ?>
								<th class="batch">
									<input type="checkbox" class="checkall-download" />
								</th>
							<?php endif; ?>
							<th colspan="2">Citations</th>
						</tr>
					</thead>
					<tbody>
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
								
									<?php if ($rollover == "yes" && $cite->abstract != "") : ?>
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
							</tr>
							<tr>
								<td colspan="<?php if ($label == "none") { echo 2; } else { echo 3; }; ?>" class="citation-details">
									<?php echo $formatter->citationDetails($cite, $this->database, $this->config, $this->openurl); ?>
								
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
			<?php else: ?>
				<p class="warning"><?php echo JText::_('NO_CITATIONS_FOUND'); ?></p>
			<?php endif; ?>
			
			<?php 
				$this->pageNav->setAdditionalUrlParam('task', 'browse');
				foreach ($this->filters as $key => $value)
				{
					switch ($key)
					{
						case 'limit':
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
			
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->