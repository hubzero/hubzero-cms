<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Lang::load('plg_members_messages', PATH_CORE . '/plugins/members/messages');

$canDo = (User::authorise('core.admin', $this->option) || User::authorise('core.edit', $this->option));
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_NO_COMPONENTS_FOUND'); ?></p>
<?php } else { ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=savesettings'); ?>" method="post" name="adminForm" id="item-form">
		<table class="settings">
			<?php if ($canDo) { ?>
			<caption>
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
			</caption>
			<?php } ?>
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SENT_WHEN'); ?></th>
					<?php foreach ($this->notimethods as $notimethod) { ?>
						<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)); ?></th>
					<?php } ?>
				</tr>
			</thead>
			<?php if ($canDo) { ?>
			<tfoot>
				<tr>
					<td colspan="<?php echo (count($this->notimethods) + 1); ?>">
						<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
					</td>
				</tr>
			</tfoot>
			<?php } ?>
			<tbody>
			<?php
			$cls = 'even';

			$sheader = '';
			foreach ($this->components as $component)
			{
				if ($component->name != $sheader)
				{
					$sheader = $component->name;
					Lang::load($component->name, Component::path($component->name) . '/site');

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
					<?php echo \Components\Members\Admin\Controllers\Messages::selectMethod($this->notimethods, $component->action, $this->settings[$component->action]['methods'], $this->settings[$component->action]['ids']); ?>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="savesettings" />
		<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
		<input type="hidden" name="tmpl" value="<?php echo Request::getWord('tmpl'); ?>" />

		<?php echo Html::input('token'); ?>
	</form>
<?php } 