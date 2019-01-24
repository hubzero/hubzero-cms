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

// No direct access
defined('_HZEXEC_') or die();
?>
<script type="text/javascript">
	function deleteFile(file)
	{
		if (confirm("Delete file \""+file+"\"?")) {
			return true;
		}

		return false;
	}
</script>
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
								<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=deletefile&delFile=' . $docs[$docName] . '&listdir=' . $this->listdir . '&tmpl=component&subdir=' . $this->subdir . '&course=' . $this->course_id . '&' . Session::getFormToken() . '=1'); ?>" target="filelist" onclick="return deleteFile('<?php echo $docs[$docName]; ?>');" title="<?php echo Lang::txt('DELETE'); ?>">
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