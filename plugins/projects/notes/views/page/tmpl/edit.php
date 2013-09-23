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

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();

if ($this->page->id) {
	$lid = $this->page->id;
} else {
	$num = time().rand(0,10000);
	$lid = JRequest::getInt( 'lid', $num, 'post' );
}

// get templates
$templates = $this->page->getTemplates();

// Incoming
$scope   = JRequest::getVar('scope', '');
$tool 	 = JRequest::getVar( 'tool', '', 'request', 'object' );	
$project = JRequest::getVar( 'project', '', 'request', 'object' );
$canDelete = JRequest::getVar('candelete', 0);	

?>
<div id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<h2><?php echo $this->escape($this->title); ?></h2>
<?php 
?>
</div><!-- /#content-header -->

<?php 
if ($this->page->id) {
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'submenu'
	));
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->page   = $this->page;
	$view->task   = $this->task;
	$view->config = $this->config;
	$view->sub    = $this->sub;
	$view->display();
} 
?>

<div class="main section">
<?php
if ($this->page->id && !$this->config->get('access-modify')) {
	if ($this->page->params->get( 'allow_changes' ) == 1) { ?>
		<p class="warning"><?php echo JText::_('WIKI_WARNING_NOT_AUTH_EDITOR_SUGGESTED'); ?></p>
<?php } else { ?>
		<p class="warning"><?php echo JText::_('WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php }
}
?>

<?php if ($this->page->state == 1 && !$this->config->get('access-manage')) { ?>
	<p class="warning"><?php echo JText::_('WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php } ?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->preview) { ?>
	<div id="preview">
		<div class="main section">
			<p class="warning"><?php echo JText::_('This a preview only. Changes will not take affect until saved.'); ?></p>

			<div class="wikipage">
				<?php echo $this->revision->pagehtml; ?>
			</div>
		</div><!-- / .section -->
	</div><div class="clear"></div>
<?php } ?>
<?php if ($tool && $tool->id) { ?>

<?php } ?>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$scope.'&pagename='.$this->page->pagename); ?>" method="post" id="hubForm"<?php echo ($this->sub) ? ' class="full"' : ''; ?>>
	<fieldset>
		<?php if ($templates) { ?>
		<div class="group">

		<label for="templates">
			<?php echo JText::_('Template'); ?>:
			<select name="tplate" id="templates">
				<option value="tc"><?php echo JText::_('Select a template...'); ?></option>
<?php
$hi = array();

if ($templates) {
	$database =& JFactory::getDBO();
	$temprev = new WikiPageRevision($database);

	foreach ($templates as $template)
	{
		$temprev->loadByVersion($template->id);

		//$temprev->pagetext = str_replace('"','&quot;', $temprev->pagetext);
		//$temprev->pagetext = str_replace('&quote;','&quot;', $temprev->pagetext);

		$tplt = new WikiPage($database);
		$tplt->id = $template->id;
		
		$tmpltags = $tplt->getTags();
		if (count($tmpltags) > 0) {
			$tagarray = array();
			foreach ($tmpltags as $tag)
			{
				$tagarray[] = $tag['raw_tag'];
			}
			if (strtolower($this->tplate) == strtolower($template->pagename)) {
				$this->tags = implode(', ', $tagarray);
			}
			$tmpltags = $tagarray;
		}
		$tmpltags = implode(', ', $tmpltags);

		echo "\t".'<option value="t'.$template->id.'"';
		if (strtolower($this->tplate) == strtolower($template->pagename)
		 || strtolower($this->tplate) == 't' . $template->id) {
			echo ' selected="selected"';
			if (!$this->page->id) {
				$this->revision->pagetext = stripslashes($temprev->pagetext);
			}
		}
		echo '>'.$this->escape(stripslashes($template->pagename)).'</option>'."\n";

		$j  = '<input type="hidden" name="t'.$template->id.'" id="t'.$template->id.'" value="'.$this->escape(stripslashes($temprev->pagetext)).'" />'."\n";
		$j .= '<input type="hidden" name="t'.$template->id.'_tags" id="t'.$template->id.'_tags" value="'.$this->escape(stripslashes($tmpltags)).'" />'."\n";

		$hi[] = $j;
	}
}
?>			</select>
			<?php echo implode("\n", $hi); ?>
		</label>
		</div>
	<?php } ?>
		
	<?php if ($this->config->get('access-edit')) { ?>
		<label for="title">
			<?php echo JText::_('WIKI_FIELD_TITLE'); ?>:
			<span class="required"><?php echo JText::_('WIKI_REQUIRED'); ?></span>
			<input type="text" name="page[title]" id="title" value="<?php echo $this->escape($this->page->title); ?>" size="38" />
		</label>
	<?php } else { ?>
		<input type="hidden" name="page[title]" id="title" value="<?php echo $this->escape($this->page->title); ?>" />
	<?php } ?>
		
		<label for="pagetext" style="position: relative;">
			<?php echo JText::_('WIKI_FIELD_PAGETEXT'); ?>: 
			<span class="required"><?php echo JText::_('WIKI_REQUIRED'); ?></span>
			<?php
			ximport('Hubzero_Wiki_Editor');
			$editor =& Hubzero_Wiki_Editor::getInstance();
			echo $editor->display('revision[pagetext]', 'pagetext', $this->revision->pagetext, '', '35', '40');
			?>
			<!-- <span id="pagetext-overlay"><span>Drop file here to include in page</span></span> -->
		</label>
		<p class="ta-right hint">
			See <a class="wiki-formatting popup" rel="external" href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiFormatting'); ?>">Help: Wiki Formatting</a> for help on editing content.
		</p>
		
<?php if ($this->sub) { ?>
	<div id="file-uploader"></div>
		<div class="field-wrap mini">
				<p><?php echo JText::_('COM_PROJECTS_NOTES_INCLUDE_FILES_EXPLAIN'); ?></p>
				<p><a class="wiki-macros" href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiMacros#image'); ?>" rel="external">[[Image(filename.jpg)]]</a> to include an image from your local project files.</p>
				<p><a class="wiki-macros" href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiMacros#file'); ?>" rel="external">[[File(filename.pdf)]]</a> to include a file from your local project files.</p>
		</div>
<?php } ?>
<?php
$mode = $this->page->params->get('mode', 'wiki');
if ($this->config->get('access-edit')) {
	$cls = '';
	if ($mode && $mode != 'knol') {
		$cls = ' class="hide"';
	}

		$juser =& JFactory::getUser(); ?>
		
			<input type="hidden" name="params[mode]" id="params_mode" value="<?php echo $mode; ?>" />

<?php } else { ?>
			<input type="hidden" name="params[mode]" value="<?php echo $mode; ?>" />
			<input type="hidden" name="params[allow_changes]" value="<?php echo ($this->page->params->get( 'allow_changes' ) == 1) ? '1' : '0'; ?>" />
			<input type="hidden" name="params[allow_comments]" value="<?php echo ($this->page->params->get( 'allow_comments' ) == 1) ? '1' : '0'; ?>" />
			<input type="hidden" name="authors" id="params_authors" value="<?php echo $this->escape($this->authors); ?>" />
			<input type="hidden" name="page[access]" value="<?php echo $this->escape($this->page->access); ?>" />
<?php } ?>

			<input type="hidden" name="page[group]" value="<?php echo $this->escape($this->page->group_cn); ?>" />
	</fieldset>
	<div class="clear"></div>

<?php if ($this->config->get('access-edit')) { ?>
	<?php if (!$this->sub) { ?>
		<div class="explaination">
			<p><?php echo JText::_('WIKI_FIELD_TAGS_EXPLANATION'); ?></p>
		</div>
	<?php } ?>
		<fieldset>
			<label>
				<?php echo JText::_('WIKI_FIELD_TAGS'); ?>:
				<?php 
				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->tags)) );
				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<input type="text" name="tags" value="'. $this->tags .'" size="38" />';
				}
				?>
				<span class="hint"><?php echo JText::_('WIKI_FIELD_TAGS_HINT'); ?></span>
			</label>
