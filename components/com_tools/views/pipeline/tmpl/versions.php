<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b. %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M. Y';
	$tz = false;
}

$juser = &JFactory::getUser();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
    	<li><a class="status btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']); ?>"><?php echo JText::_('COM_TOOLS_TOOL_STATUS'); ?></a></li>
		<li class="last"><a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=create'); ?>"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">   
<?php 
($this->status['published'] != 1 && !$this->status['version']) ?  $hint = '1.0' :$hint = '' ; // if tool is under dev and no version was specified before
$statuspath = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']);

$newstate = ($this->action == 'edit') ? $this->status['state']: ToolsHelperHtml::getStatusNum('Approved') ;
$submitlabel = ($this->action == 'edit') ? JText::_('COM_TOOLS_SAVE') : JText::_('COM_TOOLS_USE_THIS_VERSION');
if ($this->action == 'confirm') {
	ToolsHelperHtml::writeApproval(JText::_('COM_TOOLS_CONFIRM_VERSION'));
}

$rconfig =& JComponentHelper::getParams( 'com_resources' );
$hubDOIpath = $rconfig->get('doi');
?> 
	<div class="two columns first">	
<?php if ($this->error) { echo ToolsHelperHtml::error( $this->error ); } ?>

<?php if ($this->action != 'dev' && $this->status['state'] != ToolsHelperHtml::getStatusNum('Published')) { ?>
	<?php if ($this->action == 'confirm' or $this->action == 'edit') { ?>
		<h4><?php echo JText::_('COM_TOOLS_VERSION_PLS_CONFIRM'); ?> <?php echo($this->action == 'edit') ? JText::_('COM_TOOLS_NEXT'): JText::_('COM_TOOLS_THIS'); ?> <?php echo JText::_('COM_TOOLS_TOOL_RELEASE'); ?>:</h4>
	<?php } else if($this->action == 'new' && $this->status['toolname']) { // new version is required ?>
		<h4><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_ENTER_UNIQUE_VERSION'); ?>:</h4>
	<?php } ?>
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=saveversion&app=' . $this->status['toolname']); ?>" method="post" id="versionForm">
			<fieldset class="versionfield">
				<label for="newversion"><?php echo ucfirst(JText::_('COM_TOOLS_VERSION')); ?>: </label>
				<input type="text" name="newversion" id="newversion" value="<?php echo $this->status['version']; ?>" size="20" maxlength="15" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="saveversion" />
				<input type="hidden" name="newstate" value="<?php echo $newstate; ?>" />
				<input type="hidden" name="action" value="<?php echo $this->action; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->status['toolid'] ?>" />
				<input type="hidden" name="toolname" value="<?php echo $this->status['toolname'] ?>" />
				<input type="submit" value="<?php echo $submitlabel ?>" />
			</fieldset>
		</form>
<?php } ?>
		
		<h3><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_EXISTING_VERSIONS'); ?>:</h3>
