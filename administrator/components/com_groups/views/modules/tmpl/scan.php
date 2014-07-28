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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();

JToolBarHelper::title($this->group->get('description').': '.$this->module->get('title').' - '. JText::_('COM_GROUPS_MODULES_SCAN'), 'groups.png');
JToolBarHelper::custom('markscanned', 'check', 'check', 'COM_GROUPS_MODULES_MARK_SCANNED', false);
JToolBarHelper::spacer();
JToolBarHelper::custom('scanagain', 'check', 'check', 'COM_GROUPS_MODULES_SCAN_AGAIN', false);
JToolBarHelper::cancel();

// page version content
$content = $this->module->get('content');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'markscanned')
	{
		if (!confirm('<?php echo JText::_('COM_GROUPS_MODULES_MARK_SCANNED_CONFIRM'); ?>'))
		{
			return false;
		}
	}
	submitform(pressbutton);
}
</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;gid=<?php echo $this->group->cn; ?>" method="post" name="adminForm" id="item-form">

	<?php
		unset($this->issues->count);
		$severe = $elevated = $minor = array();
		foreach ($this->issues as $lang => $languageIssues)
		{
			foreach ($languageIssues as $type => $languageIssue)
			{
				foreach ($languageIssue as $line => $issue)
				{
					array_push($$type, 'Line ' . $line . '. ' . $this->escape($issue));
				}
			}
		}
	?>
	<?php if (count($severe) > 0) : ?>
		<p class="error">
			<?php echo JText::sprintf('COM_GROUPS_MODULE_SCAN_SEVERE', implode('<br />', $severe)); ?>
		</p>
	<?php endif; ?>

	<?php if (count($elevated) > 0) : ?>
		<p class="warning">
			<?php echo JText::sprintf('COM_GROUPS_MODULE_SCAN_ELEVATED', implode('<br />', $elevated)); ?>
		</p>
	<?php endif; ?>

	<?php if (count($minor) > 0) : ?>
		<p class="info">
			<?php echo JText::sprintf('COM_GROUPS_MODULE_SCAN_MINOR', implode('<br />', $elevated)); ?>
		</p>
	<?php endif; ?>

	<h3><?php echo JText::_('COM_GROUPS_MODULES_RAW_CODE'); ?></h3>
	<div class="code">
		<?php
			$lines = explode("\n", $content);
			$lineCode = '';
			for ($i=1; $i <= count($lines); $i++)
			{
				$lineCode .= "&nbsp;".$i."&nbsp;<br>";
			}
		?>
		<table>
			<tr>
				<td class="lines"><?php echo $lineCode; ?></td>
				<td class="code">
					<?php echo highlight_string($content); ?>
				</td>
			</tr>
		</table>
	</div>

	<h3><?php echo JText::_('COM_GROUPS_MODULES_UPDATE_CONTENT'); ?></h3>
	<textarea name="module[content]" rows="40"><?php echo $content; ?></textarea>

	<input type="hidden" name="module[id]" value="<?php echo $this->module->get('id'); ?>">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo JHTML::_('form.token'); ?>
</form>