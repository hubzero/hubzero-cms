<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$nextstep = $this->step+1;
$task = ($nextstep == 5) ? 'preview' : 'start';
$dev = ($this->version == 'dev') ? 1: 0;

if ($this->version == 'dev') {
	$v = ($this->status['version'] && $this->status['version'] != $this->status['currentversion']) ? $this->status['version'] : '';
}
else {
	$v = $this->status['version'];
}

$this->css('resource.css')
     ->js('resource.js');
?>
<header id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-status btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pipeline&task=status&app=' . $this->row->alias); ?>"><?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li><a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pipeline&task=create'); ?>"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="section steps-section">
	<?php
	$this->view('stage')
	     ->set('stage', $this->step)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('version', $this->version)
	     ->set('row', $this->row)
	     ->set('status', $this->status)
	     ->set('vnum', $v)
	     ->display();
	?>
</section>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

		<div class="steps-nav">
			<?php if ($this->step !=1) { ?>
				<span class="step-prev"><input type="button" value="&lt; <?php echo ucfirst(Lang::txt('COM_TOOLS_PREVIOUS')); ?>" class="btn returntoedit" /></span>
			<?php } ?>
			<span class="step-next"><input type="submit" value="<?php echo ucfirst(Lang::txt('COM_TOOLS_SAVE_AND_GO_NEXT')); ?>" class="btn" /></span>
		</div>

		<?php
			switch ($this->step)
			{
				//  registered
				case 1:
					$layout = 'compose';
				break;
				case 2:
					$layout = 'authors';
				break;
				case 3:
					$layout = 'attach';
				break;
				case 4:
					$layout = 'tags';
				break;
			}
			$this->view($layout)
			     ->set('step', $this->step)
			     ->set('option', $this->option)
			     ->set('controller', $this->controller)
			     ->set('version', $this->version)
			     ->set('row', $this->row)
			     ->set('status', $this->status)
			     ->set('dev', $dev)
			     ->set('tags', $this->tags)
			     ->set('tagfa', $this->tagfa)
			     ->set('fats', $this->fats)
			     ->set('authors', $this->authors)
			     ->display();
		?>

		<input type="hidden" name="app" value="<?php echo $this->row->alias; ?>" />
		<input type="hidden" name="rid" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $task; ?>" />
		<input type="hidden" name="step" value="<?php echo $nextstep; ?>" />
		<input type="hidden" name="editversion" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="toolname" value="<?php echo $this->status['toolname']; ?>" />
		<?php echo Html::input('token'); ?>

		<div class="steps-nav">
			<?php if ($this->step != 1) { ?>
				<span class="step-prev"><input type="button" value="&lt; <?php echo ucfirst(Lang::txt('COM_TOOLS_PREVIOUS')); ?>" class="btn returntoedit" /></span>
			<?php } ?>
			<span class="step-next"><input type="submit" value="<?php echo ucfirst(Lang::txt('COM_TOOLS_SAVE_AND_GO_NEXT')); ?>" class="btn" /></span>
		</div>
	</form>
</section>