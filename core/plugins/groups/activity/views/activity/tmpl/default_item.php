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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$status = '';
if (!$this->row->wasViewed())
{
	$status = 'new';

	$this->row->markAsViewed();
}

$creator = User::getInstance($this->row->log->get('created_by'));
//$creator = $this->row->log->creator;
$name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_GROUPS_ACTIVITY_UNKNOWN'))));
?>
<li
	data-time="<?php echo $this->row->get('created'); ?>"
	data-id="<?php echo $this->row->get('id'); ?>"
	data-log_id="<?php echo $this->row->get('log_id'); ?>"
	data-context="<?php echo $this->row->log->get('scope'); ?>"
	data-action="<?php echo $this->row->log->get('action'); ?>"
	class="activity <?php echo $status; ?>">

	<div class="activity <?php echo $this->escape($this->row->log->get('action')); ?> <?php echo $this->escape(str_replace('.', '-', $this->row->log->get('scope'))); ?>">
		<?php /*<ul class="activity-options">
			<li><a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity&action=remove&action=' . $this->row->log->get('action')); ?>">Don't show this action</a></li>
			<li><a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity&action=remove&scope=' . $this->row->log->get('scope')); ?>">Don't show from all objects of this type</a></li>
			<li><a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity&action=remove&scope=' . $this->row->log->get('scope') . '&scope_id=' . $this->row->log->get('scope_id')); ?>">Don't show this object</a></li>
		</ul>
		<?php if ($this->row->log->creator()->get('public')) { ?>
			<a href="<?php echo Route::url($this->row->log->creator->link()); ?>" title="<?php echo $name; ?>" class="img-link">
				<img src="<?php echo $this->row->log->creator->picture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_PROFILE_PICTURE', $name); ?>" />
			</a>
		<?php } else { ?>
			<span class="img-link">
				<img src="<?php echo $this->row->log->creator->picture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_PROFILE_PICTURE', $name); ?>" />
			</span>
		<?php }*/ ?>
		<span class="activity-details">
			<span class="activity-actor">
				<?php if (in_array($creator->get('access'), User::getAuthorisedViewLevels())) { ?>
					<a href="<?php echo Route::url($creator->link()); ?>">
						<?php echo $name; ?>
					</a>
				<?php } else { ?>
					<?php echo $name; ?>
				<?php } ?>
			</span>
			<span class="activity-action"><?php echo $this->escape($this->row->log->get('action')); ?></span>
			<span class="activity-channel"><?php echo $this->escape($this->row->get('scope') . '.' . $this->row->get('scope_id')); ?></span>
			<span class="activity-context"><?php
				$scope = explode('.', $this->row->log->get('scope'));
				echo $this->escape($scope[0]);
				//echo $this->escape($this->row->log->get('scope') . '.' . $this->row->log->get('scope_id'));
			?></span>
			<span class="activity-time"><time datetime="<?php echo $this->row->get('created'); ?>"><?php
				$dt = Date::of($this->row->get('created'));
				$ct = Date::of('now');

				$lapsed = $ct->toUnix() - $dt->toUnix();

				if ($lapsed < 30)
				{
					echo Lang::txt('PLG_GROUPS_ACTIVITY_JUST_NOW');
				}
				elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y'))
				{
					echo $dt->toLocal('M j, Y');
				}
				elseif ($lapsed > 86400)
				{
					echo $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
				}
				else
				{
					echo $dt->relative();
				}
			?></time></span>
		</span>
		<span class="activity-event">
			<?php echo $this->row->log->get('description'); ?>
		</span>
	</div>

</li>