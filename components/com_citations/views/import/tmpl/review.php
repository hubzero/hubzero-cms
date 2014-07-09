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

$this->css()
     ->js();

//database object
$database = JFactory::getDBO();

//declare vars
$citations_require_attention = $this->citations_require_attention;
$citations_require_no_attention = $this->citations_require_no_attention;

//dont show array
$no_show = array("errors","duplicate");
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section id="import" class="section">

	<?php
		foreach ($this->messages as $message) {
			echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
		}
	?>

	<ul id="steps">
		<li><a href="<?php echo JURI::base(true); ?>/citations/import" class="passed"><?php echo JText::_('COM_CITATIONS_IMPORT_STEP1'); ?><span><?php echo JText::_('COM_CITATIONS_IMPORT_STEP1_NAME'); ?></span></a></li>
		<li><a class="active"><?php echo JText::_('COM_CITATIONS_IMPORT_STEP2'); ?><span><?php echo JText::_('COM_CITATIONS_IMPORT_STEP2_NAME'); ?></span></a></li>
		<li><a><?php echo JText::_('COM_CITATIONS_IMPORT_STEP3'); ?><span><?php echo JText::_('COM_CITATIONS_IMPORT_STEP3_NAME'); ?></span></a></li>
	</ul><!-- / #steps -->

	<form method="post" action="<?php echo JRoute::_('index.php?option='. $this->option . '&task=import_save'); ?>">
		<?php if ($citations_require_attention) : ?>
			<table class="upload-list require-action">
				<thead>
					<tr>
						<!--<th></th>-->
						<th><?php echo JText::sprintf('COM_CITATIONS_IMPORT_REQUIRE_ATTENTION', count($citations_require_attention)); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php $counter = 0; ?>
					<?php foreach ($citations_require_attention as $c) : ?>
						<?php
							//load the duplicate citation
							$cc = new CitationsCitation($database);
							$cc->load($c['duplicate']);

							//get the type
							$ct = new CitationsType($database);
							$type = $ct->getType($cc->type);
							$type_title = $type[0]['type_title'];

							//get citations tags
							$th = new TagsHandler($database);
							$th->_tbl = "citations";
							$tags = $th->get_tag_string($cc->id, 0, 0, NULL, 0, "");
							$badges = $th->get_tag_string($cc->id, 0, 0, NULL, 0, "badges");
						?>
						<tr>
							<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
							<td>
								<span class="citation-title"><u><?php echo JText::_('COM_CITATIONS_IMPORT_DUPLICATE'); ?></u>: <?php echo html_entity_decode($c['title']); ?></span>
								<span class="click-more"><?php echo JText::_('COM_CITATIONS_IMPORT_SHOW_CITATION_DETAILS'); ?></span>
<?php if (1) { ?>
								<table class="citation-details hide">
									<thead>
										<tr>
											<th><?php echo JText::_('COM_CITATIONS_IMPORT_CITATION_DETAILS'); ?></th>
											<th class="options">
												<label>
													<input
														type="radio"
														class="citation_require_attention_option"
														name="citation_action_attention[<?php echo $counter; ?>]"
														value="overwrite"
														checked="checked" /> <?php echo JText::_('COM_CITATIONS_IMPORT_CITATION_REPLACE'); ?>
												</label>
												<label>
													<input
														type="radio"
														class="citation_require_attention_option"
														name="citation_action_attention[<?php echo $counter; ?>]"
														value="both" /> <?php echo JText::_('COM_CITATIONS_IMPORT_CITATION_KEEP'); ?>
												</label>
												<label>
													<input
														type="radio"
														class="citation_require_attention_option"
														name="citation_action_attention[<?php echo $counter; ?>]"
														value="discard" /> <?php echo JText::_('COM_CITATIONS_IMPORT_CITATION_NOTHING'); ?>
												</label>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach (array_keys($c) as $k) : ?>
											<?php if (!in_array($k, $no_show)) : ?>
												<tr>
													<td class="key">
														<?php echo str_replace("_", " ", $k); ?>
													</td>
													<td>
														<table class="citation-differences">
															<tr>
																<td><?php echo JText::_('COM_CITATIONS_IMPORT_JUST_UPLOADED'); ?>:</td>
																<td>
																	<span class="new insert"><?php echo html_entity_decode(nl2br($c[$k])); ?></span>
																</td>
															</tr>
															<tr>
																<td><?php echo JText::_('COM_CITATIONS_IMPORT_ON_FILE'); ?>:</td>
																<td>
																	<span class="old delete">
																		<?php
																			switch ($k)
																			{
																				case 'type':	echo $type_title;		break;
																				case 'tags':	echo $tags;				break;
																				case 'badges':	echo $badges;			break;
																				default:		echo html_entity_decode(nl2br($cc->$k));
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
						<th><?php echo JText::sprintf('COM_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION', count($citations_require_no_attention)); ?></th>
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
								<span class="click-more"><?php echo JText::_('COM_CITATIONS_IMPORT_SHOW_CITATION_DETAILS'); ?></span>
								<table class="citation-details hide">
									<thead>
										<tr>
											<th colspan="2"><?php echo JText::_('COM_CITATIONS_IMPORT_CITATION_DETAILS'); ?></th>
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
			<input type="submit" name="submit" value="<?php echo JText::_('COM_CITATIONS_IMPORT_SUBMIT_IMPORTED'); ?>" />
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="import_save" />
	</form>
</section><!-- / .section -->
