<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

$projects = $projctsorted;

$setup_complete = $this->pconfig->get('confirm_step', 0) ? 3 : 2;
?>

<li class="component-parent" id="myprojectsmini">
  <a class="component-button"><span class="nav-icon-groups"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/paper-airplane.svg") ?></span><span>My Projects</span><span class="nav-icon-more"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/chevron-right.svg") ?></span></a>
  <div class="component-panel">
    <header><h2>My Projects</h2></header>
    <a class="component-button"><span class="nav-icon-back"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/chevron-left.svg") ?></span>Back</a>
      <div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>

      <ul class="module-nav grouped">
	<?php if ($projects && $this->total > 0) { ?>
			<?php
			$i = 0;
			foreach ($projects as $row)
			{
				if ($i >= $this->limit)
				{
					break;
				}
				$goto  = 'alias=' . $row->alias;
				$owned_by = Lang::txt('MOD_MYPROJECTSMINI_BY') . ' ';
				if ($row->owned_by_group)
				{
					$owned_by .= '<strong>' . \Hubzero\Utility\Str::truncate($row->groupname, 20) . '</strong>';
				}
				elseif ($row->created_by_user == User::get('id'))
				{
					$owned_by .= Lang::txt('MOD_MYPROJECTSMINI_ME');
				}
				else
				{
					$owned_by .= '<strong>' . $row->authorname . '</strong>';
				}
				$role = $row->role == 1 ? Lang::txt('MOD_MYPROJECTSMINI_STATUS_MANAGER') : Lang::txt('MOD_MYPROJECTSMINI_STATUS_COLLABORATOR');
				$setup = ($row->setup_stage < $setup_complete) ? Lang::txt('MOD_MYPROJECTSMINI_STATUS_SETUP') : '';

				$class = '';
				if ($row->state == 1 && $row->setup_stage >= $setup_complete)
				{
					$class = "pr-active";
				}
				elseif ($row->setup_stage < $setup_complete)
				{
					$class = "pr-setup";
				}
				elseif ($row->state == 0)
				{
					$class = "pr-inactive";
				}
				$class = $class ? ' class="' . $class . '"' : '';

				$i++;
        
        require $this->getLayoutPath('_item');
			}
			?>
	<?php } else { ?>
		<p><em><?php echo Lang::txt('MOD_MYPROJECTSMINI_NO_PROJECTS'); ?></em></p>
	<?php } ?>

	<?php if ($this->total > $this->limit) { ?>
		<li class="note">
			<?php echo Lang::txt('MOD_MYPROJECTSMINI_YOU_HAVE_MORE', $this->limit, $this->total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=projects')); ?>
		</li>
	<?php } ?>

  <?php if ($this->params->get('button_show_all', 1) || $this->params->get('button_show_add', 1)) { ?>
		<?php if ($this->params->get('button_show_all', 1)) { ?>
		<li>
			<a class="icon-browse" href="<?php echo Route::url('index.php?option=com_projects&task=browse'); ?>">
				<?php echo Lang::txt('MOD_MYPROJECTSMINI_ALL_PROJECTS'); ?>
			</a>
		</li>
		<?php } ?>
		<?php if ($this->params->get('button_show_add', 1)) { ?>
		<li>
			<a class="icon-plus" href="<?php echo Route::url('index.php?option=com_projects&task=start'); ?>">
				<?php echo Lang::txt('MOD_MYPROJECTSMINI_NEW_PROJECT'); ?>
			</a>
		</li>
		<?php } ?>
	<?php } ?>
  </ul>

  </div>
  </div>
</li>
