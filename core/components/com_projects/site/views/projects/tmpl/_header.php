<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$privacyTxt = !$this->model->isPublic()
	? Lang::txt('COM_PROJECTS_PRIVATE')
	: Lang::txt('COM_PROJECTS_PUBLIC');

if (!$this->model->isPublic())
{
	$privacy = '<span class="private">' . ucfirst($privacyTxt) . '</span>';
}
else
{
	$privacy = '<a href="' . Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1') . '" title="' . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">' . ucfirst($privacyTxt) . '</a>';
}

$start = ($this->showPrivacy == 2 && $this->model->access('member')) ? '<span class="h-privacy">' . $privacy . '</span> ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));
?>
<div id="content-header" <?php if (!$this->showPic) { echo 'class="nopic"'; } ?>>
	<?php if ($this->showPic)
	{
		// Check if there is a picture
		$thumbClass = '';
		if (!$this->model->get('picture'))
		{
			$thumbClass = ' no-picture';
		}
		?>
		<div class="pthumb<?php echo $thumbClass; ?>"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" title="<?php echo Lang::txt('COM_PROJECTS_VIEW_UPDATES'); ?>"><img src="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&controller=media&media=thumb'); ?>" alt="<?php echo $this->escape($this->model->get('title')); ?>" /></a></div>
	<?php } ?>
	<div class="ptitle">
	<h2>
		<a href="<?php echo Route::url($this->model->link()); ?>"><?php echo \Hubzero\Utility\Str::truncate($this->escape($this->model->get('title')), 50); ?></a>
	</h2>
	<?php // Member options
	if (!empty($this->showOptions))
	{
		$this->view('_options', 'projects')
		     ->set('model', $this->model)
		     ->set('option', $this->option)
		     ->display();
	}
	?>
	<?php if ($this->model->groupOwner())
	{
		echo '<p class="groupowner">';
		echo ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));
		echo ' ' . Lang::txt('COM_PROJECTS_BY') . ' ';
		if ($cn = $this->model->groupOwner('cn'))
		{
			echo ' ' . Lang::txt('COM_PROJECTS_GROUP') . ' <a href="' . Route::url('index.php?option=com_groups&cn=' . $cn) . '">' . $cn . '</a>';
		}
		else
		{
			echo Lang::txt('COM_PROJECTS_UNKNOWN') . ' ' . Lang::txt('COM_PROJECTS_GROUP');
		}
		echo '</p>';
	 } ?>
	</div>
</div>
