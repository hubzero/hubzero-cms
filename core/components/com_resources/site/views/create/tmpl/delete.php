<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('create.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('New submission'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps', 'steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();
	?>

	<?php if ($this->getError()) { ?>
		<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>

	<form name="hubForm" id="hubForm" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=discard&step=2&id=' . $this->row->id); ?>" class="contrib">
		<div class="explaination">
			<p class="warning"><?php echo Lang::txt('COM_CONTRIBUTE_DELETE_WARNING'); ?><p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_DELETE_LEGEND'); ?></legend>
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="discard" />
			<input type="hidden" name="step" value="2" />

			<p><strong><?php echo $this->escape(stripslashes($this->row->title)); ?></strong><br />
			<?php echo $this->escape(stripslashes($this->row->typetitle)); ?></p>

			<label><input type="checkbox" name="confirm" value="confirmed" class="option" /> <?php echo Lang::txt('COM_CONTRIBUTE_DELETE_CONFIRM'); ?></label>
		</fieldset><div class="clear"></div>

		<div class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_DELETE'); ?>" />
		</div>
	</form>
</section><!-- / .main section -->
