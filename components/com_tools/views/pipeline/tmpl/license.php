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

$open 					= ($this->code == '@OPEN') ? 1 : 0 ;
$this->codeaccess 		= ($this->code == '@OPEN') ? 'open' : 'closed';
$newstate   			= ($this->action == 'confirm') ? 'Approved' :  $this->status['state'];
//$this->statuspath 		= JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app='.$this->status['toolid']);

$codeChoices = array(
	'@OPEN' => 'open source (anyone can access code)',
	'@DEV'  => 'closed code'
);

$licenseChoices = array(
	'c1' => JText::_('Load a standard license')
);
if ($this->licenses) 
{
	foreach ($this->licenses as $l) 
	{
		if ($l->name != 'default') 
		{
			$licenseChoices[$l->name] = $l->title;
		}
	}
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><a class="icon-status status btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=status&app='.$this->status['toolname']); ?>"><?php echo JText::_('COM_TOOLS_TOOL_STATUS'); ?></a></li>
		<li class="last"><a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=create'); ?>"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php
	if ($this->action == 'confirm') {
		ToolsHelperHtml::writeApproval('Confirm license');
	}
	//$license = ($this->status['license'] && !$open) ? $this->status['license'] : '' ;
?>
	<div class="two columns first">
		<h3>
			<?php echo ($this->action == 'edit') ? JText::_('Specify license for next tool release:') : JText::_('Please confirm your license for this tool release:'); ?>
		</h3>
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license&app=' . $this->status['toolname']); ?>" method="post" id="versionForm" name="versionForm">
			<fieldset class="versionfield">
				<label><?php echo JText::_('COM_TOOLS_CODE_ACCESS'); ?>:</label> 
				<?php echo ToolsHelperHtml::formSelect('t_code', 't_code', $codeChoices, $this->code, 'shifted', ''); ?>
				
				<div id="lic_cl"><?php echo JText::_('COM_TOOLS_LICENSE'); ?>:</div>
				<div class="licinput" >
					<textarea name="license" cols="50" rows="15" id="license"><?php echo $this->escape(stripslashes($this->license_choice['text'])); ?></textarea>
					<?php 
					if ($this->licenses) 
					{
						foreach ($this->licenses as $l) 
						{
							echo '<input type="hidden" name="' . $l->name . '" id="' . $l->name . '" value="'.$this->escape(stripslashes($l->text)).'" />' . "\n";
						}
					} 
					?>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="savelicense" />
					<input type="hidden" name="curcode" id="curcode" value="<?php echo $open; ?>" />
					<input type="hidden" name="newstate" value="<?php echo $newstate; ?>" />
					<input type="hidden" name="action" value="<?php echo $this->action; ?>" />
					<input type="hidden" name="toolid" value="<?php echo $this->status['toolid']; ?>" />
					<input type="hidden" name="alias" value="<?php echo $this->status['toolname']; ?>" />
				</div>  
				<div id="lic">
					<label><?php echo JText::_('COM_TOOLS_LICENSE_TEMPLATE'); ?>:</label> 
					<?php echo ToolsHelperHtml::formSelect('templates', 'templates',  $licenseChoices, $this->license_choice['template'], 'shifted', ''); ?>
				</div>     
				<div id="legendnotes">
					<p>
						<?php echo JText::_('COM_TOOLS_LICENSE_TEMPLATE_TIP'); ?>:
						<br />[<?php echo strtoupper(JText::_('COM_TOOLS_YEAR')); ?>]
						<br />[<?php echo strtoupper(JText::_('COM_TOOLS_OWNER')); ?>]
						<br />[<?php echo strtoupper(JText::_('COM_TOOLS_ORGANIZATION')); ?>]
						<br />[<?php echo strtoupper(JText::_('COM_TOOLS_ONE_LINE_DESCRIPTION')); ?>]
						<br />[<?php echo strtoupper(JText::_('COM_TOOLS_URL')); ?>]
					</p>
					<label for="field-authorize">
						<input type="checkbox" name="authorize" id="field-authorize" value="1" /> 
						<?php echo JText::_('COM_TOOLS_LICENSE_CERTIFY').' <strong>'.JText::_('COM_TOOLS_OPEN_SOURCE').'</strong> '.JText::_('COM_TOOLS_LICENSE_UNDER_SPECIFIED'); ?>
					</label>
				</div>
				<div class="moveon">
					<input type="submit" value="<?php echo JText::_('COM_TOOLS_SAVE'); ?>" />
				</div>
			</fieldset>
		</form>
	</div><!-- / .two columns first -->
	<div class="two columns second">
    	<h3><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_WHAT_OPTIONS'); ?></h3>
		<p class="opensource">
			<?php echo '<strong>'.ucfirst(JText::_('COM_TOOLS_OPEN_SOURCE')).'</strong><br />'.JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_IF_YOU_CHOOSE').' <a href="http://www.opensource.org/" rel="external" title="Open Source Initiative">'.strtolower(JText::_('COM_TOOLS_OPEN_SOURCE')).'</a>, '.JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_OPEN_TXT'); ?>
		</p>
		<p class="error">
			<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_ATTENTION'); ?>
		</p>
		<p class="closedsource">
			<strong><?php echo ucfirst(JText::_('COM_TOOLS_CLOSED_SOURCE')); ?></strong><br /><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_CLOSED_TXT'); ?>
		</p>
	</div><!-- / .two columns second -->
	<div class="clear"></div>
</div><!-- / .main section -->