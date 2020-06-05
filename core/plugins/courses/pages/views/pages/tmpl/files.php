<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->offering->link() . '&active=pages';
?>
<div id="attachments">
	<form action="<?php echo Route::url($base); ?>" id="adminForm" method="post" enctype="multipart/form-data">
		<fieldset>
			<div id="themanager" class="manager">
				<iframe src="<?php echo Route::url($base . '&action=list&tmpl=component&page=' . $this->page->get('id') . '&section_id=' . $this->page->get('section_id')); ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
			</div>
		</fieldset>

		<fieldset>
			<table>
				<tbody>
					<tr>
						<td><input type="file" name="upload" id="upload" /></td>
					</tr>
					<tr>
						<td><input type="submit" value="<?php echo Lang::txt('PLG_COURSES_PAGES_UPLOAD'); ?>" /></td>
					</tr>
				</tbody>
			</table>

			<?php echo Html::input('token'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
			<input type="hidden" name="page" value="<?php echo $this->escape($this->page->get('id')); ?>" />
			<input type="hidden" name="section_id" value="<?php echo $this->escape($this->page->get('section_id')); ?>" />
			<input type="hidden" name="active" value="pages" />
			<input type="hidden" name="action" value="upload" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
		</fieldset>
	</form>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</div>