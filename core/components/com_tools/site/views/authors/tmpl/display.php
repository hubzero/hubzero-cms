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

$this->css('component.css');

if ($this->version == 'dev') {
?>
	<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" id="authors-form" method="post" enctype="multipart/form-data">
		<fieldset>
		<?php if ($this->getError()) { ?>
			<p class="error">
				<?php echo implode('<br />', $this->getErrors()); ?>
			</p>
		<?php } ?>
			<div class="grid nobreak">
				<div class="col span7">
					<label for="acmembers">
						<?php echo Lang::txt('COM_TOOLS_AUTHORS_ENTER_LOGINS'); ?>
						<?php
						$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'new_authors', 'acmembers')));
						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?> <span class="hint"><?php echo Lang::txt('COM_TOOLS_ADD_AUTHORS_INSTRUCTIONS'); ?></span>
						<input type="text" name="new_authors" id="acmembers" value="" />
						<?php } ?>
					</label>
				</div>
				<div class="col span3">
					<label>
						<span id="new-authors-role-label"><?php echo Lang::txt('COM_TOOLS_AUTHORS_ROLE'); ?></span><br />
						<select name="role" id="new-authors-role">
							<option value=""><?php echo Lang::txt('COM_TOOLS_AUTHOR'); ?></option>
							<?php
							if ($this->roles)
							{
								foreach ($this->roles as $role)
								{
							?>
								<option value="<?php echo $this->escape($role->alias); ?>"><?php echo $this->escape($role->title); ?></option>
							<?php
								}
							}
							?>
						</select>
					</label>
				</div>
				<div class="col span2 omega">
					<p class="submit">
						<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_ADD'); ?>" />
					</p>
				</div>
			</div>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</form>
<?php } else { ?>
	<p class="warning"><?php echo Lang::txt('COM_TOOLS_AUTHORS_CANT_CHANGE'); ?></p>
<?php } ?>

<?php
// Do we have any contributors associated with this resource?
if ($this->contributors) {
	$i = 0;
	$n = count($this->contributors);
?>
	<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;tmpl=component" id="authors-list" method="post" enctype="multipart/form-data">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="task" value="update" />

		<table class="list">
			<tfoot>
				<td>
					<span class="caption">
						<?php echo Lang::txt('COM_TOOLS_AUTHORS_MUST_SAVE_CHANGES'); ?>
					</span>
				</td>
				<td>
					<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_SAVE_CHANGES'); ?>"/>
				</td>
				<td></td>
				<td></td>
				<td></td>
			</tfoot>
			<tbody>
			<?php
			foreach ($this->contributors as $contributor)
			{
				if ($contributor->lastname || $contributor->firstname)
				{
					$name  = stripslashes($contributor->firstname) . ' ';
					if ($contributor->middlename != NULL)
					{
						$name .= stripslashes($contributor->middlename) . ' ';
					}
					$name .= stripslashes($contributor->lastname);
				}
				else
				{
					$name  = stripslashes($contributor->name);
				}
			?>
				<tr>
					<td width="100%">
						<?php echo $this->escape($name); ?><br />
						<input type="text" name="authors[<?php echo $contributor->authorid; ?>][organization]" size="35" value="<?php echo $this->escape(stripslashes($contributor->org)); ?>" placeholder="<?php echo Lang::txt('COM_TOOLS_AUTHOR_ORGANIZATION'); ?>" />
					</td>
					<td>
						<select name="authors[<?php echo $contributor->authorid; ?>][role]" id="role-<?php echo $contributor->authorid; ?>">
							<option value=""<?php if ($contributor->role == '') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_TOOLS_AUTHOR'); ?></option>
							<?php
							if ($this->roles)
							{
								foreach ($this->roles as $role)
								{
									?>
									<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($contributor->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
									<?php
								}
							}
							?>
						</select>
					</td>
					<td class="u"><?php
						if ($this->version=='dev')
						{
							if ($i > 0 || ($i+0 > 0))
							{
								echo '<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$contributor->authorid.'&amp;task=reorder&amp;move=up" class="order up" title="'.Lang::txt('COM_TOOLS_MOVE_UP').'"><span>'.Lang::txt('COM_TOOLS_MOVE_UP').'</span></a>';
							}
							else
							{
								echo '&nbsp;';
							}
							?></td>
							<td class="d"><?php
							if ($i < $n-1 || $i+0 < $n-1)
							{
								echo '<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$contributor->authorid.'&amp;task=reorder&amp;move=down" class="order down" title="'.Lang::txt('COM_TOOLS_MOVE_DOWN').'"><span>'.Lang::txt('COM_TOOLS_MOVE_DOWN').'</span></a>';
							}
							else
							{
								echo '&nbsp;';
							}
						}
					?></td>
					<td class="t">
						<a class="icon-delete delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=remove&amp;tmpl=component&amp;id=<?php echo $contributor->authorid; ?>&amp;pid=<?php echo $this->id; ?>" title="<?php echo Lang::txt('COM_TOOLS_DELETE'); ?>">
							<span><?php echo Lang::txt('COM_TOOLS_DELETE'); ?></span>
						</a>
					</td>
				</tr>
			<?php
					$i++;
				}
			?>
			</tbody>
		</table>
	</form>
<?php } else { ?>
	<p><?php echo Lang::txt('COM_TOOLS_AUTHORS_NONE_FOUND'); ?></p>
<?php } ?>