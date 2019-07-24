<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->css('media.css')
	->js('media.js');

$attachments = $this->post->attachments;
?>

<div id="attachments">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&id=' . $this->post->get('id')); ?>" method="post" id="filelist">
		<?php if (count($attachments) == 0) { ?>
			<p><?php echo Lang::txt('COM_FORUM_NO_FILES_FOUND'); ?></p>
		<?php } else { ?>
			<table>
				<tbody>
				<?php foreach ($attachments as $k => $attachment) { ?>
					<tr>
						<td width="100%">
							<a download="download" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&id=' . $this->post->get('id') . '&task=download&attachment=' . $attachment->get('id') . '&' . Session::getFormToken() . '=1'); ?>" class="icon-file file <?php echo Filesystem::extension($attachment->get('filename')); ?>">
								<?php echo $this->escape(trim($attachment->get('filename'), DS)); ?>
							</a>
						</td>
						<td>
							<a class="icon-delete delete deletefile"
								target="media"
								href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=delete&attachment=' . $attachment->get('id') . '&id=' . $this->post->get('id') . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>"
								data-file="<?php echo $attachment->get('filename'); ?>"
								data-confirm="<?php echo Lang::txt('COM_FORUM_MEDIA_DELETE_FILE', $attachment->get('filename')); ?>"
								title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
							</a>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php } ?>

		<?php echo Html::input('token'); ?>
	</form>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
</div>