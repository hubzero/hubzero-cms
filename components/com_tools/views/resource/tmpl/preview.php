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
//$xhub =& Hubzero_Factory::getHub();
//$hubShortName = $xhub->getCfg('hubShortName');
//$hubShortURL = $xhub->getCfg('hubShortURL');
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
		<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=status&app=' . $this->resource->alias); ?>"><?php echo JText::_('TOOL_STATUS'); ?></a></li>
		<li class="last"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=pipeline&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<?php	
	$view = new JView(array(
		'name' => $this->controller,
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

<form action="index.php" method="post" id="hubForm">
	<input type="hidden" name="app" value="<?php echo $this->resource->alias; ?>" />
	<input type="hidden" name="rid" value="<?php echo $this->resource->id; ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="pipeline" />
	<input type="hidden" name="task" value="status" />
	
	<input type="hidden" name="msg" value="<?php echo JText::_('NOTICE_RES_UPDATED'); ?>" />
	<input type="hidden" name="step" value="6" />
	<input type="hidden" name="editversion" value="<?php echo $this->version; ?>" />
	<input type="hidden" name="toolname" value="<?php echo $this->resource->alias; ?>" />

	<div style="float:left; width:70%;padding:1em 0 1em 0;">
		<span style="float:left;width:100px;"><input type="button" value="&lt; <?php echo ucfirst(JText::_('PREVIOUS')); ?>" class="returntoedit" /></span>
		<span style="float:right;width:100px;"><input type="submit" value="<?php echo ucfirst(JText::_('CONTRIBTOOL_STEP_FINALIZE')); ?> &gt;" /></span>
	</div>
    <div class="clear"></div>
 </form>
<?php

$cats = array();
$sections = array();

include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'usage.php' );

$body = ResourcesHtml::about( $database, 0, $this->usersgroups, $this->resource, $helper, $this->config, array(), null, null, null, null, $params, $attribs, 'com_resources', 0 );

$cat = array();
$cat['about'] = JText::_('ABOUT');
array_unshift($cats, $cat);
array_unshift($sections, array('html'=>$body,'metadata'=>''));

//$html  = '<h1 id="preview-header">'.JText::_('REVIEW_PREVIEW').'</h1>'.n;
//$html .= '<div id="preview-pane">'.n;
//$html .= ResourcesHtml::title( 'com_resources', $resource, $params, false );
//$html .= ResourcesHtml::tabs( 'com_resources', $rid, $cats, 'about' );
$html .= ResourcesHtml::sections( $sections, $cats, 'about', 'hide', 'main' );
//$html .= '</div><!-- / #preview-pane -->'.n;

echo $html;