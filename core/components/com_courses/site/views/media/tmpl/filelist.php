<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->css('media.css');
$this->js('media.js');

$base = rtrim(Request::base(true), '/');

$course = \Components\Courses\Models\Course::getInstance($this->listdir);
?>

<div id="file_list">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="filelist">
		<?php if (count($this->images) == 0 && count($this->folders) == 0 && count($this->docs) == 0) { ?>
			<p><?php echo Lang::txt('COM_COURSES_NO_FILES_FOUND'); ?></p>
		<?php } else { ?>
			<table>
				<tbody>
				<?php
				$folders = $this->folders;
				for ($i=0; $i<count($folders); $i++)
				{
					$folder_name = key($folders);

					$num_files = 0;
					if (is_dir(PATH_APP.DS.$folders[$folder_name]))
					{
						$d = @dir(PATH_APP.DS.$folders[$folder_name]);

						while (false !== ($entry = $d->read()))
						{
							if (substr($entry, 0, 1) != '.')
							{
								$num_files++;
							}
						}
						$d->close();
					}

					if ($this->listdir == '/')
					{
						$this->listdir = '';
					}
					?>
					<tr>
						<td width="100%" colspan="2">
							<span class="icon-folder"><?php echo $folder_name; ?></span>
						</td>
						<td>
							<a class="icon-delete delete delete-folder" href="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;task=deletefolder&amp;folder=<?php echo DS.$folders[$folder_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1" target="filer" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the folder "%s"?', $folder_name); ?>" data-files="<?php echo $num_files; ?>" data-notempty="<?php echo Lang::txt('There are %s files/folders in this folder. Please delete all files/folder first.', $num_files); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<?php echo Lang::txt('JACTION_DELETE'); ?>
							</a>
						</td>
					</tr>
					<?php
					next($folders);
				}

				$docs = $this->docs;
				for ($i=0; $i<count($docs); $i++)
				{
					$doc_name = key($docs);
					$iconfile = $this->config->get('iconpath').DS.substr($doc_name, -3).'.png';

					if (file_exists(PATH_APP.$iconfile))
					{
						$icon = $iconfile;
					}
					else
					{
						$icon = $this->config->get('iconpath').DS.'unknown.png';
					}
					?>
					<tr>
						<td width="100%">
							<span class="icon-file"><?php echo $docs[$doc_name]; ?></span>
						</td>
						<td>
							<?php if (is_object($course)) : ?>
								<a href="#" class="icon-path filepath" data-path="<?php echo 'https://'.$_SERVER['HTTP_HOST'].DS.'courses'.DS.$course->get('cn').DS.'File:'.$docs[$doc_name]; ?>" title="<?php echo Lang::txt('COM_COURSES_SHOW_FILE_PATH'); ?>">
									<?php echo Lang::txt('COM_COURSES_SHOW_FILE_PATH'); ?>
								</a>
							<?php endif; ?>
						</td>
						<td>
							<a class="icon-delete delete delete-file" href="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;task=deletefile&amp;file=<?php echo $docs[$doc_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1" target="filer" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the file "%s"?', $docs[$doc_name]); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<?php echo Lang::txt('JACTION_DELETE'); ?>
							</a>
						</td>
					</tr>
					<?php
					next($docs);
				}

				$images = $this->images;
				for ($i=0; $i<count($images); $i++)
				{
					$image_name = key($images);
					?>
					<tr>
						<td width="100%">
							<span class="icon-image"><?php echo $images[$image_name]; ?></span>
						</td>
						<td>
							<?php if (is_object($course)) : ?>
								<a href="#" class="icon-path filepath" data-path="<?php echo 'https://'.$_SERVER['HTTP_HOST'].DS.'courses'.DS.$course->get('cn').DS.'Image:'.$images[$image_name]; ?>" title="<?php echo Lang::txt('COM_COURSES_SHOW_FILE_PATH'); ?>">
									<?php echo Lang::txt('COM_COURSES_SHOW_FILE_PATH'); ?>
								</a>
							<?php endif; ?>
						</td>
						<td>
							<a class="icon-delete delete delete-file" href="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;task=deletefile&amp;file=<?php echo $images[$image_name]; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1" target="filer" data-confirm="<?php echo Lang::txt('Are you sure you want to delete the folder "%s"?', $images[$image_name]); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
								<?php echo Lang::txt('JACTION_DELETE'); ?>
							</a>
						</td>
					</tr>
					<?php
					next($images);
				}
				?>
				</tbody>
			</table>
		<?php } ?>
	</form>
</div>
