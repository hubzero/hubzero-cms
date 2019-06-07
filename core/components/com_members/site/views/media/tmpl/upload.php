<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$default = User::getInstance(0)->picture(0, false);

$picture = $this->profile->picture(0, false);
?>
<div id="ajax-upload-container">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" enctype="multipart/form-data">
		<h2><?php echo Lang::txt('Upload a New Profile Picture'); ?></h2>
		<div class="grid">
			<div class="col span6" id="ajax-upload-left">
				<img id="picture-src" src="<?php echo $picture; ?>" alt="" data-default-pic="<?php echo $this->escape($default); ?>" />
				<?php if ($this->profile->picture() != $default) : ?>
					<a href="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;id=<?php echo $this->profile->get('id'); ?>&amp;task=delete&amp;no_html=1&amp;<?php echo Session::getFormToken(); ?>=1" id="remove-picture"><?php echo Lang::txt('[Remove Picture]'); ?></a>
				<?php endif; ?>
			</div><!-- /#ajax-upload-left -->
			<div class="col span6 omega" id="ajax-upload-right">
				<div id="ajax-uploader" data-action="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;id=<?php echo $this->profile->get('id'); ?>&amp;task=doajaxupload&amp;no_html=1&amp;<?php echo Session::getFormToken(); ?>=1"></div>
			</div><!-- /#ajax-upload-right -->
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="ajaxuploadsave" />
		<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
		<input type="hidden" name="no_html" value="1" />

		<?php echo Html::input('token'); ?>
	</form>
</div><!-- /#ajax-upload-container -->