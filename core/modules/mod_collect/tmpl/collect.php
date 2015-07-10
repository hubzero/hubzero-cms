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

// no direct access
defined('_HZEXEC_') or die();

$url = urldecode(Request::path());
$url = implode('/', array_map('rawurlencode', explode('/', $url)));
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="<?php echo htmlspecialchars($url); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo Lang::txt('MOD_COLLECT'); ?></legend>

		<?php if ($this->collections) { ?>
			<div class="grid in-collections">
				<p><?php echo Lang::txt('MOD_COLLECT_ALREADY_COLLECTED'); ?></p>
				<ul>
					<?php foreach ($this->collections as $collection) { ?>
						<li><a href="<?php echo Route::url($collection->link()); ?>"><?php echo $this->escape(stripslashes($collection->get('title'))); ?></a></li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>

		<div class="grid">
			<div class="col span-half">
				<label for="field-collection">
					<?php echo Lang::txt('MOD_COLLECT_SELECT_COLLECTION'); ?>
					<select name="collectible[collection_id]" id="field-collection">
						<option value="0"><?php echo Lang::txt('MOD_COLLECT_SELECT'); ?></option>
						<optgroup label="<?php echo Lang::txt('MOD_COLLECT_MY_COLLECTIONS'); ?>">
						<?php
						$i = 0;
						if ($this->myboards)
						{
							foreach ($this->myboards as $board)
							{
						?>
							<option<?php if ($i == 0) { echo ' selected="selected"'; } ?> value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
						<?php
								$i++;
							}
						}
						?>
						</optgroup>
						<?php
						if ($this->groupboards)
						{
							foreach ($this->groupboards as $optgroup => $boards)
							{
								if (count($boards) <= 0) continue;
								?>
								<optgroup label="<?php echo $this->escape(stripslashes($optgroup)); ?>">
									<?php
									foreach ($boards as $board)
									{
										?>
										<option<?php if ($i == 0) { echo ' selected="selected"'; } ?> value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
										<?php
										$i++;
									}
									?>
						</optgroup>
						<?php
							}
						}
						?>
					</select>
				</label>
			</div>

			<p class="or"><?php echo Lang::txt('MOD_COLLECT_OR'); ?></p>

			<div class="col span-half omega">
				<label for="field-collection_title">
					<?php echo Lang::txt('MOD_COLLECT_CREATE_COLLECTION'); ?>
					<input type="text" name="collectible[title]" id="field-collection_title" />
				</label>
			</div>
		</div>

		<label for="field_description">
			<?php echo Lang::txt('MOD_COLLECT_ADD_DESCRIPTION'); ?>
			<?php echo App::get('editor')->display('collectible[description]', '', '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer')); ?>
		</label>
	</fieldset>

	<input type="hidden" name="collectible[item_id]" value="<?php echo $this->escape($this->item->get('id')); ?>" />
	<input type="hidden" name="tryto" value="collect" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input type="submit" value="<?php echo Lang::txt('MOD_COLLECT_SAVE'); ?>" />
	</p>
</form>
