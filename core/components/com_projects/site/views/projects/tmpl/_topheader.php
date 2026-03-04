<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="project-header" class="project-header">
	<div class="grid">
		<div class="col span10">
			<div class="pimage-container">
				<?php
				// Draw image
				$this->view('_image', 'projects')
				     ->set('model', $this->model)
				     ->set('option', $this->option)
				     ->display();
				?>
			</div>
			<div class="ptitle-container">
				<h2><?php echo \Hubzero\Utility\Str::truncate($this->escape($this->model->get('title')), 50); ?> <span>(<?php echo $this->model->get('alias'); ?>)</span></h2>

				<?php if ($this->model->groupOwner()) { ?>
					<p>
						<?php
						if (!$this->model->isPublic())
						{
							$privacy = '<span class="private">' . ucfirst(Lang::txt('COM_PROJECTS_PRIVATE')) . '</span>';
						}
						else
						{
							$privacy = '<a href="' . Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1') .'" title="' . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">' . ucfirst(Lang::txt('COM_PROJECTS_PUBLIC')) . '</a>';
						}

						$start = ($this->publicView == false && $this->model->access('member')) ? '<span class="h-privacy">' . $privacy . '</span> ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));

						echo $start . ' ' . Lang::txt('COM_PROJECTS_BY') . ' ';
						if ($cn = $this->model->groupOwner('cn'))
						{
							echo ' ' . Lang::txt('COM_PROJECTS_GROUP') . ' <a href="' . Route::url('index.php?option=com_groups&cn=' . $cn) . '">' . $cn . '</a>';
						}
						else
						{
							echo Lang::txt('COM_PROJECTS_UNKNOWN') . ' ' . Lang::txt('COM_PROJECTS_GROUP');
						}
						?>
					</p>
				<?php } ?>
			</div>
		</div>
		<div class="col span2 omega">
			<?php
			// Member options
			if ($this->publicView == false)
			{
				$this->view('_options', 'projects')
				     ->set('model', $this->model)
				     ->set('option', $this->option)
				     ->display();
			}
			else
			{
				?>
				<ul id="useroptions">
					<li><a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('COM_PROJECTS_ALL_PROJECTS'); ?></a></li>
					<?php if (User::authorise('core.create', $this->option)) { ?>
						<li><a class="btn icon-add" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=start'); ?>"><?php echo Lang::txt('COM_PROJECTS_START_NEW'); ?></a></li>
					<?php } ?>
				</ul>
				<?php
			}
			?>
		</div>
		<div class="clear"></div>
	</div>
</div>
