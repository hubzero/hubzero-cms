<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author	Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

if (count($this->citations) > 0) :
	$this->css('citations.css')
		 ->js();

	$formatter = new \Components\Citations\Helpers\Format();
	$formatter->setTemplate($this->citationTemplate);

	$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';
?>
	<div class="frm" id="browsebox">
		<table class="citations entries">
			<thead>
				<tr>
					<th class="batch">
						<input type="checkbox" class="checkall-download" />
					</th>
					<th colspan="5"><?php echo Lang::txt('PLG_GROUPS_CITATIONS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php $x = 0; ?>
				<?php foreach ($this->citations as $cite) : ?>
					<tr class="citation-row">
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
									foreach ($this->types as $t)
									{
										if ($t->id == $cite->type)
										{
											$type = $t->type_title;
										}
									}
									$type = ($type != "") ? $type : "Generic";

									switch ($this->label)
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
							<?php
								$formatted = $cite->formatted
									? $cite->formatted
									: $formatter->formatCitation($cite,
										$this->filters['search'], $this->coins, $this->config);

								if ($cite->doi)
								{
									$formatted = str_replace('doi:' . $cite->doi,
										'<a href="' . $cite->url . '" rel="external">'
										. 'doi:' . $cite->doi . '</a>', $formatted);
								}

								echo $formatted; ?>
							<?php
								//get this citations rollover param
								$params = new \Hubzero\Html\Parameter($cite->params);
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
					</tr>
					<tr>
						<td <?php if ($this->label == "none") { echo 'colspan="3"'; } else { echo 'colspan="3"'; } ?> class="citation-details">
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
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div><!-- /.frm /#browsebox -->
<?php endif;
