<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js();

$base =	Route::url($this->member->link() . '&active=' . $this->_name);
?>

<div id="browsebox" class="frm">
	<h3><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS'); ?></h3>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url($base . '?action=settings'); ?>" method="post" id="hubForm" class="add-citation">
		<!-- Badge and Tag options -->
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_BADGE_OPTIONS'); ?></legend>

			<div class="grid">
				<div class="col span6">
					<div class="form-group">
						<label for="display-members">
							<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DISPLAY_TAGS'); ?>
							<select name="citations_show_tags" id="show_tags" class="form-control">
								<option value="yes" <?php echo ($this->citations_show_tags == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
								<option value="no" <?php echo ($this->citations_show_tags == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
							</select>
						</label>
					</div>
				</div>
				<div class="col span6 omega">
					<div class="form-group">
						<label for="display-members">
							<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DISPLAY_BADGES'); ?>
							<select name="citations_show_badges" id="show_badges" class="form-control">
								<option value="yes" <?php echo ($this->citations_show_badges == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
								<option value="no" <?php echo ($this->citations_show_badges == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
							</select>
						</label>
					</div>
				</div>
			</div>
		</fieldset>
		<div class="clear"></div>

		<!-- Coins and other other options -->
		<div class="explaination">
			<p>
				<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_WHAT_ARE_COINS'); ?><br />
				<a href="http://ocoins.info/" rel="nofollow external" alt="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_READ_MORE_COINS'); ?>"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_READ_MORE_COINS'); ?></a>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_COINS_OPTIONS'); ?></legend>

			<div class="grid">
				<div class="col span6">
					<div class="form-group">
						<label for="display-members">
							<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_INCLUDE_COINS'); ?>
							<select name="include_coins" id="include-coins" class="form-control">
								<option value="no" <?php echo ($this->include_coins == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
								<option value="yes" <?php echo ($this->include_coins == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
							</select>
						</label>
					</div>
				</div>
				<div class="col span6 omega">
					<div class="form-group">
						<label for="display-members">
							<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_COINS_ONLY'); ?>
							<select name="coins_only" id="coins-only" class="form-control">
								<option value="no" <?php echo ($this->coins_only == "no") ? "selected=selected" : ""; ?>><?php echo Lang::txt('No'); ?></option>
								<option value="yes" <?php echo ($this->coins_only == "yes") ? "selected=selected" : ""; ?>><?php echo Lang::txt('Yes'); ?></option>
							</select>
						</label>
					</div>
				</div>
		</fieldset>
		<div class="clear"></div>

		<div class="explaination">
			<p>
				<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_FORMAT_EXPLAIN'); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_FORMAT'); ?></legend>

			<div class="form-group">
				<label for="cite">
					<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_FORMAT'); ?>:
					<select name="citation-format" id="format-selector" class="form-control" data-uid="<?php echo $this->member->get('id'); ?>">
						<?php foreach ($this->formats as $format): ?>
								<?php if ($format->style != 'custom-member-'.$this->member->get('id')): ?>
							<option <?php if ($this->currentFormat->id == $format->id) { echo 'selected'; } ?> value="<?php echo $format->id; ?>" data-format="<?php echo $format->format; ?>">
								<?php echo $format->style; ?>
							</option>
							 <?php elseif ($format->style == 'custom-member-'.$this->member->get('id')): ?>
								<option <?php if ($this->currentFormat->id == $format->id) { echo 'selected'; } ?> value="<?php echo $format->id; ?>" data-format="<?php echo $format->format; ?>">
									<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_CUSTOM_FORMAT'); ?>
								</option>
							<?php endif; ?> 
						<?php endforeach; ?>
						<?php if ($this->customFormat === false): ?>
						<option value="custom" data-format="">
									<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_CUSTOM_FORMAT'); ?>
						</option>
						<?php endif; ?>
					</select>

					<span class="hint"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_FORMAT_EXPLAINATION'); ?></span>
				</label>
			</div>

			<!-- some space -->
			<div class="clear"></div>

			<div class="form-group">
				<label for="format-string">
					<textarea name="template" rows="10" id="format-string" class="form-control"><?php echo addslashes($this->currentFormat->format); ?></textarea>
				</label>
			</div>

			<table class="templateTable">
				<caption id="templateExplaination"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_CLICK_TABLE'); ?></caption>
				<thead>
					<tr class="clickable">
						<th scope="col"><?php echo Lang::txt('Key'); ?></th>
						<th scope="col"><?php echo Lang::txt('Value'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					// get the keys
					foreach ($this->templateKeys as $k => $v)
					{
						?>
						<tr id="<?php echo $v; ?>">
							<td><?php echo $v; ?></td>
							<td><?php echo $k; ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</fieldset><div class="clear"></div>

		<!-- submit -->
		<p class="submit">
			<input class="btn btn-success" type="submit" name="create" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SAVE'); ?>" />
		</p>

		<div class="clear"></div>
	</form>
</div>
