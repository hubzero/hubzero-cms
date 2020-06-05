<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->js('media.js');
?>
<div id="attachments">
	<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" id="filelist" name="filelist">
		<table>
			<tbody>
				<?php if (count($this->docs) == 0) { ?>
					<tr>
						<td>
							<?php echo Lang::txt('No files found.'); ?>
						</td>
					</tr>
				<?php
				} else {
					$docs = $this->docs;
					for ($i=0; $i<count($docs); $i++)
					{
						$docName = key($docs);

						$subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
						?>
						<tr>
							<td>
								<?php echo $docs[$docName]; ?>
							</td>
							<td>
								<a class="delete-file" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=deletefile&delFile=' . $docs[$docName] . '&listdir=' . $this->listdir . '&tmpl=component&subdir=' . $this->subdir . '&course=' . $this->course_id . '&' . Session::getFormToken() . '=1'); ?>" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the file "%s"?', $docs[$docName]); ?>" title="<?php echo Lang::txt('DELETE'); ?>">
									<img src="<?php echo Request::base(true); ?>/core/components/<?php echo $this->option; ?>/admin/assets/img/trash.png" width="15" height="15" alt="<?php echo Lang::txt('DELETE'); ?>" />
								</a>
							</td>
						</tr>
						<?php
						next($docs);
					}
				}
				?>
			</tbody>
		</table>
	</form>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</div>