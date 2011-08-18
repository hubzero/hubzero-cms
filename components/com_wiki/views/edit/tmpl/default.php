<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$params = new JParameter( $this->page->params );

if ($this->sub) {
	$hid = 'sub-content-header';
	$sid = 'sub-section-menu';
} else {
	$hid = 'content-header';
	$sid = 'sub-menu';
}
?>
<div id="<?php echo $hid; ?>">
	<h2><?php echo $this->title; ?></h2>
<?php 
if ($this->page->id) { 
	echo WikiHtml::authors( $this->page, $params );
}
?>
</div><!-- /#content-header -->

<?php 
if ($this->page->id) {
	echo WikiHtml::subMenu( $this->sub, $this->option, $this->page->pagename, $this->page->scope, $this->page->state, $this->task, $params, $this->editauthorized );
} else {
?>
<div id="<?php echo $sid; ?>">
	<ul>
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename); ?>"><span>Article</span></a></li>
		<li class="active"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=edit'); ?>"><span>Edit</span></a></li>
	</ul>
	<div class="clear"></div>
</div><!-- / #sub-menu -->
<?php
}
?>

<div class="main section">
<?php
if ($this->page->id && !$this->authorized) {
	if ($params->get( 'allow_changes' ) == 1) { ?>
		<p class="warning"><?php echo JText::_('WIKI_WARNING_NOT_AUTH_EDITOR_SUGGESTED'); ?></p>
<?php } else { ?>
		<p class="warning"><?php echo JText::_('WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php }
}
?>

<?php if ($this->page->state == 1 && $this->authorized !== 'admin' && $this->authorized !== 'manager') { ?>
	<p class="warning"><?php echo JText::_('WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php } ?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->preview) { ?>
	<div id="preview">
		<div class="main section">
			<div class="aside">
				<p>This a preview only. Changes will not take affect until saved.</p>
			</div><!-- / .aside -->
			<div class="subject">
				<?php echo $this->preview->pagehtml; ?>
			</div><!-- / .subject -->
		</div><!-- / .section -->
	</div><div class="clear"></div>
<?php } ?>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope); ?>" method="post" id="hubForm">
	<div class="explaination">
		<p>To change the page name (the portion used for URLs), go <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=renamepage'); ?>">here</a>.</p>
		<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:WikiMacros#image'); ?>">[[Image(filename.jpg)]]</a> to include an image.</p>
		<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:WikiMacros#file'); ?>">[[File(filename.pdf)]]</a> to include a file.</p>
<?php 
if ($this->page->id) {
	$lid = $this->page->id;
} else {
	$num = time().rand(0,10000);
	$lid = JRequest::getInt( 'lid', $num, 'post' );
}
?>
		<iframe width="100%" height="370" name="filer" id="filer" style="border:2px solid #eee;margin-top: 0;" src="index.php?option=com_wiki&amp;no_html=1&amp;task=media&amp;listdir=<?php echo $lid; ?>"></iframe>
	</div>
	<fieldset>
<?php if ($this->authorized) { ?>
		<label>
			<?php echo JText::_('WIKI_FIELD_TITLE'); ?>:
	<?php if (!$this->page->pagename) { ?>
			<span class="required"><?php echo JText::_('WIKI_REQUIRED'); ?></span>
	<?php } ?>
			<input type="text" name="title" value="<?php echo $this->page->title; ?>" size="38" />
		</label>
<?php } else { ?>
		<input type="hidden" name="title" value="<?php echo $this->page->title; ?>" />
<?php } ?>
		<label>
			<?php echo JText::_('Template'); ?>:
			<select name="templates" id="templates">
				<option value="tc"><?php echo JText::_('Select a template...'); ?></option>
<?php
$hi = array();
$templates = $this->page->getTemplates();

if ($templates) {
	$database =& JFactory::getDBO();
	$temprev = new WikiPageRevision( $database );

	foreach ($templates as $template)
	{
		$temprev->loadByVersion( $template->id );

		$temprev->pagetext = str_replace('"','&quot;',$temprev->pagetext);
		$temprev->pagetext = str_replace('&quote;','&quot;',$temprev->pagetext);

		$tplt = new WikiPage( $database );
		$tplt->id = $template->id;
		$tmpltags = $tplt->getTags();
		if (count($tmpltags) > 0) {
			$tagarray = array();
			foreach ($tmpltags as $tag)
			{
				$tagarray[] = $tag['raw_tag'];
			}
			$tmpltags = implode( ', ', $tagarray );
			if (strtolower($this->tplate) == strtolower($template->pagename)) {
				$this->tags = $tmpltags;
			}
		}
		
		echo "\t".'<option value="t'.$template->id.'"';
		if (strtolower($this->tplate) == strtolower($template->pagename)) {
			echo ' selected="selected"';
			if (!$this->page->id) {
				$this->revision->pagetext = stripslashes($temprev->pagetext);
			}
		}
		echo '>'.stripslashes($template->pagename).'</option>'."\n";

		$j  = '<input type="hidden" name="t'.$template->id.'" id="t'.$template->id.'" value="'.htmlentities(stripslashes($temprev->pagetext), ENT_QUOTES).'" />'."\n";
		$j .= '<input type="hidden" name="t'.$template->id.'_tags" id="t'.$template->id.'_tags" value="'.htmlentities(stripslashes($tmpltags), ENT_QUOTES).'" />'."\n";
		
		$hi[] = $j;
	}
}
?>			</select>
			<?php echo implode("\n",$hi); ?>
		</label>
		<label>
			<?php echo JText::_('WIKI_FIELD_PAGETEXT'); ?>: 
			<span class="required"><?php echo JText::_('WIKI_REQUIRED'); ?></span>
			<?php
			ximport('Hubzero_Wiki_Editor');
			$editor =& Hubzero_Wiki_Editor::getInstance();
			echo $editor->display('pagetext', 'pagetext', $this->revision->pagetext, '', '35', '40');
			?>
		</label>
		<p class="ta-right hint">See <a class="popup 400x500" href="/topics/Help:WikiFormatting">Help: Wiki Formatting</a> for help on editing content.</p>
<?php
$mode = $params->get( 'mode', 'wiki' );
if ($this->authorized) {
	$cls = '';
	if ($mode && $mode != 'knol') {
		$cls = ' class="hide"';
	}

		$juser =& JFactory::getUser();
		if (!$this->page->id || $this->page->created_by == $juser->get('id') || $this->authorized === 'admin') {
?>
			<label>
				<?php echo JText::_('WIKI_FIELD_MODE'); ?>: <span class="required"><?php echo JText::_('WIKI_REQUIRED'); ?></span>
				<select name="params[mode]" id="params_mode">
					<option value="knol"<?php if ($mode == 'knol') { echo ' selected="selected"'; } ?>>Knowledge article with specific authors</option>
					<option value="wiki"<?php if ($mode == 'wiki') { echo ' selected="selected"'; } ?>>Wiki page anyone can edit</option>
<?php 		if ($this->authorized === 'admin') { ?>
					<option value="static"<?php if ($mode == 'static') { echo ' selected="selected"'; } ?>>Static (open layout)</option>
<?php 		} ?>
				</select>
			</label>
<?php 	} else { ?>
			<input type="hidden" name="params[mode]" value="<?php echo $mode; ?>" />
<?php 	} ?>
	
			<label<?php echo $cls; ?>>
				<?php echo JText::_('WIKI_FIELD_AUTHORS'); ?>:
				<input type="text" name="authors" id="params_authors" value="<?php echo $this->authors; ?>" />
			</label>
			<label<?php echo $cls; ?>>
				<input class="option" type="checkbox" name="params[hide_authors]" id="params_hide_authors"<?php if ($params->get( 'hide_authors' ) == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('Hide author list'); ?>
			</label>
			&nbsp;
	
			<label<?php echo $cls; ?>>
				<input class="option" type="checkbox" name="params[allow_changes]" id="params_allow_changes"<?php if ($params->get( 'allow_changes' ) == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('WIKI_FIELD_ALLOW_CHANGES'); ?>
			</label>
	
			<label<?php echo $cls; ?>>
				<input class="option" type="checkbox" name="params[allow_comments]" id="params_allow_comments"<?php if ($params->get( 'allow_comments' ) == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('WIKI_FIELD_ALLOW_COMMENTS'); ?>
			</label>
<?php } else { ?>
			<input type="hidden" name="params[mode]" value="<?php echo $mode; ?>" />
			<input type="hidden" name="params[allow_changes]" value="<?php echo ($params->get( 'allow_changes' ) == 1) ? '1' : '0'; ?>" />
			<input type="hidden" name="params[allow_comments]" value="<?php echo ($params->get( 'allow_comments' ) == 1) ? '1' : '0'; ?>" />
			<input type="hidden" name="authors" id="params_authors" value="<?php echo $this->authors; ?>" />
			<input type="hidden" name="access" value="<?php echo $this->page->access; ?>" />
<?php } ?>

			<input type="hidden" name="group" value="<?php echo $this->page->group; ?>" />

<?php if ($this->sub && $this->page->group) { ?>
			<label>
				<input class="option" type="checkbox" name="access" id="access"<?php if ($this->page->access == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('WIKI_FIELD_ACCESS'); ?>
			</label>
<?php } 
	if ($this->authorized === 'admin' || $this->authorized === 'manager') { ?>
			<label>
				<input class="option" type="checkbox" name="state" id="state"<?php if ($this->page->state == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('WIKI_FIELD_STATE'); ?>
			</label>
<?php } ?>
		</fieldset>
		<div class="clear"></div>

<?php if ($this->authorized) { ?>
		<div class="explaination">
			<p><?php echo JText::_('WIKI_FIELD_TAGS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<label>
				<?php echo JText::_('WIKI_FIELD_TAGS'); ?>:
<?php 
	JPluginHelper::importPlugin( 'hubzero' );
	$dispatcher =& JDispatcher::getInstance();
	$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->tags)) );
	if (count($tf) > 0) {
		echo $tf[0];
	} else {
		echo '<input type="text" name="tags" value="'. $this->tags .'" size="38" />';
	}
?>
				<span class="hint"><?php echo JText::_('WIKI_FIELD_TAGS_HINT'); ?></span>
			</label>
		</fieldset>
		<div class="clear"></div>
<?php } else { ?>
		<input type="hidden" name="tags" value="<?php echo $this->tags; ?>" />
<?php } ?>

		<fieldset>
			<label>
				<?php echo JText::_('WIKI_FIELD_EDIT_SUMMARY'); ?>:
				<input type="text" name="summary" value="<?php echo $this->revision->summary; ?>" size="38" />
				<span class="hint"><?php echo JText::_('WIKI_FIELD_EDIT_SUMMARY_HINT'); ?></span>
			</label>
			<input type="hidden" name="minor_edit" value="1" />
		</fieldset>

		<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
		<input type="hidden" name="pagename" value="<?php echo $this->page->pagename; ?>" />
		<input type="hidden" name="scope" value="<?php echo $this->page->scope; ?>" />
		<input type="hidden" name="pageid" value="<?php echo $this->page->id; ?>" />
		<input type="hidden" name="version" value="<?php echo $this->revision->version; ?>" />
		<input type="hidden" name="created_by" value="<?php echo $this->revision->created_by; ?>" />
		<input type="hidden" name="created" value="<?php echo $this->revision->created; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->revision->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />

<?php if ($this->sub) { ?>
		<input type="hidden" name="gid" value="<?php echo $this->page->group; ?>" />
		<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
<?php } ?>


	<p class="submit">
		<input type="submit" name="preview" value="<?php echo JText::_('PREVIEW'); ?>" /> &nbsp; 
		<input type="submit" name="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
	</p>
</form>
</div><!-- / .main section -->
