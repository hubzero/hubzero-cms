<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<h4><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_TEAM')); ?></h4>
<div id="cbody">
	<?php echo $this->content; ?>
</div>
<h5 class="terms-question">
	<?php echo Lang::txt('COM_PROJECTS_PROJECT') . ' ' . Lang::txt('COM_PROJECTS_OWNER'); ?>
	<?php if ($this->model->access('manager')): ?>
		<span class="mini">
			<a href="<?php echo Route::url($this->model->link('team') . '&action=changeowner'); ?>" class="showinbox">
				<?php echo ucfirst(Lang::txt('COM_PROJECTS_EDIT')); ?>
			</a>
		</span>
	<?php endif; ?>
</h5>
<?php if ($this->model->groupOwner() && $cn = $this->model->groupOwner('cn'))
{
	$ownedby = ucfirst(Lang::txt('COM_PROJECTS_GROUP')) . ' <a href="' . Route::url('index.php?option=com_groups&cn=' . $cn) . '">' . ' ' . $this->model->groupOwner('description') . ' (' . $cn . ')</a>';
}
else
{
	$ownedby = '<a href="' . Route::url('index.php?option=com_members&id=' . $this->model->owner('id')) . '">' . $this->model->owner('name') . '</a>';
}

echo '<span class="mini">' . $ownedby . '</span>';

