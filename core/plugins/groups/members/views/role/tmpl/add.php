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
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-browse btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=members'); ?>">
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS'); ?>
		</a>
	</li>
</ul>

<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=members'); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_DETAILS'); ?></legend>
		<label>
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_NAME'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
			<input type="text" name="role[name]" value="<?php echo $this->role->name; ?>" >
		</label>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_PERMISSIONS'); ?></legend>
			<?php foreach ($this->available_permissions as $perm => $label) : ?>
				<label>
					<?php $ckd = ($this->role->permissions->get($perm)) ? 'checked="checked"' : '' ?>
					<input type="hidden" name="role[permissions][<?php echo $perm; ?>]" value="0" />
					<input class="option" type="checkbox" <?php echo $ckd; ?> name="role[permissions][<?php echo $perm; ?>]" value="1"> <?php echo $label; ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	</fieldset>
	<div class="clear"></div>

	<input type="hidden" name="role[id]" value="<?php echo $this->role->id; ?>" >
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="action" value="saverole" />

	<p class="submit">
		<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
	</p>
</form>
