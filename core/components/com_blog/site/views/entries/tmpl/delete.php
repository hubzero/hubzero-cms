<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (Pathway::count() <= 0)
{
	Pathway::append(
		Lang::txt('COM_BLOG'),
		'index.php?option=' . $this->option
	);
}
Pathway::append(
	Lang::txt('COM_BLOG_DELETE'),
	$this->entry->link('delete')
);

Document::setTitle(Lang::txt('COM_BLOG') . ': ' . Lang::txt('JACTION_DELETE'));

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_BLOG') . ': ' . Lang::txt('JACTION_DELETE'); ?></h2>

	<div id="content-header-extra">
		<p><a class="icon-archive archive btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_BLOG_ARCHIVE'); ?></a></p>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<div class="section-inner">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<form action="<?php echo Route::url($this->entry->link('delete')); ?>" method="post" id="hubForm">
			<div class="explaination">
			<?php if ($this->config->get('access-create-entry')) { ?>
				<p>
					<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>"><?php echo Lang::txt('COM_BLOG_NEW_ENTRY'); ?></a>
				</p>
			<?php } ?>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_BLOG_DELETE_HEADER'); ?></legend>

				<p class="warning">
					<?php echo Lang::txt('COM_BLOG_DELETE_WARNING', $this->escape(stripslashes($this->entry->get('title')))); ?>
				</p>

				<div class="form-group form-check">
					<label for="confirmdel" class="form-check-label">
						<input type="checkbox" class="option form-check-input" name="confirmdel" id="confirmdel" value="1" />
						<?php echo Lang::txt('COM_BLOG_DELETE_CONFIRM'); ?>
					</label>
				</div>
			</fieldset>
			<div class="clear"></div>

			<input type="hidden" name="id" value="<?php echo $this->entry->get('id'); ?>" />
			<input type="hidden" name="task" value="delete" />
			<input type="hidden" name="process" value="1" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

			<?php echo Html::input('token'); ?>

			<p class="submit">
				<input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('JACTION_DELETE'); ?>" />
				<a class="btn btn-secondary" href="<?php echo $this->entry->link(); ?>">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			</p>
		</form>
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
