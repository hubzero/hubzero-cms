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

$this->css('create.css')
     ->js('create.js');

$hideform = Request::getInt('hideform', 0);
?>
	<div id="small-page">
		<?php if (!$hideform) { ?>
		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<label>
					<input type="file" class="option" name="upload" />
				</label>
				<input type="submit" class="option" value="<?php echo Lang::txt('COM_CONTRIBUTE_UPLOAD'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</form>
		<?php } ?>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>

		<?php
		$out = '';
		// loop through children and build list
		if ($this->children)
		{
			$base = $this->config->get('uploadpath');

			$k = 0;
			$i = 0;
			$files = array(13,15,26,33,35,38);
			$n = count($this->children);
		?>
		<p><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_EDIT_TITLE_EXPLANATION'); ?></p>
		<table class="list">
			<thead>
				<tr>
					<th><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_ATTACHMENTS'); ?></th>
					<th><?php echo Lang::txt('COM_CONTRIBUTE_ACCESS'); ?></th>
					<th colspan="2"><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_ORDER'); ?></th>
					<th><?php echo Lang::txt('JACTION_DELETE'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->children as $child)
			{
				$k++;

				// figure ou the URL to the file
				switch ($child->get('type'))
				{
					case 12:
						if ($child->path)
						{
							// internal link, not a resource
							$url = $child->path;
						}
						else
						{
							// internal link but a resource
							$url = '/index.php?option=com_resources&id=' . $child->id;
						}
						break;
					default:
						$url = $child->path;
						break;
				}

				// figure out the file type so we can give it the appropriate CSS class
				$type = Filesystem::extension($url);
				if (!$child->get('type') != 12 && $child->get('type') != 11)
				{
					$type = ($type) ? $type : 'html';
				}

				$isFile = true;
				if (($child->get('type') == 12 || $child->get('type') == 11)
				 || in_array($type, array('html', 'htm', 'php', 'asp', 'shtml'))
				 || strstr($url, '?'))
				{
					$isFile = false;
				}
				?>
				<tr>
					<td width="100%">
						<span class="ftitle item:name id:<?php echo $child->id; ?>" data-id="<?php echo $child->id; ?>">
							<?php echo $this->escape($child->title); ?>
						</span>
						<?php echo ($isFile) ? \Components\Resources\Helpers\Html::getFileAttribs($url, $base) : '<span class="caption">' . $url . '</span>'; ?>
					</td>
					<td>
						<?php
						$cs = ($child->access == 1 ? 'registered' : 'public');
						$st = ($child->access == 1 ? 0 : 1);
						?>
						<a class="access-<?php echo $cs; ?> access" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=access&amp;access=<?php echo $st; ?>&amp;tmpl=component&amp;id=<?php echo $child->id; ?>&amp;pid=<?php echo $this->id; ?>&amp;hideform=<?php echo $hideform; ?>" title="<?php echo Lang::txt('COM_CONTRIBUTE_SET_ACCESS_TO', Lang::txt($child->access == 1 ? 'COM_CONTRIBUTE_ACCESS_PUBLIC' : 'COM_CONTRIBUTE_ACCESS_REGISTERED')); ?>">
							<span><?php echo Lang::txt($child->access == 1 ? 'COM_CONTRIBUTE_ACCESS_REGISTERED' : 'COM_CONTRIBUTE_ACCESS_PUBLIC'); ?></span>
						</a>
					</td>
					<td class="u">
						<?php
						if ($i > 0 || ($i+0 > 0)) {
							echo '<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=up&amp;hideform=' . $hideform . '" class="order up" title="'.Lang::txt('COM_CONTRIBUTE_MOVE_UP').'"><span>'.Lang::txt('COM_CONTRIBUTE_MOVE_UP').'</span></a>';
						} else {
							echo '&nbsp;';
						}
						?>
					</td>
					<td class="d">
						<?php
						if ($i < $n-1 || $i+0 < $n-1) {
							echo '<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$child->id.'&amp;task=reorder&amp;move=down&amp;hideform=' . $hideform . '" class="order down" title="'.Lang::txt('COM_CONTRIBUTE_MOVE_DOWN').'"><span>'.Lang::txt('COM_CONTRIBUTE_MOVE_DOWN').'</span></a>';
						} else {
							echo '&nbsp;';
						}
						?>
					</td>
					<td class="t">
						<a class="icon-delete delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=delete&amp;tmpl=component&amp;id=<?php echo $child->id; ?>&amp;pid=<?php echo $this->id; ?>&amp;hideform=<?php echo $hideform; ?>">
							<span><?php echo Lang::txt('COM_CONTRIBUTE_DELETE'); ?></span>
						</a>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</tbody>
		</table>
	<?php } else { ?>
		<p><?php echo Lang::txt('COM_CONTRIBUTE_ATTACH_NONE_FOUND'); ?></p>
	<?php } ?>
	</div>