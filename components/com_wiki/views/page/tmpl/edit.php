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

/*if (JPluginHelper::isEnabled('system', 'jquery')) 
{
	Hubzero_Document::addSystemScript('jquery.sisyphus');
	$jdoc = &JFactory::getDocument();
	$jdoc->addScriptDeclaration('
	jQuery(document).ready(function(jq){
		var $ = jq;
		$("#hubForm").sisyphus({
			timeout: 5,
			customKeyPrefix: "rev' . $this->revision->id . '_"
		});
	});');
}*/

if ($this->page->id) {
	$lid = $this->page->id;
} else {
	$num = time().rand(0,10000);
	$lid = JRequest::getInt( 'lid', $num, 'post' );
}
?>
<div id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<h2><?php echo $this->escape($this->title); ?></h2>
<?php 
if ($this->page->id) {
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'authors'
	));
	$view->option = $this->option;
	$view->page   = $this->page;
	$view->task   = $this->task;
	$view->config = $this->config;
	//$view->revision = $this->revision;
	$view->display();
}
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
} else {
?>

	<ul class="sub-menu">
		<li class="page-text">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_ARTICLE'); ?></span>
			</a>
		</li>
		<li class="page-edit active">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&' . ($this->sub ? 'action' : 'task') . '=edit'); ?>">
				<span><?php echo JText::_('COM_WIKI_TAB_EDIT'); ?></span>
			</a>
		</li>
<?php //if ($this->page->pagename != 'MainPage') { ?>
		<li class="page-main">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope); ?>">
				<span><?php echo JText::_('Main Page'); ?></span>
			</a>
		</li>
<?php //} ?>
<?php //if ($this->page->pagename != 'Special:AllPages') { ?>
		<li class="page-index">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Special:AllPages'); ?>">
				<span><?php echo JText::_('Index'); ?></span>
			</a>
		</li>
<?php //} ?>
	</ul>

<?php } ?>

