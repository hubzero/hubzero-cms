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
defined('_JEXEC') or die('Restricted access');

// get tool access text
$toolaccess = ToolsHelperHtml::getToolAccess($this->status['exec'], $this->status['membergroups']);
// get source code access text
$codeaccess = ToolsHelperHtml::getCodeAccess($this->status['code']);
// get wiki access text
$wikiaccess = ToolsHelperHtml::getWikiAccess($this->status['wiki']);
?>
<div id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><a class="idon-status status btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']); ?>"><?php echo JText::_('COM_TOOLS_TOOL_STATUS'); ?></a></li>
		<li class="last"><a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=create'); ?>"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php ToolsHelperHtml::writeApproval('Approve'); ?>

	<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	
	<h4><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_FINAL_REVIEW'); ?>:</h4>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=finalizeversion&app=' . $this->status['toolname']); ?>" method="post" id="versionForm" name="versionForm">
		<fieldset class="versionfield">
			<div class="two columns first">
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="finalizeversion" />
				<input type="hidden" name="newstate" value="<?php echo ToolsHelperHtml::getStatusNum('Approved') ?>" />
				<input type="hidden" name="id" value="<?php echo $this->status['toolid'] ?>" />
				<input type="hidden" name="app" value="<?php echo $this->status['toolname'] ?>" />
				<div>
					<h4>Tool Information <a class="edit button" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app=' . $this->status['toolname']); ?>" title="Edit this version information">Edit</a></h4>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_TITLE'); ?>: </span><span class="desc"><?php echo $this->escape(stripslashes($this->status['title'])); ?></span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_VERSION'); ?>: </span><span class="desc"><?php echo $this->escape(stripslashes($this->status['version'])); ?></span>
						<span class="actionlink">[<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=versions&action=confirm&app=' . $this->status['toolname']); ?>">edit</a>]</span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_DESCRIPTION'); ?>: </span><span class="desc"><?php echo $this->escape(stripslashes($this->status['description'])); ?></span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_TOOL_ACCESS'); ?>: </span><span class="desc"> <?php echo $toolaccess; ?></span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_SOURCE_CODE'); ?>: </span><span class="desc"> <?php echo $codeaccess; ?></span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_WIKI_ACCESS'); ?>: </span><span class="desc"> <?php echo $wikiaccess; ?></span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_SCREEN_SIZE'); ?>: </span><span class="desc"> <?php echo $this->status['vncGeometry']; ?></span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_DEVELOPERS'); ?>: </span><span class="desc"> <?php echo ToolsHelperHtml::getDevTeam($this->status['developers']); ?></span></p>
					<p><span class="heading"><?php echo JText::_('COM_TOOLS_AUTHORS'); ?>: </span><span class="desc"> <?php echo ToolsHelperHtml::getDevTeam($this->status['authors']); ?></span></p>
					<p><a href="<?php echo JRoute::_('index.php?option=com_resources&alias=' . $this->status['toolname'] . '&rev=dev'); ?>"><?php echo JText::_('COM_TOOLS_PREVIEW_RES_PAGE'); ?></a></p>
				</div>
			</div>
			<div class="two columns second">
				<h4>
					<?php echo JText::_('COM_TOOLS_TOOL_LICENSE'); ?> 
					<span class="actionlink">
						[<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license&app=' . $this->status['toolname'] . '&action=confirm'); ?>"><?php echo JText::_('COM_TOOLS_EDIT'); ?></a>]
					</span>
				</h4>
				<pre class="licensetxt"><?php echo $this->escape(stripslashes($this->status['license'])); ?></pre>
			</div>
			<div class="moveon">
				<input type="submit" value="<?php echo JText::_('COM_TOOLS_APPROVE_THIS_TOOL'); ?>" />
			</div>
		</fieldset>
	</form>
	<div class="clear"></div>
</div>