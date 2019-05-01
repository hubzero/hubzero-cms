<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$role  = Lang::txt('COM_PROJECTS_PROJECT') . ' <span>';
if ($this->model->access('manager'))
{
	$role .= Lang::txt('COM_PROJECTS_LABEL_OWNER');
}
elseif (!$this->model->access('content'))
{
	$role .= Lang::txt('COM_PROJECTS_LABEL_REVIEWER');
}
else
{
	$role .= Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
}
$role .= '</span>';

$counts = $this->model->get('counts');

$member = $this->model->member();
?>
<ul id="member_options">
	<li><?php echo ucfirst($role); ?>
		<div id="options-dock">
			<div><p><?php echo Lang::txt('COM_PROJECTS_JOINED') . ' ' . $this->model->created('date'); ?></p>
				<ul>
		<?php if ($this->model->access('manager')) { ?>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=edit'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT'); ?></a></li>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=edit&active=team'); ?>"><?php echo Lang::txt('COM_PROJECTS_INVITE_PEOPLE'); ?></a></li>
		<?php } ?>
		<?php if ($this->model->isPublic()) { ?>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1'); ?>"><?php echo Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'); ?></a></li>
		<?php } ?>
		<?php if (isset($counts['team']) && $counts['team'] > 1 && $member && $member->get('status') == 1) { ?>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=team&action=quit'); ?>"><?php echo Lang::txt('COM_PROJECTS_LEAVE_PROJECT'); ?></a></li>
		<?php } ?>
				</ul>
			</div>
		</div>
	</li>
</ul>