<div class="main section">
<?php
if ($this->page->id && !$this->config->get('access-modify')) {
	if ($this->page->params->get( 'allow_changes' ) == 1) { ?>
		<p class="warning"><?php echo JText::_('COM_WIKI_WARNING_NOT_AUTH_EDITOR_SUGGESTED'); ?></p>
<?php } else { ?>
		<p class="warning"><?php echo JText::_('COM_WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php }
}
?>

<?php if ($this->page->state == 1 && !$this->config->get('access-manage')) { ?>
	<p class="warning"><?php echo JText::_('COM_WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
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

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename); ?>" method="post" id="hubForm"<?php echo ($this->sub) ? ' class="full"' : ''; ?>>
<?php if (!$this->sub) { ?>
	<div class="explaination">
	<?php if ($this->page->id && $this->config->get('access-edit')) { ?>
		<p>To change the page name (the portion used for URLs), go <a class="page-rename" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&' . ($this->sub ? 'action' : 'task') . '=rename'); ?>">here</a>.</p>
	<?php } ?>
		<p><a class="wiki-macros image-macro" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:WikiMacros#image'); ?>">[[Image(filename.jpg)]]</a> to include an image.</p>
		<p><a class="wiki-macros file-macro" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:WikiMacros#file'); ?>">[[File(filename.pdf)]]</a> to include a file.</p>

		<div id="file-uploader" data-action="/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=upload&amp;listdir=<?php echo $lid; ?>" data-list="/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=list&amp;listdir=<?php echo $lid; ?>">
			<iframe width="100%" height="370" name="filer" id="filer" style="border:2px solid #eee;margin-top: 0;" src="index.php?option=com_wiki&amp;tmpl=component&amp;controller=media&amp;scope=<?php echo $this->page->scope; ?>&amp;pagename=<?php echo $this->page->pagename; ?>&amp;listdir=<?php echo $lid; ?>"></iframe>
		</div>
		<div id="file-uploader-list"></div>
	</div>
<?php } else { ?>
	<?php if ($this->page->id && $this->config->get('access-edit')) { ?>
		<p>To change the page name (the portion used for URLs), go <a class="page-rename" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&' . ($this->sub ? 'action' : 'task') . '=rename'); ?>">here</a>.</p>
	<?php } ?>
<?php } ?>
	<fieldset>
		<legend><?php echo JText::_('Page'); ?></legend>

		<div class="group">
		<label for="parent">
			<?php echo JText::_('Parent page'); ?>:
			<select name="scope" id="parent">
				<option value=""><?php echo JText::_('[ none ]'); ?></option>
<?php
	if ($this->tree) 
	{
		foreach ($this->tree as $item) 
		{
			if ($this->page->pagename == $item->pagename)
			{
				continue;
			}
?>
				<option value="<?php echo $this->escape(stripslashes($item->scope)); ?>"<?php if ($this->page->scope == $item->scope) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($item->scopeName)); ?></option>
<?php
		}
	}
?>
			</select>
		</label>
		
		<label for="templates">
			<?php echo JText::_('Template'); ?>:
			<select name="tplate" id="templates">
				<option value="tc"><?php echo JText::_('Select a template...'); ?></option>
<?php
$hi = array();
$templates = $this->page->getTemplates();

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
		
	<?php if ($this->config->get('access-edit')) { ?>
		<label for="title">
			<?php echo JText::_('COM_WIKI_FIELD_TITLE'); ?>:
			<span class="required"><?php echo JText::_('COM_WIKI_REQUIRED'); ?></span>
			<input type="text" name="page[title]" id="title" value="<?php echo $this->escape($this->page->title); ?>" size="38" />
		</label>
	<?php } else { ?>
		<input type="hidden" name="page[title]" id="title" value="<?php echo $this->escape($this->page->title); ?>" />
	<?php } ?>
		
		<label for="pagetext" style="position: relative;">
			<?php echo JText::_('COM_WIKI_FIELD_PAGETEXT'); ?>: 
			<span class="required"><?php echo JText::_('COM_WIKI_REQUIRED'); ?></span>
			<?php
			ximport('Hubzero_Wiki_Editor');
			$editor =& Hubzero_Wiki_Editor::getInstance();
			echo $editor->display('revision[pagetext]', 'pagetext', $this->revision->pagetext, '', '35', '40');
			?>
			<!-- <span id="pagetext-overlay"><span>Drop file here to include in page</span></span> -->
		</label>
		<p class="ta-right hint">
			See <a class="wiki-formatting popup" href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiFormatting'); ?>">Help: Wiki Formatting</a> for help on editing content.
		</p>
		
<?php if ($this->sub) { ?>
		<div class="field-wrap">
			<div class="two columns first">
				<div id="file-uploader" data-action="/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=upload&amp;listdir=<?php echo $lid; ?>" data-list="/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=list&amp;listdir=<?php echo $lid; ?>">
					<iframe width="100%" height="370" name="filer" id="filer" style="border:2px solid #eee;margin-top: 0;" src="index.php?option=com_wiki&amp;tmpl=component&amp;controller=media&amp;scope=<?php echo $this->page->scope; ?>&amp;pagename=<?php echo $this->page->pagename; ?>&amp;listdir=<?php echo $lid; ?>"></iframe>
				</div>
				<div id="file-uploader-list"></div>
			</div>
			<div class="two columns second">
				<p><a class="wiki-macros image-macro" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:WikiMacros#image'); ?>">[[Image(filename.jpg)]]</a> to include an image.</p>
				<p><a class="wiki-macros file-macro" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:WikiMacros#file'); ?>">[[File(filename.pdf)]]</a> to include a file.</p>
			</div>
			<div class="clear"></div>
		</div>
<?php } ?>
	</fieldset><div class="clear"></div>
	<fieldset>
		<legend><?php echo JText::_('Access'); ?></legend>
<?php
$mode = $this->page->params->get('mode', 'wiki');
if ($this->config->get('access-edit')) {
	$cls = '';
	if ($mode && $mode != 'knol') {
		$cls = ' class="hide"';
	}

		$juser =& JFactory::getUser();
		if (!$this->page->id || $this->page->created_by == $juser->get('id') || $this->config->get('access-manage')) { ?>
			<label for="params_mode">
				<?php echo JText::_('COM_WIKI_FIELD_MODE'); ?>: <span class="required"><?php echo JText::_('COM_WIKI_REQUIRED'); ?></span>
				<select name="params[mode]" id="params_mode">
					<option value="knol"<?php if ($mode == 'knol') { echo ' selected="selected"'; } ?>>Knowledge article with specific authors</option>
					<option value="wiki"<?php if ($mode == 'wiki') { echo ' selected="selected"'; } ?>>Wiki page anyone can edit</option>
<?php 		if ($this->config->get('access-admin')) { ?>
					<option value="static"<?php if ($mode == 'static') { echo ' selected="selected"'; } ?>>Static (open layout)</option>
<?php 		} ?>
				</select>
			</label>
<?php 	} else { ?>
			<input type="hidden" name="params[mode]" id="params_mode" value="<?php echo $mode; ?>" />
<?php 	} ?>
	
			<label<?php echo $cls; ?> for="params_authors">
					<?php echo JText::_('COM_WIKI_FIELD_AUTHORS'); ?>:
					<?php 
					JPluginHelper::importPlugin( 'hubzero' );
					$dispatcher =& JDispatcher::getInstance();
					$mc = $dispatcher->trigger(
						'onGetMultiEntry', 
						array(array(
							'members', 
							'authors', 
							'params_authors', 
							'', 
							$this->authors
						))
					);
					if (count($mc) > 0) {
						echo $mc[0];
					} else { ?>
					<input type="text" name="authors" id="params_authors" value="<?php echo $this->authors; ?>" />
					<?php } ?>
			</label>

			<label<?php echo $cls; ?>>
				<input class="option" type="checkbox" name="params[hide_authors]" id="params_hide_authors"<?php if ($this->page->params->get( 'hide_authors' ) == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('Hide author list'); ?>
			</label>
			&nbsp;
	
			<label<?php echo $cls; ?> for="params_allow_changes">
				<input class="option" type="checkbox" name="params[allow_changes]" id="params_allow_changes"<?php if ($this->page->params->get( 'allow_changes' ) == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('COM_WIKI_FIELD_ALLOW_CHANGES'); ?>
			</label>
	
			<label<?php echo $cls; ?> for="params_allow_comments">
				<input class="option" type="checkbox" name="params[allow_comments]" id="params_allow_comments"<?php if ($this->page->params->get( 'allow_comments' ) == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('COM_WIKI_FIELD_ALLOW_COMMENTS'); ?>
			</label>
<?php } else { ?>
			<input type="hidden" name="params[mode]" value="<?php echo $mode; ?>" />
			<input type="hidden" name="params[allow_changes]" value="<?php echo ($this->page->params->get( 'allow_changes' ) == 1) ? '1' : '0'; ?>" />
			<input type="hidden" name="params[allow_comments]" value="<?php echo ($this->page->params->get( 'allow_comments' ) == 1) ? '1' : '0'; ?>" />
			<input type="hidden" name="authors" id="params_authors" value="<?php echo $this->escape($this->authors); ?>" />
			<input type="hidden" name="page[access]" value="<?php echo $this->escape($this->page->access); ?>" />
<?php } ?>

			<input type="hidden" name="page[group]" value="<?php echo $this->escape($this->page->group_cn); ?>" />

<?php if ($this->config->get('access-manage')) { ?>
			<label for="state">
				<input class="option" type="checkbox" name="page[state]" id="state"<?php if ($this->page->state == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo JText::_('COM_WIKI_FIELD_STATE'); ?>
			</label>
<?php } ?>
		</fieldset>
		<div class="clear"></div>

<?php if ($this->config->get('access-edit')) { ?>
	<?php if (!$this->sub) { ?>
		<div class="explaination">
			<p><?php echo JText::_('COM_WIKI_FIELD_TAGS_EXPLANATION'); ?></p>
		</div>
	<?php } ?>
		<fieldset>
			<legend><?php echo JText::_('Metadata'); ?></legend>
			<label>
				<?php echo JText::_('COM_WIKI_FIELD_TAGS'); ?>:
				<?php 
				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->tags)) );
				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<input type="text" name="tags" value="'. $this->tags .'" size="38" />';
				}
				?>
				<span class="hint"><?php echo JText::_('COM_WIKI_FIELD_TAGS_HINT'); ?></span>
			</label>
<?php } else { ?>
		<input type="hidden" name="tags" value="<?php echo $this->escape($this->tags); ?>" />
<?php } ?>

			<label>
				<?php echo JText::_('COM_WIKI_FIELD_EDIT_SUMMARY'); ?>:
				<input type="text" name="revision[summary]" value="<?php echo $this->escape($this->revision->summary); ?>" size="38" />
				<span class="hint"><?php echo JText::_('COM_WIKI_FIELD_EDIT_SUMMARY_HINT'); ?></span>
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

<?php if ($this->sub) { ?>
		<input type="hidden" name="cn" value="<?php echo $this->page->group_cn; ?>" />
		<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
		<input type="hidden" name="action" value="save" />
<?php } else { ?>
		<input type="hidden" name="task" value="save" />
<?php } ?>

		<p class="submit">
			<input type="submit" name="preview" value="<?php echo JText::_('PREVIEW'); ?>" /> &nbsp; 
			<input type="submit" name="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
		</p>
	</form>
</div><!-- / .main section -->