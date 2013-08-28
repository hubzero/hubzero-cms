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

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '&amp;controller=' 
. $this->controller . '">' . JText::_('Publication Manager') . '</a> &raquo; <small><small>' . 	'<a href="index.php?option=' 
. $this->option . '&amp;controller=' . $this->controller . '&amp;task=edit&amp;id[]= '. $this->pub->id .'">' 
. JText::_('Publication') . ': #' . $this->pub->id . '</a> - Versions</small></small>', 'addedit.png');
JToolBarHelper::spacer();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>
<?php if($this->config->get('enabled') == 0) { ?>
<p class="warning">This component is currently disabled and is inaccessible to end users.</p>
<?php } ?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm">
	
	<table class="adminlist">
		<thead>
			<tr>
				<th class="tdmini"></th>	
				<th class="tdmini"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DOI').'/'.JText::_('PLG_PROJECTS_PUBLICATIONS_ARK'); ?></th>
				<th><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_OPTIONS')); ?></th>
			</tr>
		 </thead>
		<tbody>
<?php
$k = 0;

	foreach($this->versions as $v) { 
	// Get DOI
	$doi = $v->doi ? 'doi:'.$v->doi : '';
	$ark = $v->ark ? 'ark:'.$v->ark : '';
	if($ark || $doi)
	{
		$doi_notice = $doi ? $doi : $ark;	
	}
	else {
		$doi_notice = JText::_('PLG_PROJECTS_PUBLICATIONS_NA');	
	}
				
	// Version status
	$status = PublicationHelper::getPubStateProperty($v, 'status');
	$class = PublicationHelper::getPubStateProperty($v, 'class');
	$date = PublicationHelper::getPubStateProperty($v, 'date');
			
	$options = '<a href="index.php?option=' . $this->option . '&amp;controller=' 
		. $this->controller . '&amp;task=edit&amp;id[]=' . $this->pub->id . '&amp;version='.$v->version_number.'">'
	.JText::_('PLG_PROJECTS_PUBLICATIONS_MANAGE_VERSION').'</a>';
	
	?>
	<tr class="mini <?php if($v->main == 1) { echo ' vprime'; } ?>">
		<td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
		<td><?php echo $v->version_label; ?></td>
		<td><?php echo $v->title; ?></td>
		<td class="v-status">
			<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
			<?php if($date) { echo '<span class="block faded">'.$date.'</span>';  } ?>
		</td>
		<td><?php echo $doi_notice; ?></td>
		<td><?php echo $options; ?></td>
	</tr>
<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
