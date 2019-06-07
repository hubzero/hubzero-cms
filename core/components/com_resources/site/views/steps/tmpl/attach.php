<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(true), '/');

$this->css('create.css')
	->css('autocompleter.css')
	->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();
	?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
			<p><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_EXPLANATION'); ?></p>
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS'); ?></h4>
			<p>
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC_EXPLANATION'); ?><br />
				<strong><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED'); ?></strong> = <?php echo Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED_EXPLANATION'); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_ATTACHMENTS'); ?></legend>
			<div class="field-wrap">
				<div class="asset-uploader">
					<div class="grid">
						<?php if (!$this->row->type->get('collection')): ?>
							<?php
							$this->js('jquery.fileuploader.js', 'system')
								->js('fileupload.js');
							echo $this->loadTemplate('fileuploader');
							?>
						<?php else: ?>
							<?php
							$this->js('addchild.js');
							echo $this->loadTemplate('addchild');
							?>
						<?php endif; ?>
					</div>
					<iframe width="100%" height="500" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $this->option; ?>&amp;controller=attachments&amp;id=<?php echo $this->id; ?>&amp;tmpl=component"></iframe>
				</div><!-- / .asset-uploader -->
			</div><!-- / .field-wrap -->
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		</fieldset><div class="clear"></div>
		<div class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_NEXT'); ?>" />
		</div>
	</form>
</section><!-- / .main section -->
