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
?>
<?php if (!$this->components->count()) { ?>
	<p class="error"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_NO_COMPONENTS_FOUND'); ?></p>
<?php } else { ?>
	<form action="<?php echo Route::url($this->member->link() . '&active=messages'); ?>" method="post" id="hubForm" class="full">
		<input type="hidden" name="action" value="savesettings" />
		<table class="settings">
			<caption>
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
			</caption>
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SENT_WHEN'); ?></th>
				<?php foreach ($this->notimethods as $notimethod) { ?>
					<th scope="col"><input type="checkbox" name="override[<?php echo $notimethod; ?>]" value="all" onclick="HUB.MembersMsg.checkAll(this, 'opt-<?php echo $notimethod; ?>');" /> <?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)); ?></th>
				<?php } ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo (count($this->notimethods) + 1); ?>">
						<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$cls = 'even';

			$sheader = '';
			foreach ($this->components as $component)
			{
				if ($component->name != $sheader)
				{
					$sheader = $component->name;
					Lang::load($component->name);

					$display_header = Lang::hasKey($component->name) ? Lang::txt($component->name) : ucfirst(str_replace('com_', '', $component->name));
				?>
				<tr class="section-header">
					<th scope="col"><?php echo $this->escape($display_header); ?></th>
					<?php foreach ($this->notimethods as $notimethod) { ?>
						<th scope="col"><span class="<?php echo $notimethod; ?> iconed"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></span></th>
					<?php } ?>
				</tr>
				<?php
				}
				$cls = (($cls == 'even') ? 'odd' : 'even');
				?>
				<tr class="<?php echo $cls; ?>">
					<th scope="col"><?php echo $this->escape($component->title); ?></th>
					<?php echo plgMembersMessages::selectMethod($this->notimethods, $component->action, $this->settings[$component->action]['methods'], $this->settings[$component->action]['ids']); ?>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>

		<?php echo Html::input('token'); ?>
	</form>
<?php } ?>