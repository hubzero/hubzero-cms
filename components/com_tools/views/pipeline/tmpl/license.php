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

$open             = ($this->code == '@OPEN') ? 1 : 0 ;
$this->codeaccess = ($this->code == '@OPEN') ? 'open' : 'closed';
$newstate         = ($this->action == 'confirm') ? 'Approved' :  $this->status['state'];

$codeChoices = array(
	'@OPEN' => JText::_('COM_TOOLS_OPEN_SOURCE'),
	'@DEV'  => JText::_('COM_TOOLS_CLOSED_SOURCE')
);

$licenseChoices = array(
	'0' => JText::_('Choose a template')
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

$this->css('pipeline.css')
     ->js('pipeline.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-config btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=status&app='.$this->status['toolname']); ?>"><?php echo JText::_('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li class="last"><a class="icon-add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=create'); ?>"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
	<?php
		if ($this->action == 'confirm')
		{
			ToolsHelperHtml::writeApproval('Confirm license');
		}
		//$license = ($this->status['license'] && !$open) ? $this->status['license'] : '' ;
	?>
	<div class="grid">
		<div class="col span-half">
			<h3>
				<?php echo ($this->action == 'edit') ? JText::_('Specify license for next tool release:') : JText::_('Please confirm your license for this tool release:'); ?>
			</h3>
			<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license&app=' . $this->status['toolname']); ?>" method="post" id="licenseForm" name="licenseForm">
				<fieldset class="versionfield">
					<label><?php echo JText::_('COM_TOOLS_CODE_ACCESS'); ?>:</label>
					<?php echo ToolsHelperHtml::formSelect('t_code', 't_code', $codeChoices, $this->code, 'shifted', ''); ?>
					<span id="choice-icon">&nbsp;</span>
					<div id="closed-source">
						<h4><?php echo JText::_('COM_TOOLS_LICENSE_ARE_YOU_SURE'); ?></h4>
						<div class="why-open"><?php echo JText::_('COM_TOOLS_LICENSE_WHY_OPEN_SOURCE'); ?></div>
						<label><?php echo JText::_('COM_TOOLS_LICENSE_CLOSED_REASON'); ?> <span class="required"><?php echo JText::_('COM_TOOLS_REQUIRED'); ?></span>
							<textarea name="reason" cols="30" rows="5" id="reason"></textarea>
						</label>
					</div>
					<div id="open-source">
						<div id="lic">
							<label><?php echo JText::_('COM_TOOLS_LICENSE_TEMPLATE'); ?>:</label>
							<?php echo ToolsHelperHtml::formSelect('templates', 'templates',  $licenseChoices, $this->license_choice['template'], 'shifted', ''); ?>
						</div>
						<div class="licinput" >
							<label><?php echo JText::_('COM_TOOLS_LICENSE_TEXT'); ?><span class="required"><?php echo JText::_('COM_TOOLS_REQUIRED'); ?></span>
							<textarea name="license" cols="50" rows="15" id="license" placeholder="<?php echo JText::_('COM_TOOLS_ENTER_LICENSE_TEXT'); ?>"> <?php echo $this->escape(stripslashes($this->license_choice['text'])); ?></textarea>
							</label>
							<?php
							if ($this->licenses)
							{
								foreach ($this->licenses as $l)
								{
									echo '<div class="hidden" id="' . $l->name . '" >' . $this->escape(stripslashes($l->text)) . '</div>' . "\n";
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
						<div id="legendnotes">
							<h3><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_TEMPLATE_CHOICE'); ?></h3>
							<p>
								<?php echo JText::_('COM_TOOLS_LICENSE_TEMPLATE_TIP'); ?>:
								<br />[<?php echo strtoupper(JText::_('COM_TOOLS_YEAR')); ?>]
								<br />[<?php echo strtoupper(JText::_('COM_TOOLS_OWNER')); ?>]
								<br />[<?php echo strtoupper(JText::_('COM_TOOLS_ORGANIZATION')); ?>]
								<br />[<?php echo strtoupper(JText::_('COM_TOOLS_ONE_LINE_DESCRIPTION')); ?>]
								<br />[<?php echo strtoupper(JText::_('COM_TOOLS_URL')); ?>]
							</p>
						</div>
						<label for="field-authorize">
							<input type="checkbox" name="authorize" id="field-authorize" value="1" />
							<span class="required"><?php echo JText::_('COM_TOOLS_REQUIRED'); ?></span><?php echo JText::_('COM_TOOLS_LICENSE_CERTIFY') . ' '.JText::_('COM_TOOLS_LICENSE_UNDER_SPECIFIED'); ?>
						</label>
					</div>
					<div class="moveon">
						<input type="submit" value="<?php echo JText::_('COM_TOOLS_SAVE'); ?>" />
					</div>
				</fieldset>
			</form>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<h3><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_WHAT_OPTIONS'); ?></h3>
			<p class="opensource">
				<?php echo '<strong>'.ucfirst(JText::_('COM_TOOLS_OPEN_SOURCE')).'</strong><br />'.JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_IF_YOU_CHOOSE').' <a href="http://www.opensource.org/" rel="external" title="Open Source Initiative">'.strtolower(JText::_('COM_TOOLS_OPEN_SOURCE')).'</a>, '.JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_OPEN_TXT'); ?>
			</p>
			<p class="closedsource">
				<strong><?php echo ucfirst(JText::_('COM_TOOLS_CLOSED_SOURCE')); ?></strong><br /><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_LICENSE_CLOSED_TXT'); ?>
			</p>
		</div><!-- / .col span-half -->
	</div><!-- / .grid -->
</section><!-- / .main section -->