<?php } else { ?>
		<input type="hidden" name="tags" value="<?php echo $this->escape($this->tags); ?>" />
<?php } ?>
			<label>
				<?php echo JText::_('WIKI_FIELD_EDIT_SUMMARY'); ?>:
				<input type="text" name="revision[summary]" value="<?php echo $this->escape($this->revision->summary); ?>" size="38" />
				<span class="hint"><?php echo JText::_('WIKI_FIELD_EDIT_SUMMARY_HINT'); ?></span>
			</label>
			<input type="hidden" name="revision[minor_edit]" value="1" />
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="page[id]" value="<?php echo $this->page->id; ?>" />
		<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
		<input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->pagename); ?>" />
		
		<input type="hidden" name="revision[id]" value="<?php echo $this->revision->id; ?>" />
		<input type="hidden" name="revision[pageid]" value="<?php echo $this->page->id; ?>" />
		<input type="hidden" name="revision[version]" value="<?php echo $this->revision->version; ?>" />
		<input type="hidden" name="revision[created_by]" value="<?php echo $this->revision->created_by; ?>" />
		<input type="hidden" name="revision[created]" value="<?php echo $this->revision->created; ?>" />
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="gid" value="<?php echo $this->page->group_cn; ?>" />
		<input type="hidden" name="active" value="notes" />
		<input type="hidden" name="scope" value="<?php echo $scope; ?>" />

		<p class="submit">
			<input type="submit" name="preview" value="<?php echo JText::_('PREVIEW'); ?>" /> &nbsp; 
			<input type="submit" name="submit" id="page-submit" value="<?php echo JText::_('SUBMIT'); ?>" />
		</p>
	</form>

	<style>
		#pagetext-overlay {
			background: rgba(255, 255, 255, 0.6); position: absolute; top: 0; bottom: 0; left: 0; right: 0;
		}
		#pagetext-overlay span {
			display: block;
			width: 200px;
			border-radius: 0.25em;
			background: rgba(0, 0, 0, 0.8);
			color: #fff;
			padding: 1em;
			text-align: center;
			text-shadow: rgba(0, 0, 0, 0.8);
			margin: 200px auto 100px auto;
		}
	</style>
</div><!-- / .main section -->
	
	<?php if ($this->page->id && strtolower($this->page->getNamespace()) != 'special' && $canDelete) { ?>
		<p class="mini rightfloat"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$scope.'&pagename='.$this->page->pagename.'&task=delete'); ?>" class="btn"><?php echo JText::_('Delete this page'); ?></a></p>
	<?php } ?>