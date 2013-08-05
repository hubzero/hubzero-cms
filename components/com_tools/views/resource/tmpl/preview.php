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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
//$license = '/legal/license';
$database = JFactory::getDBO();
$html = '';

$paramsClass = 'JRegistry';
if (version_compare(JVERSION, '1.6', 'lt'))
{
	$paramsClass = 'JParameter';
}

// Get parameters
$rparams = new $paramsClass($this->resource->params);
$params = $this->config;
$params->merge($rparams);

// Get attributes
$attribs = new $paramsClass($this->resource->attribs);

// Get the resource's children
$helper = new ResourcesHelper($this->resource->id, $database);

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><a class="icon-status status btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=status&app=' . $this->resource->alias); ?>"><?php echo JText::_('COM_TOOLS_TOOL_STATUS'); ?></a></li>
		<li class="last"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=create'); ?>" class="icon-add add btn"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="section steps-section">
<?php	
	$view = new JView(array(
		'name'   => $this->controller,
		'layout' => 'stage'
	));
	$view->stage = $this->step;
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->version = $this->version;
	$view->row = $this->resource;
	$view->status = $this->status;
	$view->vnum = 0;
	$view->display();
?>
</div>

<div class="main section">
	<form action="index.php" method="post" id="hubForm">
		<input type="hidden" name="app" value="<?php echo $this->resource->alias; ?>" />
		<input type="hidden" name="rid" value="<?php echo $this->resource->id; ?>" />
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="pipeline" />
		<input type="hidden" name="task" value="status" />
		
		<input type="hidden" name="msg" value="<?php echo JText::_('COM_TOOLS_NOTICE_RES_UPDATED'); ?>" />
		<input type="hidden" name="step" value="6" />
		<input type="hidden" name="editversion" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="toolname" value="<?php echo $this->resource->alias; ?>" />

		<div style="float:left; width:70%;padding:1em 0 1em 0;">
			<span style="float:left;width:100px;"><input type="button" value="&lt; <?php echo ucfirst(JText::_('COM_TOOLS_PREVIOUS')); ?>" class="returntoedit" /></span>
			<span style="float:right;width:100px;"><input type="submit" value="<?php echo ucfirst(JText::_('COM_TOOLS_CONTRIBTOOL_STEP_FINALIZE')); ?> &gt;" /></span>
		</div>
		<div class="clear"></div>
	</form>

	<h1 id="preview-header"><?php echo JText::_('COM_TOOLS_Preview'); ?></h1>
	<div id="preview-pane">
		<iframe id="preview-frame" name="preview-frame" width="100%" frameborder="0" src="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->resource->id . '&tmpl=component&mode=preview&rev=' . $this->version); ?>"></iframe>
	</div>
</div>
