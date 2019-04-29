<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);

$section = Request::getInt('section_id', 0);

$base = $this->offering->link() . '&active=pages';

if (!$no_html) { ?>
	<script type="text/javascript">
		function updateDir()
		{
			var allPaths = window.top.document.forms[0].dirPath.options;
			for (i=0; i<allPaths.length; i++)
			{
				allPaths.item(i).selected = false;
				if ((allPaths.item(i).value)== '<?php echo (isset($this->listdir)) ? $this->listdir : '/'; ?>') {
					allPaths.item(i).selected = true;
				}
			}
		}
		function deleteFile(file)
		{
			if (confirm("Delete file \""+file+"\"?")) {
				return true;
			}

			return false;
		}
		function deleteFolder(folder, numFiles)
		{
			if (numFiles > 0) {
				alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
				return false;
			}

			if (confirm('Delete folder "'+folder+'"?')) {
				return true;
			}

			return false;
		}
	</script>
<?php } ?>

<div id="attachments">
	<?php if (!$no_html) { ?>
		<form action="<?php echo Route::url($base); ?>" method="post" id="filelist">
	<?php } ?>
	<?php if (count($this->docs) == 0) { ?>
			<p><?php echo Lang::txt('PLG_COURSES_PAGES_NO_FILES_FOUND'); ?></p>
	<?php } else { ?>
			<table>
				<tbody>
				<?php
				if ($this->docs)
				{
					$page = Request::getString('page', '');

					foreach ($this->docs as $path => $name)
					{
						$ext = Filesystem::extension($name);
						$type = (in_array($ext, array('jpg', 'jpe', 'jpeg', 'gif', 'png')) ? 'Image' : 'File');
				?>
					<tr class="row-group start">
						<td width="100%">
							<span><?php echo $this->escape(stripslashes($name)); ?></span>
						</td>
						<td>
							<a class="delete" href="<?php echo Route::url($base . '&action=remove&file=' . urlencode(stripslashes($name)) . '&' . (!$no_html ? 'tmpl=component' : 'no_html=1') . '&section_id=' . $section); ?>" <?php if (!$no_html) { ?>target="filer" onclick="return deleteFile('<?php echo $this->escape($name); ?>');"<?php } ?> title="<?php echo Lang::txt('PLG_COURSES_PAGES_DELETE'); ?>">
								<?php echo Lang::txt('PLG_COURSES_PAGES_DELETE'); ?>
							</a>
						</td>
					</tr>
					<tr class="row-group end">
						<td colspan="2">
							<span class="file-path"><?php echo Route::url($base . '&unit=download&b=' . $type . ':' . $this->escape(stripslashes($name))); ?></span>
						</td>
					</tr>
				<?php
					}
				}
				?>
				</tbody>
			</table>
	<?php } ?>
	<?php if (!$no_html) { ?>
		</form>
	<?php } ?>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</div>