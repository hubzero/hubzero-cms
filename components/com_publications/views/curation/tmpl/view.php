<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$this->css()
     ->js()
	 ->css('jquery.fancybox.css', 'system')
	 ->css('curation.css')
	 ->js('curation.js');

// Get blocks
$blocks = $this->pub->_curationModel->_blocks;

$pubHelper 		= $this->pub->_helpers->pubHelper;
$htmlHelper 	= $this->pub->_helpers->htmlHelper;
$projectsHelper = $this->pub->_helpers->projectsHelper;

$now = JFactory::getDate()->toSql();

// Get creator name
$profile = \Hubzero\User\Profile::getInstance($this->pub->created_by);
$creator = $profile->get('name') . ' (' . $profile->get('username') . ')';

// Version status
$status = $pubHelper->getPubStateProperty($this->pub, 'status');
$class 	= $pubHelper->getPubStateProperty($this->pub, 'class');

$typetitle = $pubHelper::writePubCategory($this->pub->cat_alias, $this->pub->cat_name);

$profile = \Hubzero\User\Profile::getInstance($this->pub->modified_by);
$by 	 = ' ' . JText::_('COM_PUBLICATIONS_CURATION_BY') . ' ' . $profile->get('name');

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'controller=curation'); ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_LIST'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<form action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'controller=curation'); ?>" method="post" id="curation-form" name="curation-form">
	<fieldset>
		<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
		<input type="hidden" name="vid" value="<?php echo $this->pub->version_id; ?>" />
		<input type="hidden" name="task" id="task" value="save" />
	</fieldset>
<div class="curation-wrap">
	<div class="pubtitle">
		<h3><span class="restype indlist"><?php echo $typetitle; ?></span> <?php echo \Hubzero\Utility\String::truncate($this->pub->title, 65); ?> | <?php echo JText::_('COM_PUBLICATIONS_CURATION_VERSION')
		. ' ' . $this->pub->version_label; ?>
		</h3>
	</div>
	<p class="instruct">
		<span class="pubimage"><img src="<?php echo JRoute::_('index.php?option=com_publications&id=' . $this->pub->id . '&v=' . $this->pub->version_id) . '/Image:thumb'; ?>" alt="" /></span>
		<strong class="block"><?php echo $this->pub->reviewed ? JText::_('COM_PUBLICATIONS_CURATION_RESUBMITTED') : JText::_('COM_PUBLICATIONS_CURATION_SUBMITTED'); echo ' ' . JHTML::_('date', $this->pub->submitted, 'M d, Y') . $by; ?></strong>
	<?php echo JText::_('COM_PUBLICATIONS_CURATION_REVIEW_AND_ACT'); ?>
	<span class="legend">
		<span class="legend-checker-none"><?php echo JText::_('COM_PUBLICATIONS_CURATION_LEGEND_NONE'); ?></span>
		<span class="legend-checker-pass"><?php echo JText::_('COM_PUBLICATIONS_CURATION_LEGEND_PASS'); ?></span>
		<span class="legend-checker-fail"><?php echo JText::_('COM_PUBLICATIONS_CURATION_LEGEND_FAIL'); ?></span>
		<span class="legend-checker-update"><?php echo JText::_('COM_PUBLICATIONS_CURATION_LEGEND_UPDATE'); ?></span>
	</span>
	</p>
	<div class="clear"></div>
	<div class="submit-curation">
		<p>
			<span class="button-wrapper icon-kickback">
				<input type="submit" value="<?php echo JText::_('COM_PUBLICATIONS_CURATION_LOOKS_BAD'); ?>" class="btn btn-primary active icon-kickback btn-curate curate-kickback" />
			</span>
			<span class="button-wrapper icon-apply">
				<input type="submit" value="<?php echo JText::_('COM_PUBLICATIONS_CURATION_LOOKS_GOOD'); ?>" class="btn btn-success active icon-apply btn-curate curate-save" />
			</span>
		</p>
	</div>
	 	 <fieldset>
			<input type="hidden" name="id" id="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->pub->version_id; ?>" />
		 </fieldset>
		<?php if ($this->history && $this->history->comment) { ?>
			<div class="submitter-comment">
				<h5><?php echo JText::_('COM_PUBLICATIONS_CURATION_SUBMITTER_COMMENT'); ?></h5>
				<p><?php echo $this->history->comment; ?></p>
			</div>
		<?php } ?>
		<div class="curation-blocks">
<?php foreach ($blocks as $sequence => $block) {

	// Skip inactive blocks
	if (isset($block->active) && $block->active == 0)
	{
		continue;
	}
	$this->pub->_curationModel->setBlock( $block->name, $sequence );

	// Get block content
	echo $block->name == 'review' ? NULL : $this->pub->_curationModel->parseBlock( 'curator' );
	?>

<?php } ?>
		</div>
</div>
</form>
<div class="hidden">
	<div id="addnotice" class="addnotice">
		<form id="notice-form" name="noticeForm" action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'controller=curation'); ?>" method="post">
		 <fieldset>
			<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->pub->version_id; ?>" />
			<input type="hidden" name="ajax" value="1" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="p" id="props" value="" />
			<input type="hidden" name="pass" value="0" />
			<input type="hidden" name="task" value="save" />
			<h5 id="notice-title"><?php echo JText::_('COM_PUBLICATIONS_CURATION_NOTICE_TITLE'); ?></h5>
			<p class="notice-item" id="notice-item"></p>
			<label>
				<span class="block"><?php echo JText::_('COM_PUBLICATIONS_CURATION_NOTICE_LABEL'); ?></span>
				<textarea name="review" id="notice-review" rows="5" cols="10"></textarea>
			</label>
			</fieldset>
			<p class="submitarea">
				<input type="submit" id="notice-submit" class="btn" value="<?php echo JText::_('COM_PUBLICATIONS_CURATION_MARK_AS_FAIL'); ?>" />
			</p>
		</form>
	</div>
</div>