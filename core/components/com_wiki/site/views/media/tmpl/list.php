<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);
$base = Request::base(true);

if (!$no_html) {
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