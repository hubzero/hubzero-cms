<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = TagsHelper::getActions();

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('TAGS') . ': <small><small>[ ' . $text . ' ]</small></small>', 'tags.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('edit.html', true);

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (form.raw_tag.value == '') {
		alert('<?php echo JText::_('ERROR_EMPTY_TAG'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<?php
if ($this->getError()) 
{
	echo '<p class="error">' . implode('<br />', $this->getError()) . '</p>';
}
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('DETAILS'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="admin"><?php echo JText::_('ADMIN'); ?>:</label></th>
						<td><input type="checkbox" name="fields[admin]" id="admin" value="1" <?php if ($this->tag->get('admin') == 1) { echo 'checked="checked"'; } ?> /></td>
					</tr>
					<tr>
						<th class="key"><label for="raw_tag"><?php echo JText::_('TAG'); ?>:</label></th>
						<td><input type="text" name="fields[raw_tag]" id="raw_tag" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->tag->get('raw_tag'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key" style="vertical-align:top;"><label><?php echo JText::_('DESCRIPTION'); ?>:</label></th>
						<td><?php echo $editor->display('fields[description]', stripslashes($this->tag->get('description')), '100%', '200px', '50', '10'); ?></td>
					</tr>
					<tr>
						<th class="key" style="vertical-align:top;"><label><?php echo JText::_('ALIAS'); ?>:</label></th>
						<td><?php echo $editor->display('fields[substitutions]', stripslashes($this->tag->substitutes('string')), '100%', '200px', '50', '10'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
<?php
		//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'log.php');
		//$logger = new TagsTableLog(JFactory::getDBO());
		$logs = $this->tag->logs('list'); //$logger->getLogs($this->tag->get('id'));
		if ($logs)
		{
?>
		<h4><?php echo JText::_('Activity log'); ?></h4>
		<ul class="entry-log">
			<?php
			foreach ($logs as $log)
			{
				//$user = JUser::getInstance($log->actorid);
				$actor = $this->escape(stripslashes($log->actor('name'))); //$this->escape(stripslashes($user->get('name')))
			?>
			<li>
				<?php
				$data = json_decode($log->get('comments'));
				if (!isset($data->entries))
				{
					$data->entries = 0;
				}
				switch ($log->get('action'))
				{
					case 'substitute_created':
						$s = JText::sprintf('%s alias created on %s by %s', $data->raw_tag, $log->get('timestamp'), $actor);
					break;

					case 'substitute_edited':
						$s = JText::sprintf('%s alias edited on %s by %s', $data->raw_tag, $log->get('timestamp'), $actor);
					break;

					case 'substitute_deleted':
						$s = JText::sprintf('%s aliases removed on %s by %s', implode(', ', $data->tags), $log->get('timestamp'), $actor);
					break;
					
					case 'substitute_moved':
						$s = JText::sprintf('%s aliases moved from %s on %s by %s', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
					break;

					case 'tags_removed':
						$s = JText::sprintf('%s associations removed from %s %s on %s by %s', count($data->entries), $data->tbl, $data->objectid, $log->get('timestamp'), $actor);
					break;

					case 'objects_copied':
						$s = JText::sprintf('%s associations copied from %s on %s by %s', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
					break;

					case 'objects_moved':
						$s = JText::sprintf('%s associations moved from %s on %s by %s', count($data->entries), $data->old_id, $log->get('timestamp'), $actor);
					break;

					case 'objects_removed':
						if ($data->objectid || $data->tbl)
						{
							$s = JText::sprintf('%s associations removed for %s %s on %s by %s', count($data->entries), $data->tbl, $data->objectid, $log->get('timestamp'), $actor);
						}
						else 
						{
							$s = JText::sprintf('%s associations removed on %s by %s', count($data->entries), $data->tagid, $log->get('timestamp'), $actor);
						}
					break;

					default:
						$s = JText::sprintf('%s on %s by %s', str_replace('_', ' ', $log->get('action')), $log->get('timestamp'), $actor);
					break;
				}
				if ($s)
				{
					echo '<span class="entry-log-data">' . $s . '</span>';
				}
				?>
			</li>
			<?php 
			}
			?>
		</ul>
<?php 
		}
?>
	</div>
	<div class="col width-40 fltrt">
		<h4><?php echo JText::_('Normalization'); ?></h4>
		<p><?php echo JText::_('NORMALIZED_EXPLANATION'); ?></p>
		<h4><?php echo JText::_('ALIAS'); ?></h4>
		<p><?php echo JText::_('Enter a comma-separated list of tags you wish this tag to be substituted for. For example: If you enter "h20, aqua" for the tag "water", any time someone enters "h20" or "aqua" it will result in a tag of "water".'); ?></p>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->tag->get('id'); ?>" />
	<input type="hidden" name="fields[tag]" value="<?php echo $this->tag->get('tag'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>