<?php if ($this->versions && $this->status['toolname']) { // show versions ?>
		<table id="tktlist" summary="<?php echo JText::_('COM_TOOLS_Tool versions'); ?>">
			<thead>
				<tr>
					<th scope="row"><?php echo ucfirst(JText::_('COM_TOOLS_VERSION')); ?></th>
					<th scope="row"><?php echo ucfirst(JText::_('COM_TOOLS_RELEASED')); ?></th>
					<th scope="row"><?php echo ucfirst(JText::_('COM_TOOLS_SUBVERSION')); ?></th>
					<th scope="row"><?php echo ucfirst(JText::_('COM_TOOLS_PUBLISHED')); ?></th>
					<th scope="row"></th>
				</tr>
			</thead>
			<tbody>
<?php
	$i=0;
	foreach ($this->versions as $t) 
	{
		// get tool access text
		$toolaccess = ToolsHelperHtml::getToolAccess($t->toolaccess, $this->status['membergroups']);
		// get source code access text
		$codeaccess = ToolsHelperHtml::getCodeAccess($t->codeaccess);
		// get wiki access text
		$wikiaccess = ToolsHelperHtml::getWikiAccess($t->wikiaccess);

		$handle = ($t->doi) ? $hubDOIpath.'r'.$this->status['resourceid'].'.'.$t->doi : '' ;

		$t->version = ($t->state==3 && $t->version==$this->status['currentversion']) ? JText::_('COM_TOOLS_NO_LABEL') : $t->version;
?>
				<tr id="displays_<?php echo $i; ?>">
					<td>
						<span class="showcontrols">
							<a href="javascript:void(0);" class="expand" style="border:none;" id="exp_<?php echo $i; ?>">&nbsp;&nbsp;</a>
						</span> 
						<?php echo ($t->version) ? $t->version : JText::_('COM_TOOLS_NA'); ?>
					</td>
					<td>
						<?php if ($t->state != 3) { ?>
							<?php echo $t->released ? JHTML::_('date', $t->released, $dateFormat, $tz) : 'N/A'; ?>
						<?php } else { ?>
							<span class="yes"><?php echo JText::_('COM_TOOLS_UNDER_DEVELOPMENT'); ?></span>
						<?php } ?>
					</td>
					<td>
						<?php if ($t->state!=3 or ($t->state==3 && $t->revision != $this->status['currentrevision'])) { echo $t->revision; } else { echo '-'; } ?>
					</td>
					<td>
						<span class="<?php echo ($t->state=='1' ? 'toolpublished' : 'toolunpublished'); ?>"></span>
					</td>
					<td>
						<?php if ($t->state == 1 && $this->admin) { ?> 
							<span class="actionlink">
								<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app=' . $this->status['toolname'] . '&editversion=current'); ?>"><?php echo JText::_('COM_TOOLS_EDIT'); ?></a>
							</span>
						<?php } else if ($t->state == 3) { ?>
							<span class="actionlink">
								<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app=' . $this->status['toolname'] . '&editversion=dev'); ?>"><?php echo JText::_('COM_TOOLS_EDIT');?></a>
							</span> 
						<?php } ?>
					</td>
				</tr>
				<tr id="configure_<?php echo $i; ?>" class="config hide">
					<td id="conftdone_<?php echo $i; ?>"></td>
					<td colspan="4" id="conftdtwo_<?php echo $i; ?>">
						<div id="confdiv_<?php echo $i; ?>" class="vmanage">
							<p><span class="heading"><?php echo ucfirst(JText::_('COM_TOOLS_TITLE')); ?>: </span><span class="desc"><?php echo $t->title; ?></span></p>
							<p><span class="heading"><?php echo ucfirst(JText::_('COM_TOOLS_DESCRIPTION')); ?>: </span><span class="desc"><?php echo $t->description; ?></span></p>
							<p><span class="heading"><?php echo ucfirst(JText::_('COM_TOOLS_AUTHORS')); ?>: </span><span class="desc"><?php echo ToolsHelperHtml::getDevTeam($t->authors); ?></span></p>
							<p><span class="heading"><?php echo ucfirst(JText::_('COM_TOOLS_TOOL_ACCESS')); ?>: </span><span class="desc"><?php echo $toolaccess; ?></span></p>
							<p><span class="heading"><?php echo ucfirst(JText::_('COM_TOOLS_CODE_ACCESS')); ?>: </span><span class="desc"><?php echo $codeaccess; ?></span></p>
							<?php if ($handle) { echo ' <p><span class="heading">'.JText::_('COM_TOOLS_DOI').': </span><span class="desc"><a href="http://hdl.handle.net/'.$handle.'">'.$handle.'</a></span></p>'; } ?>
						</div>
					</td>
				</tr>
<?php 
		$i++;
	} // end foreach
?>
		</tbody>
	</table>
<?php
} else { // no versions found
	echo (JText::_('COM_TOOLS_CONTRIBTOOL_NO_VERSIONS').' '.$this->status['toolname']. '. '.ucfirst(JText::_('COM_TOOLS_GO_BACK_TO')).' <a href="'.$statuspath.'">'.strtolower(JText::_('COM_TOOLS_TOOL_STATUS')).'</a>.');
}
?>
	</div>
	<div class="two columns second">
		<h3><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_VERSION_WHY_NEED_NUMBER'); ?></h3>
		<p><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_VERSION_WHY_NEED_NUMBER_ANSWER'); ?></p>
		<h3><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE'); ?></h3>
		<p><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_ONE'); ?></p>			
		<p><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_ONE'); ?></p>		
		<p><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_THREE'); ?></p>
	</div>
	<div class="clear"></div>
</div>
