<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
					if ($contributor->middlename != null)
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
						<input type="text" name="authors[<?php echo isset($contributor->authorid) ? $contributor->authorid : "";  ?>][organization]" size="35" value="<?php echo $this->escape(stripslashes($contributor->organization)); ?>" placeholder="<?php echo Lang::txt('COM_TOOLS_AUTHOR_ORGANIZATION'); ?>" />
					</td>
					<td>
						<select name="authors[<?php echo isset($contributor->authorid) ? $contributor->authorid : "";  ?>][role]" id="role-<?php echo isset($contributor->authorid) ? $contributor->authorid : "";  ?>">
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
						<a class="icon-delete delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=remove&amp;tmpl=component&amp;id=<?php echo isset($contributor->authorid) ? $contributor->authorid : "";  ?>&amp;pid=<?php echo $this->id; ?>" title="<?php echo Lang::txt('COM_TOOLS_DELETE'); ?>">
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
<?php } 
