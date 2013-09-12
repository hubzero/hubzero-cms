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

// Import share CSS to style share features on right side of trifold
ximport('Hubzero_Document');
Hubzero_Document::addPluginStylesheet('resources', 'share');
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="get" id="tagBrowserForm">

	<div id="content-header-extra">
		<fieldset>
			<label for="browse-type">
				<span><?php echo JText::_('COM_RESOURCES_TYPE'); ?>:</span> 
				<select name="type" id="browse-type">
				<?php foreach ($this->types as $type) { ?>
					<option value="<?php echo $this->escape($type->alias); ?>"<?php if ($type->id == $this->filters['type']) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($type->type)); ?></option>
				<?php } ?>
				</select>
			</label>
			<input type="submit" value="<?php echo JText::_('COM_RESOURCES_GO'); ?>"/>
			<input type="hidden" name="task" value="browsetags" />
		</fieldset>
	</div><!-- / #content-header-extra -->

	<div class="main section" id="browse-resources">
		<div id="tagbrowser">
			<p class="info"><?php echo JText::_('COM_RESOURCES_TAGBROWSER_EXPLANATION'); ?></p>
			<div id="level-1">
				<h3><?php echo JText::_('COM_RESOURCES_TAG'); ?></h3>
				<ul>
					<li id="level-1-loading"></li>
				</ul>
			</div><!-- / #level-1 -->
			<div id="level-2">
				<h3><?php echo JText::_('COM_RESOURCES'); ?> <select name="sortby" id="sortby"></select></h3>
				<ul>
					<li id="level-2-loading"></li>
				</ul>
			</div><!-- / #level-2 -->
			<div id="level-3">
				<h3><?php echo JText::_('COM_RESOURCES_INFO'); ?></h3>
				<ul>
					<li><?php echo JText::_('COM_RESOURCES_TAGBROWSER_COL_EXPLANATION'); ?></li>
				</ul>
			</div><!-- / #level-3 -->
			<input type="hidden" name="pretype" id="pretype" value="<?php echo $this->escape($this->filters['type']); ?>" />
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="preinput" id="preinput" value="<?php echo $this->escape($this->tag); ?>" />
			<input type="hidden" name="preinput2" id="preinput2" value="<?php echo $this->escape($this->tag2); ?>" />
			<div class="clear"></div>
		</div><!-- / #tagbrowser -->
	
		<p id="viewalltools"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&type='.$this->filters['type']); ?>"><?php echo JText::_('COM_RESOURCES_VIEW_MORE'); ?></a></p>
		<div class="clear"></div>

<?php
$database =& JFactory::getDBO();

if ($this->supportedtag) {
	include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');

	$tag = new TagsTableTag( $database );
	$tag->loadTag($this->supportedtag);

	$sl = $this->config->get('supportedlink');
	if ($sl) {
		$link = $sl;
	} else {
		$link = JRoute::_('index.php?option=com_tags&tag='.$tag->tag);
	}
?>
		<p class="supported"><?php echo JText::_('COM_RESOURCES_WHATS_THIS'); ?> <a href="<?php echo $link; ?>"><?php echo JText::sprintf('COM_RESOURCES_ABOUT_TAG', $tag->raw_tag); ?></a></p>
<?php
}
?>
	</div>
	<div class="below section">
<?php
if ($this->results) {
?>
		<h3><?php echo JText::_('COM_RESOURCES_TOP_RATED'); ?></h3>
		<div class="aside">
			<p><?php echo JText::_('COM_RESOURCES_TOP_RATED_EXPLANATION'); ?></p>
		</div><!-- / .aside -->
		<div class="subject">
			<?php echo ResourcesHtml::writeResults( $database, $this->results, $this->authorized ); ?>
		</div><!-- / .subject -->
<?php
}
?>
	</div><!-- / .main section -->
</form>
