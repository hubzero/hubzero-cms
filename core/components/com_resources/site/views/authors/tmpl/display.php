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

$this->css('create.css');
?>
<div id="small-page">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" id="authors-form" method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="grid">
				<div class="col span8">
					<label>
						<?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_ENTER_LOGINS'); ?>
						<?php
						$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'new_authors', 'acmembers')));
						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?> <span class="hint"><?php echo Lang::txt('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
						<input type="text" name="new_authors" id="acmembers" value="" />
						<?php } ?>
					</label>
				</div>
				<div class="col span2">
					<label for="new-authors-role">
						<span id="new-authors-role-label"><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_ROLE'); ?></span>
						<select name="role" id="new-authors-role">
							<option value=""><?php echo Lang::txt('COM_CONTRIBUTE_AUTHOR'); ?></option>
							<?php
							if ($this->roles)
							{
								foreach ($this->roles as $role)
								{
									?>
									<option value="<?php echo $this->escape($role->alias); ?>"><?php echo $this->escape(stripslashes($role->title)); ?></option>
									<?php
								}
							}
							?>
						</select>
					</label>
				</div>
				<div class="col span2 omega">
					<p class="submit">
						<input type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_ADD'); ?>" />
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
<?php
// Do we have any contributors associated with this resource?
if ($this->contributors) {
	$i = 0;
	$n = $this->contributors->count();
?>
	<form action="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=update&amp;tmpl=component" id="authors-list" method="post" enctype="multipart/form-data">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="task" value="update" />

		<table class="list">
			<tfoot>
				<td>
					<span class="caption">
						<?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_CHANGES_INSTRUCTIONS'); ?>
					</span>
				</td>
				<td>
					<input type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_SAVE'); ?>"/>
				</td>
				<td></td>
				<td></td>
				<td></td>
			</tfoot>
			<tbody>
			<?php
			foreach ($this->contributors as $contributor)
			{
				if (!$contributor->name)
				{
					$contributor->populateFromProfile();
				}

				$name = stripslashes($contributor->name);
				?>
				<tr class="author-<?php echo $contributor->authorid; ?>">
					<td>
						<span class="author-name"><?php echo $this->escape($name); ?></span><br />
						<input type="text" name="authors[<?php echo $contributor->authorid; ?>][organization]" id="organization-<?php echo $contributor->authorid; ?>" size="35" value="<?php echo $this->escape(stripslashes($contributor->organization)); ?>" placeholder="<?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_ORGANIZATION'); ?>" />
						<?php //echo ($contributor->org) ? ' <span class="caption">(' . $this->escape($contributor->org) . ')</span>' : ''; ?>
					</td>
					<td>
						<select name="authors[<?php echo $contributor->authorid; ?>][role]" id="role-<?php echo $contributor->authorid; ?>">
							<option value=""<?php if ($contributor->role == '') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_CONTRIBUTE_AUTHOR'); ?></option>
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
					if ($i > 0 || ($i+0 > 0)) {
						echo '<a href="' . Request::base(true) . '/index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$contributor->authorid.'&amp;task=reorder&amp;move=up" class="order up" title="'.Lang::txt('COM_CONTRIBUTE_MOVE_UP').'"><span>'.Lang::txt('COM_CONTRIBUTE_MOVE_UP').'</span></a>';
					} else {
						echo '&nbsp;';
					}
					?></td>
					<td class="d"><?php
					if ($i < $n-1 || $i+0 < $n-1) {
						echo '<a href="' . Request::base(true) . '/index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;tmpl=component&amp;pid='.$this->id.'&amp;id='.$contributor->authorid.'&amp;task=reorder&amp;move=down" class="order down" title="'.Lang::txt('COM_CONTRIBUTE_MOVE_DOWN').'"><span>'.Lang::txt('COM_CONTRIBUTE_MOVE_DOWN').'</span></a>';
					} else {
						echo '&nbsp;';
					}
					?></td>
					<td class="t">
						<a class="icon-delete delete" href="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=remove&amp;tmpl=component&amp;id=<?php echo $contributor->authorid; ?>&amp;pid=<?php echo $this->id; ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
							<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
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
	<p><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORS_NONE_FOUND'); ?></p>
<?php } ?>
 </div><!-- / #small-page -->