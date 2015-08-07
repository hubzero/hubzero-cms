<?php

/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
//defined('_HZEXEC_') or die();

$this->css()->js();
$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';
?>

<script type="text/javascript">
var $jQ = jQuery.noConflict();

$jQ(document).ready(function(e) {
	var formatSelector = $jQ('#format-selector'),
		formatBox = $jQ('#format-string');

	//when we change format box
	formatSelector.on('change', function(event) {
		var value  = $jQ(this).val(),
			format = $jQ(this).find(':selected').attr('data-format');
		formatBox.val(format);
	});

	//when we customize the format
	formatBox.on('keyup', function(event) {
		var customOption = formatSelector.find('option[value=custom]');
		customOption.attr('data-format', formatBox.val());
	});

	// Thanks Zach Weidner!
	$jQ(function($) {
		$('tr').click(function() {
			if ( ($('select[name="citation-format"]').find('option[value="custom"]').attr("selected") != "selected"))
			{
				// force custom format
				$('select[name="citation-format"]').find('option[value="custom"]').attr("selected",true);

			}
			if ($('select[name="citation-format"] option:contains(custom-group-<?php echo $this->group->get('cn'); ?>)').attr("selected") != "selected")
			{
					// force the existing custom group format to be selected
					$('select[name="citation-format"] option:contains(custom-group-<?php echo $this->group->get('cn'); ?>)').attr("selected", true);
			}

			$('#format-string').val($('#format-string').val() + $(this).attr('id'));
			$('#format-string').focus();
		});
	});
});
</script>

<div id="browsebox" class="frm">
	<h3><?php echo Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS'); ?></h3>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
		<form action="<?php echo Route::url($base . '?action=settings'); ?>" method="post" id="hubForm" class="add-citation">

			<!-- Citation sources -->
			<div class="explaination">
			<p>
			    <?php echo Lang::txt('PLG_GROUPS_CITATIONS_SOURCE_EXPLAIN'); ?>
            </p>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('Sources'); ?></legend>

				<div class="grid">
					<div class="col span6">
						<label for="display-members">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SELECT_SOURCES'); ?>
						<select name="display" id="display-sources">
							<option value="group"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DISPLAY_GROUPS'); ?></option>
							<option value="member"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DISPLAY_MEMBERS'); ?></option>
						</select>
						</label>
					</div>
					<div class="col span6 omega">
					<p id="applicableFields">
                        <?php echo Lang::txt('PLG_GROUPS_CITATIONS_GROUP_ATTRIB'); ?>
					</p>
					<p>
                        <?php echo Lang::txt('PLG_GROUPS_CITATIONS_MEMBER_ATTRIB'); ?>
					</p>
					</div>
				</div>
			</fieldset>
			<div class="clear"></div>

			<!-- Badge and Tag options -->
			<fieldset>
				<legend><?php echo Lang::txt('Badges and Tags Options'); ?></legend>

				<div class="grid">
					<div class="col span6">
						<label for="display-members">
						<?php echo Lang::txt('Display Tags'); ?>
						<select name="citations_show_tags" id="show_tags">
							<option value="yes" <?php echo ($this->citations_show_tags == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
							<option value="no" <?php echo ($this->citations_show_tags == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
						</select>
						</label>
					</div>
					<div class="col span6 omega">
					<label for="display-members">
						<?php echo Lang::txt('Show Badges'); ?>
						<select name="citations_show_badges" id="show_badges">
							<option value="yes" <?php echo ($this->citations_show_badges == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
							<option value="no" <?php echo ($this->citations_show_badges == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
						</select>
					</label>
					</div>
			</fieldset>
			<div class="clear"></div>

			<!-- Coins and other other options -->
			<div class="explaination">
			<p>
				<?php echo Lang::txt('What are COinS?\n'); ?>
			    <a href="http://ocoins.info/" target="_blank" alt="read more about COiNS"> <?php echo Lang::txt('Read more about COinS.'); ?></a>
			</p>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('COinS Options'); ?></legend>

				<div class="grid">
					<div class="col span6">
						<label for="display-members">
						<?php echo Lang::txt('Include COinS'); ?>
						<select name="include_coins" id="include-coins">
							<option value="no" <?php echo ($this->include_coins == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
							<option value="yes" <?php echo ($this->include_coins == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
						</select>
						</label>
					</div>
					<div class="col span6 omega">
					<label for="display-members">
						<?php echo Lang::txt('COinS Only'); ?>
						<select name="coins_only" id="coins-only">
							<option value="no" <?php echo ($this->coins_only == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
							<option value="yes" <?php echo ($this->coins_only == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
						</select>
					</label>
					</div>
			</fieldset>
			<div class="clear"></div>
			<div class="explaination">
				<p>
					 <?php echo Lang::txt('PLG_GROUPS_CITATIONS_FORMAT_EXPLAIN'); ?>
				</p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('PLG_GROUPS_CITATIONS_CITATION_FORMAT'); ?></legend>

				<div class="grid">
					<div class="col span7">
						<label for="cite">
							<?php echo Lang::txt('PLG_GROUPS_CITATIONS_CITATION_FORMAT'); ?>:
								<select name="citation-format" id="format-selector">
								<?php if ($this->customFormat === false): ?>
									<option value="custom" data-format="">Custom for Group</option>
								<?php endif; ?>
									<?php foreach ($this->formats as $format): ?>
										<option <?php if ($this->currentFormat->id == $format->id) { echo 'selected'; } ?> value="<?php echo $format->id; ?>" data-format="<?php echo $format->format; ?>">
											<?php echo $format->style; ?>
										</option>
									<?php endforeach; ?>
								</select>

							<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_CITE_KEY_EXPLANATION'); ?></span>
						</label>

						<!-- some space -->
						<div class="clear"></div>

						<label for="cite">
						<textarea name="template" rows="10" id="format-string"><?php echo $this->currentFormat->format; ?></textarea>

					</div>
					<div class="col span4 omega">
						<span id="templateExplaination"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_CLICK_TABLE'); ?></span>
						<table class="templateTable">
							<thead>
								<tr class="clickable">
									<th><?php echo Lang::txt('Key'); ?></th>
									<th><?php echo Lang::txt('Value'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									// get the keys
									foreach ($this->templateKeys as $k => $v)
									{
										echo "<tr id='{$v}'><td>{$v}</td><td>{$k}</td></tr>";
									}
								?>
							</tbody>
					</table>
					</div>
				</div>
			</fieldset><div class="clear"></div>

		<!-- submit -->
		<p class="submit">
			<input class="btn btn-success" type="submit" name="create" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SAVE'); ?>" />
		</p>

		<div class="clear"></div>
	</form>
</div>
