<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('component.css');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<label for="upload">
			<input type="file" class="option" name="upload" id="upload" />
			<input type="submit" class="option" value="<?php echo strtolower(Lang::txt('COM_CONTRIBUTE_UPLOAD')); ?>" />
		</label>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="resource" value="<?php echo $this->resource; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>

	<?php if ($this->getError()) { ?>
		<p class="error">
			<?php echo implode('<br />', $this->getErrors()); ?>
		</p>
	<?php } ?>

	<?php if (count($this->folders) == 0 && count($this->docs) == 0) { ?>
		<p><?php echo Lang::txt('COM_CONTRIBUTE_MEDIA_EXPLANATION'); ?></p>
	<?php } else { ?>
		<table>
			<tbody>
			<?php
			$docs = $this->docs;
			for ($i=0; $i<count($docs); $i++)
			{
				$docName = key($docs);

				$subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
			?>
				<tr>
					<td width="100%">
						<?php echo Route::url('index.php?option=com_resources&id=' . ($this->row->alias ? $this->row->alias : $this->resource) . '&task=download&file=' . $docs[$docName]); ?>
					</td>
					<td>
						<a class="icon-delete delete" href="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=delete&amp;file=<?php echo $docs[$docName]; ?>&amp;resource=<?php echo $this->resource; ?>&amp;tmpl=component&amp;subdir=<?php echo $this->subdir; ?>&amp;<?php echo Session::getFormToken(); ?>=1" target="filer" onclick="return deleteFile('<?php echo $docs[$docName]; ?>');" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
							<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
						</a>
					</td>
				</tr>
			<?php
				next($docs);
			}
			?>
			</tbody>
		</table>
	<?php } ?>

	<?php echo Html::input('token'); ?>
</form>
