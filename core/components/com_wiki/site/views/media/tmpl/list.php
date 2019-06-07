<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);
$base = Request::base(true);

if (!$no_html) {
	Html::behavior('framework', true);

	$this->css()
		->js('media.js');
	?>
<?php } ?>
	<div id="attachments">
<?php if (!$no_html) { ?>
		<form action="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>" method="post" id="filelist">
<?php } ?>
<?php if (count($this->docs) == 0) { ?>
			<p><?php echo Lang::txt('COM_WIKI_ERROR_NO_FILES_FOUND'); ?></p>
<?php } else { ?>
			<table>
				<tbody>
				<?php
				if ($this->docs)
				{
					foreach ($this->docs as $path => $name)
					{
						$ext = Filesystem::extension($name);
						?>
						<tr>
							<td>
								<span><?php echo $this->escape(stripslashes($name)); ?></span>
							</td>
							<td>
								<a class="icon-delete delete" href="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=deletefile&amp;file=<?php echo $name; ?>&amp;listdir=<?php echo $this->listdir; ?>&amp;<?php echo (!$no_html) ? 'tmpl=component' : 'no_html=1'; ?>" <?php if (!$no_html) { ?>target="filer" data-confirm="<?php echo Lang::txt('Delete file %s?', $name); ?>"<?php } ?> title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
									<?php echo Lang::txt('JACTION_DELETE'); ?>
								</a>
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