<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

?>
	<div id="attachments">
		<form action="<?php echo Request::base(true); ?>/index.php" id="adminForm" method="post" enctype="multipart/form-data">
			<fieldset>
				<div id="themanager" class="manager">
					<iframe src="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;controller=media&amp;task=list&amp;listdir=<?php echo $this->listdir; ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
				</div>
			</fieldset>

			<fieldset>
				<table>
					<tbody>
						<tr>
							<td><input type="file" name="upload" id="upload" /></td>
						</tr>
						<tr>
							<td><input type="submit" value="<?php echo Lang::txt('COM_WIKI_UPLOAD'); ?>" /></td>
						</tr>
					</tbody>
				</table>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
				<input type="hidden" name="task" value="upload" />
				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			</fieldset>
		</form>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } 